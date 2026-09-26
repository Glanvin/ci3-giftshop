<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

	// Static map used by the public/user shop filter dropdown.
	const STATIC_MAP = array(
		1 => 'Textbook',
		2 => 'Uniform',
		3 => 'PE Uniform',
		4 => 'Merchandise'
	);

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
		$this->db->order_by('display_order', 'ASC');
		return $this->db->get('categories')->result_array();
	}

	// Returns the hardcoded id => name map for the public filter dropdown.
	// Static so the shop view can label products without another query.
	public function get_map()
	{
		return self::STATIC_MAP;
	}
}