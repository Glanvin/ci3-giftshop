<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

	// Loads the database handle for the categories queries below.
	// Kept minimal: the query builder is the only dependency this model needs.
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// All categories in display order, for staff dropdowns and selects.
	// Ordered by the display_order column the admin can maintain.
	public function get_all()
	{
		$this->db->select('id, name, display_order')->order_by('display_order', 'ASC');
		return $this->db->get('categories')->result_array();
	}

	// Returns the database-backed id => name map for shop category filters.
	public function get_map()
	{
		$map = array();
		foreach ($this->get_all() as $category) {
			$map[(int) $category['id']] = $category['name'];
		}
		return $map;
	}

	public function exists($id)
	{
		return $this->db->where('id', (int) $id)->count_all_results('categories') > 0;
	}

	public function name_exists($name)
	{
		return $this->db->where('name', trim($name))->count_all_results('categories') > 0;
	}

	/**
	 * Inserts a new category into the database.
	 * Automatically sets display_order to the next available position.
	 *
	 * @param  array $data Array containing category data
	 * @return bool
	 */

	public function add($data)
	{
		if ( ! isset($data['display_order'])) {
			$data['display_order'] = $this->get_next_order();
		}

		return $this->db->insert('categories', $data);
	}
	public function delete($id)
    {
        return $this->db->delete('categories', array('id' => (int) $id));
    }

	/**
	 * Gets the next display_order integer so new categories append to the bottom.
	 *
	 * @return int
	 */
	private function get_next_order()
	{
		$this->db->select_max('display_order', 'max_order');
		$query = $this->db->get('categories')->row();
		return ($query && $query->max_order !== null) ? ((int) $query->max_order + 1) : 1;
	}
}
