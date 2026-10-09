<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

	// Loads the database handle used by every query in this model.
	// The query builder is the only dependency; no external libraries.
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Shared catalog query for the public shop and the logged-in browse page.
	 *
	 * Left-joins the category name, applies an optional name/description
	 * search, filters by category id, and maps the `sort` key onto an
	 * ORDER BY. Unknown sort keys fall through to ascending name.
	 *
	 * @param  string|null $search    Free-text term matched against name and description.
	 * @param  int|null    $category  Category id, or NULL/0 for all.
	 * @param  string      $sort      One of newest, price_low, price_high, za, low, high.
	 * @param  int|null    $limit     Row cap, or NULL for no LIMIT.
	 * @param  int         $offset    Rows to skip, used with$limit.
	 * @return array
	 */
	public function get_catalog($search = NULL, $category = NULL,$sort = 'newest', $limit = NULL,$offset = 0)
	{
		$this->db->select('p.*, c.name AS category_name');
		$this->db->from('products p');$this->db->join('categories c', 'p.category_id = c.id', 'left');

		if (!empty($search)) {$this->db->group_start()
				->like('p.name', $search)
				->or_like('p.description', $search)
				->group_end();
		}

		if (is_numeric($category) && (int)$category > 0) {
			$this->db->where('p.category_id', (int)$category);
		}

		switch ($sort) {
			case 'price_low': $this->db->order_by('p.price', 'ASC');  break;
			case 'price_high': $this->db->order_by('p.price', 'DESC'); break;
			case 'za':         $this->db->order_by('p.name', 'DESC');  break;
			case 'low':        $this->db->order_by('p.price', 'ASC');  break;
			case 'high':       $this->db->order_by('p.price', 'DESC'); break;
			case 'newest':     $this->db->order_by('p.created_at', 'DESC'); break;
			default:           $this->db->order_by('p.name', 'ASC');
		}

		if ($limit !== NULL) {$this->db->limit((int) $limit, (int)$offset);
		}

		return $this->db->get()->result_array();
	}

	// Row count for the catalog, matching get_catalog's search and category filters.
	// Kept separate from get_catalog so pagination can count before fetching rows.
	public function get_catalog_count($search = NULL,$category = NULL)
	{
		$this->db->from('products p');

		if (!empty($search)) {$this->db->group_start()
				->like('p.name', $search)
				->or_like('p.description', $search)
				->group_end();
		}

		if (is_numeric($category) && (int)$category > 0) {
			$this->db->where('p.category_id', (int)$category);
		}

		return $this->db->count_all_results();
	}

	// Fetches a single product with its category name, or NULL if absent.
	// $active_only defaults TRUE so the public shop hides inactive products.
	public function get_by_id($id,$active_only = TRUE)
	{
		$this->db->select('p.*, c.name AS category_name');
		$this->db->from('products p');$this->db->join('categories c', 'p.category_id = c.id', 'left');
		$this->db->where('p.id',$id);
		if ($active_only) {$this->db->where('p.status', 'active');
		}
		return $this->db->get()->row_array();
	}

	// Inserts a new product row and stamps both audit timestamps.
	// The caller supplies every column, including the already-uploaded image path.
	public function add($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');$data['updated_at'] = date('Y-m-d H:i:s');
		return $this->db->insert('products',$data);
	}

	// Updates an existing product by id and refreshes updated_at.
	// Only the keys present in $data are written, so partial updates are safe.
	public function update($id,$data)
	{
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->db->where('id',$id);
		return $this->db->update('products',$data);
	}

	// Hard-deletes a product row by id.
	// No soft delete: reservation_items may still reference the old id.
	public function delete($id)
	{
		return $this->db->delete('products', array('id' =>$id));
	}

	// Checks whether a SKU is already taken, returning a boolean.
	// $exclude_id lets the edit form keep its own SKU without a false clash.
	public function sku_exists($sku,$exclude_id = NULL)
	{
		$this->db->select('id')->where('sku',$sku);
		if ($exclude_id !== NULL) {
			$this->db->where('id !=',$exclude_id);
		}
		return $this->db->limit(1)->get('products')->num_rows() > 0;
	}

	// Returns just id and name for every product, ready for a dropdown helper.
	public function get_options_for_select()
	{
		$this->db->select('id, name')->order_by('name', 'ASC');
		return $this->db->get('products')->result_array();
	}

	// Batch lookup of products by id, keyed by id for O(1) merging.
	// Replaces a per-row get_by_id() loop when hydrating a list of items.
	public function get_by_ids(array $ids)
	{
		$ids = array_values(array_unique(array_map('intval', $ids)));
		if (empty($ids)) {
			return array();
		}

		$rows = $this->db
			->select('id, name, price, size, color, stock_quantity')
			->where_in('id', $ids)
			->get('products')
			->result_array();

		$keyed = array();
		foreach ($rows as $row) {
			$keyed[(int) $row['id']] = $row;
		}
		return $keyed;
	}

	// Counts products for the inventory filter pills, all in one query.
	// Each bucket is a boolean SUM that mirrors the matching get_by_filter WHERE.
	public function get_counts()
	{
		$row = $this->db
			->select("
				COUNT(*) AS all_count,
				SUM(status = 'active' AND stock_quantity > 5) AS active_count,
				SUM(stock_quantity > 0 AND stock_quantity <= 5) AS low_count,
				SUM(stock_quantity <= 0 OR status = 'out_of_stock') AS out_count
			", FALSE)
			->get('products')
			->row_array();

		return array(
			'all' => (int) $row['all_count'],
			'active' => (int) $row['active_count'],
			'low' => (int) $row['low_count'],
			'out' => (int) $row['out_count']
		);
	}

	/**
	 * Product list for the staff inventory screen, by stock filter.
	 *
	 * Deliberately not a status filter: `low` and `out` are stock ranges
	 * and `out` is grouped so a product matches on either zero quantity
	 * or an `out_of_stock` status. `all` applies no WHERE clause.
	 *
	 * @param  string $filter One of all, active, low, out.
	 * @return array
	 */
	public function get_by_filter($filter = 'all')
	{
		$this->db->select('p.*, c.name AS category_name');
		$this->db->from('products p');$this->db->join('categories c', 'p.category_id = c.id', 'left');

		switch ($filter) {
			case 'active':
				$this->db->where('p.status', 'active')->where('p.stock_quantity >', 5);
				break;
			case 'low':
				$this->db->where('p.stock_quantity >', 0)->where('p.stock_quantity <=', 5);
				break;
			case 'out':
				$this->db->group_start()
					->where('p.stock_quantity <=', 0)
					->or_where('p.status', 'out_of_stock')
					->group_end();
				break;
		}

		return $this->db->get()->result_array();
	}

	// ==========================================
	// ADDED METHODS FOR STOCK IN OPERATIONS
	// ==========================================

	/**
	 * Increments product stock quantity by a specific amount.
	 * Automatically sets status to 'active' if product was previously 'out_of_stock'.
	 *
	 * @param  int $product_id
	 * @param  int $quantity
	 * @return bool
	 */
	public function add_stock($product_id,$quantity)
	{
		$this->db->set('stock_quantity', 'stock_quantity + ' . (int) $quantity, FALSE);$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('id', (int)$product_id);
		return $this->db->update('products');
	}

	/**
	 * Atomic transaction helper: Increments product quantity and logs 
	 * the stock-in movement in the inventory log table.
	 *
	 * @param  int         $product_id
	 * @param  int         $quantity
	 * @param  string|null $reference_no
	 * @param  string|null $supplier
	 * @param  string|null $notes
	 * @param  int|null    $created_by Staff user ID
	 * @return bool
	 */
	public function process_stock_in($product_id, $quantity,$reference_no = NULL, $supplier = NULL, $notes = NULL, $created_by = NULL)
	{
		$this->db->trans_start();

		// 1. Update product quantity
		$this->add_stock($product_id,$quantity);

		// 2. Insert audit/movement log if inventory_logs table exists
		if ($this->db->table_exists('inventory_logs')) {$log = array(
				'product_id'   => (int) $product_id,
				'type'         => 'stock_in',
				'quantity'     => (int) $quantity,
				'reference_no' => $reference_no,
				'supplier'     => $supplier,
				'notes'        => $notes,
				'created_by'   => $created_by,
				'created_at'   => date('Y-m-d H:i:s')
			);
			$this->db->insert('inventory_logs',$log);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}