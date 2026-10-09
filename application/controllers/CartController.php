<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CartController extends CI_Controller {

	// Loads the cart library plus the models needed to price reservations.
	// Requires a login, and stashes a guest's cart intent before redirecting.
	public function __construct()
	{
		parent::__construct();
		$this->load->library('cart');
		$this->load->model('Product_model');
		$this->load->model('Reservation_model');

		// Product names may contain characters outside the default whitelist.
		$this->cart->product_name_safe = FALSE;

		if ( ! $this->session->userdata('user_id')) {
			// Preserve guest intent so they return to the product/browse page after login.
			if ($this->uri->segment(1) === 'cart' && (int) $this->input->post('product_id')) {
				$this->session->set_userdata('post_login_redirect',
					$this->input->post('redirect') === 'browse'
						? 'shop/browse'
						: 'user/product/' . (int) $this->input->post('product_id'));
			}
			redirect('auth/login');
		}
	}

	/**
	 * Renders the shopping cart with per-line product details.
	 *
	 * Merges each CI3 cart row with a fresh product lookup so the view can
	 * show current `size`, `color` and remaining `stock_quantity` for the
	 * `max_qty` input. Expiry and receipt flashdata are passed through for
	 * the success/error banner.
	 *
	 * @return void
	 */
	public function index()
	{
		$cart = $this->cart->contents();

		// One batch lookup for every cart line instead of a query per row.
		$product_ids = array();
		foreach ($cart as $item) {
			$product_ids[] = (int) $item['id'];
		}
		$products = $this->Product_model->get_by_ids($product_ids);

		$items = array();
		foreach ($cart as $item) {
			$product = isset($products[(int) $item['id']]) ? $products[(int) $item['id']] : NULL;

			$items[] = array(
				'rowid' => $item['rowid'],
				'id' => (int) $item['id'],
				'name' => $item['name'],
				'price' => (float) $item['price'],
				'qty' => (int) $item['qty'],
				'subtotal' => (float) $item['subtotal'],
				'image_url' => isset($item['options']['image_url']) ? $item['options']['image_url'] : '',
				'size' => $product ? $product['size'] : '',
				'color' => $product ? $product['color'] : '',
				'max_qty' => $product ? (int) $product['stock_quantity'] : (int) $item['qty']
			);
		}

		$data = array(
			'title' => 'Shopping Cart - CU Giftshop',
			'items' => $items,
			'total' => (float) $this->cart->total(),
			'total_items' => (int) $this->cart->total_items(),
			'error' => $this->session->flashdata('reservation_error'),
			'reservation_success' => $this->session->flashdata('reservation_success'),
			'reservation_code' => $this->session->flashdata('reservation_code')
		);

		$this->load->view('templates/user_header', $data);
		$this->load->view('user/cart', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Adds a product to the cart. POST `product_id` plus optional `quantity`.
	 *
	 * Quantity is clamped to a minimum of 1 and the product must be in
	 * stock, otherwise the line is skipped silently. `image_url` is stored
	 * as a cart option so the cart view can show the thumbnail. The
	 * `redirect` field selects the destination: `browse` goes back to the
	 * catalog, anything else to that product's detail page.
	 *
	 * @return void
	 */
	public function add()
	{
		$product_id = (int) $this->input->post('product_id');
		$qty = max(1, (int) $this->input->post('quantity', TRUE));

		$product = $this->Product_model->get_by_id($product_id, FALSE);

		if ($product && (int) $product['stock_quantity'] > 0) {
			$this->cart->insert(array(
				'id' => (string) $product['id'],
				'qty' => $qty,
				'price' => (float) $product['price'],
				'name' => $product['name'],
				'options' => array(
					'image_url' => str_replace('Product-Images', 'product-images', (string) $product['image_url'])
				)
			));

			$this->session->set_flashdata('cart_toast', $product['name']);
		}

		$dest = $this->input->post('redirect');
		$map = array(
			'browse' => 'shop/browse',
			'view' => $product ? 'user/product/' . (int) $product_id : 'shop/browse'
		);

		redirect(isset($map[$dest]) ? $map[$dest] : 'shop/browse');
	}

	// Increments a cart line by one, addressed by its CI3 rowid.
	// Does not re-check stock here; the cart max_qty input is the guard.
	public function increase($rowid)
	{
		$qty = (int) $this->cart->get_item($rowid)['qty'];
		$this->cart->update(array('rowid' => $rowid, 'qty' => $qty + 1));
		redirect('cart');
	}

	// Decrements a cart line by one, but never below a quantity of one.
	// Dropping the last unit writes qty 0, which is how CI3 removes a row.
	public function decrease($rowid)
	{
		$item = $this->cart->get_item($rowid);
		if ($item && (int) $item['qty'] > 1) {
			$this->cart->update(array('rowid' => $rowid, 'qty' => (int) $item['qty'] - 1));
		} else {
			$this->cart->update(array('rowid' => $rowid, 'qty' => 0));
		}
		redirect('cart');
	}

	// Removes a cart line outright by writing qty 0 for that rowid.
	// The CI3 cart has no delete method, so this is the documented removal.
	public function remove($rowid)
	{
		$this->cart->update(array('rowid' => $rowid, 'qty' => 0));
		redirect('cart');
	}

	/**
	 * Turns the checked cart lines into a reservation. POST `selected_items[]` + `notes`.
	 *
	 * Filters the cart down to the submitted rowids and recomputes the
	 * total from the stored prices. The reservation header, its items and
	 * the matching stock decrements run inside a single transaction; each
	 * reserved line is dropped from the cart afterwards. An empty
	 * selection or a failed transaction sets `reservation_error` and
	 * redirects back to the cart, otherwise `reservation_success` and the
	 * new `reservation_code` are flashed.
	 *
	 * @return void
	 */
	public function submit()
	{
		$selected = array_filter((array) $this->input->post('selected_items', TRUE));
		if (empty($selected)) {
			$this->session->set_flashdata('reservation_error', 'Please check at least one item to reserve.');
			redirect('cart');
		}

		$cart = $this->cart->contents();
		$notes = (string) $this->input->post('notes', TRUE);
		$user_id = (int) $this->session->userdata('user_id');

		// pick the checked items out of the current cart
		$items = array();
		$total = 0.0;
		foreach ($cart as $item) {
			if (in_array($item['rowid'], $selected, TRUE)) {
				$items[$item['rowid']] = $item;
				$total += (float) $item['price'] * (int) $item['qty'];
			}
		}

		if (empty($items)) {
			$this->session->set_flashdata('reservation_error', 'Please check at least one item to reserve.');
			redirect('cart');
		}

		$res_code = 'RES' . date('Ymd') . rand(1000, 9999);
		$expiry = date('Y-m-d H:i:s', strtotime('+7 days'));

		$this->db->trans_start();

		$reservation_id = $this->Reservation_model->create($user_id, $res_code, $total, $notes, $expiry);

		foreach ($items as $rowid => $item) {
			$this->Reservation_model->add_item($reservation_id, (int) $item['id'], (int) $item['qty'], (float) $item['price']);
			$this->Reservation_model->decrement_stock((int) $item['id'], (int) $item['qty']);
			$this->cart->update(array('rowid' => $rowid, 'qty' => 0)); // drop the reserved item from the cart
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('reservation_error', 'Could not place your reservation. Please try again.');
		} else {
			$this->session->set_flashdata('reservation_success', TRUE);
			$this->session->set_flashdata('reservation_code', $res_code);
		}

		redirect('cart');
	}
}