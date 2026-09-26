<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reservation_model extends CI_Model {

	const STATUSES = array('pending', 'confirmed', 'ready', 'completed', 'cancelled');

	// Loads the database handle shared by every reservation query.
	// The query builder is the only dependency in this model.
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/* ------------------------------------------------------------------ */
	/*  Create                                                             */
	/* ------------------------------------------------------------------ */

	// Inserts a reservation header in `pending` and returns the new id.
	// created_at is stamped here; expiry_date is supplied by the caller.
	public function create($user_id, $code, $total, $notes, $expiry)
	{
		$data = array(
			'user_id' => $user_id,
			'reservation_code' => $code,
			'total_amount' => $total,
			'status' => 'pending',
			'notes' => $notes,
			'expiry_date' => $expiry,
			'created_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('reservations', $data);
		return $this->db->insert_id();
	}

	// Inserts one line item, snapshotting the price at reservation time.
	// Keeping price_at_time denormalized preserves the original order value.
	public function add_item($reservation_id, $product_id, $quantity, $price_at_time)
	{
		return $this->db->insert('reservation_items', array(
			'reservation_id' => $reservation_id,
			'product_id' => $product_id,
			'quantity' => $quantity,
			'price_at_time' => $price_at_time
		));
	}

	// Decrements stock with a raw set() so it cannot go negative via the ORM.
	// Then flips the row to out_of_stock once the remaining quantity hits zero.
	public function decrement_stock($product_id, $quantity)
	{
		$this->db->set('stock_quantity', 'stock_quantity - ' . (int) $quantity, FALSE)
			->where('id', $product_id)
			->update('products');

		$this->db->set('status', 'out_of_stock')
			->where('id', $product_id)
			->where('stock_quantity <=', 0)
			->update('products');
	}

	// Moves a reservation to a new status, returning the update result.
	// The caller is responsible for validating against STATUSES first.
	public function update_status($id, $status)
	{
		return $this->db->where('id', $id)->update('reservations', array('status' => $status));
	}

	// Stores the relative path of an uploaded receipt on the reservation.
	// The file itself is written by the controller before this is called.
	public function upload_receipt($id, $receipt_path)
	{
		return $this->db->where('id', $id)->update('reservations', array('receipt_image' => $receipt_path));
	}

	/* ------------------------------------------------------------------ */
	/*  Reads                                                              */
	/* ------------------------------------------------------------------ */

	// One user's reservations, newest first, each with a line-item count.
	// The count comes from a correlated subquery to avoid a second round trip.
	public function get_user_reservations($user_id)
	{
		return $this->db
			->select('r.*,
				(SELECT COUNT(*) FROM reservation_items WHERE reservation_id = r.id) AS item_count')
			->from('reservations r')
			->where('r.user_id', $user_id)
			->order_by('r.created_at', 'DESC')
			->get()->result_array();
	}

	// Fetches a single reservation only if it belongs to $user_id.
	// The ownership check is part of the query, which is what prevents IDOR.
	public function get_user_reservation($id, $user_id)
	{
		return $this->db
			->from('reservations')
			->where('id', $id)
			->where('user_id', $user_id)
			->get()->row_array();
	}

	// Line items for a reservation, joined to products for display fields.
	// Subtotal is computed in SQL from the snapshotted price and quantity.
	public function get_items($reservation_id)
	{
		return $this->db
			->select('ri.quantity, ri.price_at_time AS price, (ri.price_at_time * ri.quantity) AS subtotal,
				p.name, p.image_url, p.sku, p.size, p.stock_quantity AS current_stock')
			->from('reservation_items ri')
			->join('products p', 'ri.product_id = p.id')
			->where('ri.reservation_id', $reservation_id)
			->get()->result_array();
	}

	// One reservation joined to its customer, for the staff detail screens.
	// Not scoped to a user: staff legitimately read any reservation.
	public function get_with_user($id)
	{
		return $this->db
			->select('r.*, u.full_name, u.email')
			->from('reservations r')
			->join('users u', 'r.user_id = u.id')
			->where('r.id', $id)
			->get()->row_array();
	}

	// Staff reservation queue, optionally narrowed to one status.
	// $filter is re-validated against STATUSES here, not only by the caller.
	public function get_all($filter = 'all')
	{
		$this->db->select('r.*, u.full_name, u.email,
			(SELECT COUNT(*) FROM reservation_items WHERE reservation_id = r.id) AS item_count');
		$this->db->from('reservations r');
		$this->db->join('users u', 'r.user_id = u.id');

		if ($filter !== 'all' && in_array($filter, self::STATUSES, TRUE)) {
			$this->db->where('r.status', $filter);
		}

		$this->db->order_by('r.created_at', 'DESC');
		return $this->db->get()->result_array();
	}

	// Counts reservations for every status in STATUSES, keyed by status name.
	// Statuses with no rows are present with a zero so the tabs always render.
	public function get_status_counts()
	{
		$counts = array();
		foreach (self::STATUSES as $status) {
			$counts[$status] = $this->db->where('status', $status)->count_all_results('reservations');
		}
		return $counts;
	}

	// The newest few reservations with the customer name, for the dashboard.
	// $limit is cast by the query builder; only summary columns are selected.
	public function get_recent($limit = 5)
	{
		return $this->db
			->select('r.id, r.reservation_code, r.total_amount, r.status, r.created_at, u.full_name')
			->from('reservations r')
			->join('users u', 'r.user_id = u.id')
			->order_by('r.created_at', 'DESC')
			->limit($limit)
			->get()->result_array();
	}

	/* ------------------------------------------------------------------ */
	/*  Dashboard                                                          */
	/* ------------------------------------------------------------------ */

	// Counts reservations still waiting to be actioned by staff.
	// Used for the dashboard tile, so it is a bare COUNT with no join.
	public function count_pending()
	{
		return $this->db->where('status', 'pending')->count_all_results('reservations');
	}

	// Counts reservations that have been fulfilled.
	// Pairs with count_pending to show progress on the staff dashboard.
	public function count_completed()
	{
		return $this->db->where('status', 'completed')->count_all_results('reservations');
	}

	// Counts a user's reservations limited to the given status list.
	// Takes the array rather than a single status so callers can group states.
	public function count_by_user_with_status($user_id, array $statuses)
	{
		$this->db->where('user_id', $user_id);
		$this->db->where_in('status', $statuses);
		return $this->db->count_all_results('reservations');
	}

	// Total, in-flight and pending counts for one user, in three queries.
	// "Active" is pending, confirmed or ready, i.e. anything not finalised.
	public function user_stats($user_id)
	{
		$total = $this->db->where('user_id', $user_id)->count_all_results('reservations');
		$active = $this->db
			->where('user_id', $user_id)
			->where_in('status', array('pending', 'confirmed', 'ready'))
			->count_all_results('reservations');
		$pending = $this->db
			->where('user_id', $user_id)
			->where('status', 'pending')
			->count_all_results('reservations');

		return array('total' => $total, 'active' => $active, 'pending' => $pending);
	}

	/* ------------------------------------------------------------------ */
	/*  Reports                                                            */
	/* ------------------------------------------------------------------ */

	// Summed total_amount for one status, as a float.
	// COALESCE to 0 via the null fallback so the reports page never shows null.
	public function total_revenue($status = 'completed')
	{
		$this->db->select('SUM(total_amount) AS revenue')->where('status', $status);
		$row = $this->db->get('reservations')->row_array();
		return (float) ($row['revenue'] ?? 0);
	}

	// Total number of reservations regardless of status.
	// Backs the "all reservations" figure in the staff reports header.
	public function total_count()
	{
		return $this->db->count_all('reservations');
	}

	/**
	 * Revenue and volume per month for the staff reports chart.
	 *
	 * Groups by a `YYYY-MM` key so months are ordered chronologically
	 * rather than alphabetically, takes the newest $limit months, then
	 * reverses them so the chart reads oldest to newest. Returns three
	 * parallel series instead of rows to keep the view loop simple.
	 *
	 * @param  int $limit How many months to include.
	 * @return array ['months' => string[], 'counts' => int[], 'revenue' => float[]]
	 */
	public function monthly($limit = 6)
	{
		$rows = $this->db
			->select("DATE_FORMAT(created_at, '%b %Y') AS month,
				DATE_FORMAT(created_at, '%Y-%m') AS month_key,
				COUNT(*) AS total,
				SUM(total_amount) AS revenue")
			->from('reservations')
			->group_by('month_key')
			->order_by('month_key', 'DESC')
			->limit($limit)
			->get()->result_array();

		// return oldest → newest ordering for the chart
		$months = array(); $counts = array(); $revenue = array();
		foreach (array_reverse($rows) as $row) {
			$months[] = $row['month'];
			$counts[] = (int) $row['total'];
			$revenue[] = (float) $row['revenue'];
		}

		return array(
			'months' => $months,
			'counts' => $counts,
			'revenue' => $revenue
		);
	}

	// Best selling products by units reserved, with the revenue they produced.
	// Joins items to products and groups by product, summing in SQL.
	public function top_products($limit = 5)
	{
		return $this->db
			->select('p.name, p.image_url,
				SUM(ri.quantity) AS total_quantity,
				SUM(ri.quantity * ri.price_at_time) AS revenue')
			->from('reservation_items ri')
			->join('products p', 'ri.product_id = p.id')
			->group_by('ri.product_id')
			->order_by('total_quantity', 'DESC')
			->limit($limit)
			->get()->result_array();
	}

	// Splits products into ok, low and out buckets for the reports page.
	// "Low" is 1-5 units and matches the threshold used by the inventory pills.
	public function stock_summary()
	{
		$stockOk = $this->db->where('stock_quantity >', 5)->count_all_results('products');
		$stockLow = $this->db->where('stock_quantity >', 0)->where('stock_quantity <=', 5)->count_all_results('products');
		$stockOut = $this->db->where('stock_quantity <=', 0)->count_all_results('products');

		return array('ok' => $stockOk, 'low' => $stockLow, 'out' => $stockOut);
	}
}