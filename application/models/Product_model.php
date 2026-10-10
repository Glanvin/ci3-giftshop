<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_catalog($search = NULL, $category = NULL, $sort = 'newest', $limit = NULL, $offset = 0)
	{
		$this->db->select('p.*, c.name AS category_name');
		$this->db->from('products p');
		$this->db->join('categories c', 'p.category_id = c.id', 'left');

		if (!empty($search)) {
			$this->db->group_start()
				->like('p.name', $search)
				->or_like('p.description', $search)
				->group_end();
		}

		if (is_numeric($category) && (int)$category > 0) {
			$this->db->where('p.category_id', (int)$category);
		}

		switch ($sort) {
			case 'price_low':  $this->db->order_by('p.price', 'ASC');  break;
			case 'price_high': $this->db->order_by('p.price', 'DESC'); break;
			case 'za':         $this->db->order_by('p.name', 'DESC');  break;
			case 'low':        $this->db->order_by('p.price', 'ASC');  break;
			case 'high':       $this->db->order_by('p.price', 'DESC'); break;
			case 'newest':     $this->db->order_by('p.created_at', 'DESC'); break;
			default:           $this->db->order_by('p.name', 'ASC');
		}

		if ($limit !== NULL) {
			$this->db->limit((int) $limit, (int)$offset);
		}

		return $this->db->get()->result_array();
	}

	public function get_catalog_count($search = NULL, $category = NULL)
	{
		$this->db->from('products p');

		if (!empty($search)) {
			$this->db->group_start()
				->like('p.name', $search)
				->or_like('p.description', $search)
				->group_end();
		}

		if (is_numeric($category) && (int)$category > 0) {
			$this->db->where('p.category_id', (int)$category);
		}

		return $this->db->count_all_results();
	}

	public function get_by_id($id, $active_only = TRUE)
	{
		$this->db->select('p.*, c.name AS category_name');
		$this->db->from('products p');
		$this->db->join('categories c', 'p.category_id = c.id', 'left');
		$this->db->where('p.id', $id);
		if ($active_only) {
			$this->db->where('p.status', 'active');
		}
		return $this->db->get()->row_array();
	}

	// Updated: Inserts a product and records an 'add_product' history log
	public function add($data, $created_by = NULL)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		
		if ($this->db->insert('products', $data)) {
			$product_id = $this->db->insert_id();

			if ($this->db->table_exists('inventory_logs')) {
				$this->db->insert('inventory_logs', array(
					'product_id'   => (int) $product_id,
					'type'         => 'add_product',
					'quantity'     => (int) $data['stock_quantity'],
					'notes'        => 'Initial stock upon product creation',
					'created_by'   => $created_by,
					'created_at'   => date('Y-m-d H:i:s')
				));
			}
			return $product_id;
		}
		return FALSE;
	}

	public function update($id, $data)
	{
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update('products', $data);
	}

	// Updated: Deletes a product and records a 'delete_product' history log
	public function delete($id, $created_by = NULL)
	{
		$product = $this->get_by_id((int) $id, FALSE);
		
		if ($product && $this->db->delete('products', array('id' => (int) $id))) {
			if ($this->db->table_exists('inventory_logs')) {
				$this->db->insert('inventory_logs', array(
					'product_id'   => (int) $id,
					'type'         => 'delete_product',
					'quantity'     => -((int) $product['stock_quantity']),
					'notes'        => 'Product "' . $product['name'] . '" deleted from inventory',
					'created_by'   => $created_by,
					'created_at'   => date('Y-m-d H:i:s')
				));
			}
			return TRUE;
		}
		return FALSE;
	}

	public function sku_exists($sku, $exclude_id = NULL)
	{
		$this->db->select('id')->where('sku', $sku);
		if ($exclude_id !== NULL) {
			$this->db->where('id !=', $exclude_id);
		}
		return $this->db->limit(1)->get('products')->num_rows() > 0;
	}

	public function get_options_for_select()
	{
		$this->db->select('id, name')->order_by('name', 'ASC');
		return $this->db->get('products')->result_array();
	}

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
			'all'    => (int) $row['all_count'],
			'active' => (int) $row['active_count'],
			'low'    => (int) $row['low_count'],
			'out'    => (int) $row['out_count']
		);
	}

	public function get_by_filter($filter = 'all')
	{
		$this->db->select('p.*, c.name AS category_name');
		$this->db->from('products p');
		$this->db->join('categories c', 'p.category_id = c.id', 'left');

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

	public function search_for_stock_in($query, $limit = 12)
	{
		$query = trim((string) $query);
		if ($query === '') {
			return array();
		}

		return $this->db
			->select('id, name, sku, stock_quantity')
			->from('products')
			->group_start()
				->like('name', $query)
				->or_like('sku', $query)
			->group_end()
			->order_by('name', 'ASC')
			->limit(max(1, min(25, (int) $limit)))
			->get()
			->result_array();
	}

	public function add_stock($product_id, $quantity)
	{
		$this->db->set('stock_quantity', 'stock_quantity + ' . (int) $quantity, FALSE);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('id', (int)$product_id);
		return $this->db->update('products');
	}

	public function process_stock_in($product_id, $quantity, $reference_no = NULL, $supplier = NULL, $notes = NULL, $created_by = NULL)
	{
		$this->db->trans_start();

		$this->add_stock($product_id, $quantity);

		if ($this->db->table_exists('inventory_logs')) {
			$log = array(
				'product_id'   => (int) $product_id,
				'type'         => 'stock_in',
				'quantity'     => (int) $quantity,
				'reference_no' => $reference_no,
				'supplier'     => $supplier,
				'notes'        => $notes,
				'created_by'   => $created_by,
				'created_at'   => date('Y-m-d H:i:s')
			);
			$this->db->insert('inventory_logs', $log);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	// New: Fetch inventory audit logs
	public function get_inventory_logs($limit = 100)
	{
		if (! $this->db->table_exists('inventory_logs')) {
			return array();
		}

		return $this->db
			->select('l.*, p.name AS fallback_product_name, u.full_name AS staff_name')
			->from('inventory_logs l')
			->join('products p', 'l.product_id = p.id', 'left')
			->join('users u', 'l.created_by = u.id', 'left')
			->order_by('l.created_at', 'DESC')
			->limit((int) $limit)
			->get()
			->result_array();
	}
}