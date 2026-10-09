<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ShopController extends CI_Controller {

	// Loads the product and category models used by every shop action.
	// No session guard: the catalog is public, `browse` guards itself.
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->model('Category_model');
	}

	// Public catalog; no login required, so the view prompts "Log In to Reserve".
	// Thin wrapper that hands off to render_catalog with the public header.
	public function index()
	{
		return $this->render_catalog('shop/index', 'shop');
	}

	// Public product detail; no login required and the product must be active.
	// get_by_id is called with $active_only TRUE, so hidden products 404.
	public function product($id)
	{
		$product = $this->Product_model->get_by_id($id, TRUE);

		if ( ! $product) {
			show_error('Product not found.');
		}

		$data = array(
			'title' => html_escape($product['name']) . ' - CU Giftshop',
			'active' => 'shop',
			'product' => $product,
			'stock' => (int) $product['stock_quantity'],
			'img_url' => self::img_url($product['image_url'])
		);

		$this->load->view('templates/public_header', $data);
		$this->load->view('shop/product', $data);
		$this->load->view('templates/footer', $data);
	}

	// Same catalog as the public index, but behind a login and with add-to-cart.
	// Uses the user header, which renders the shared flash notification toast.
	public function browse()
	{
		if ( ! $this->session->userdata('user_id')) {
			redirect('auth/login');
		}

		$data = $this->catalog_data('shop');
		$data['title'] = 'Shop - CU Giftshop';

		$this->load->view('templates/user_header', $data);
		$this->load->view('shop/browse', $data);
		$this->load->view('templates/footer', $data);
	}

	/* ------------------------------------------------------------------ */
	/*  Helpers                                                            */
	/* ------------------------------------------------------------------ */

	// Renders any catalog variant once the data has been assembled.
	// Takes the view name so `index` and `browse` can share this shell.
	private function render_catalog($view, $active)
	{
		$data = $this->catalog_data($active);
		$data['title'] = 'Shop - CU Giftshop';

		$this->load->view('templates/public_header', $data);
		$this->load->view($view, $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Builds the shared view data for the paginated product catalog.
	 *
	 * Reads `search`, `category`, `sort` and `page` from the query string,
	 * validates `sort` against the allowed set (falling back to `newest`),
	 * clamps the page to the available range, and returns the product rows
	 * alongside the filter state the views need to rebuild the query
	 * string. Shared by the public `index` and the logged-in `browse`.
	 *
	 * @param  string $active Nav key to highlight in the header.
	 * @return array  Catalog rows plus filter, sort and pagination state.
	 */
	private function catalog_data($active)
	{
		$per_page = 9;

		$search = substr(trim((string) $this->input->get('search', TRUE)), 0, 100);
		$category = is_numeric($this->input->get('category', TRUE)) ? (int) $this->input->get('category', TRUE) : 0;
		$category_map = $this->Category_model->get_map();
		if ($category && ! array_key_exists($category, $category_map)) {
			$category = 0;
		}
		$sort = $this->input->get('sort', TRUE);
		$page = max(1, (int) $this->input->get('page', TRUE));

		if ( ! in_array($sort, array('newest', 'price_low', 'price_high'), TRUE)) {
			$sort = 'newest';
		}

		$total = (int) $this->Product_model->get_catalog_count($search, $category);
		$total_pages = max(1, (int) ceil($total / $per_page));
		if ($page > $total_pages) {
			$page = $total_pages;
		}

		return array(
			'active' => $active,
			'products' => $this->Product_model->get_catalog($search, $category, $sort, $per_page, ($page - 1) * $per_page),
			'total' => $total,
			'search' => $search,
			'category' => $category,
			'category_map' => $category_map,
			'sort' => $sort,
			'page' => $page,
			'total_pages' => $total_pages
		);
	}

	// Normalizes legacy image paths to the current uploads folder.
	// Static because views need it too, and it is a pure string transform.
	public static function img_url($path)
	{
		return giftshop_product_image_path($path);
	}
}
