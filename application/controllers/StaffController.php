<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StaffController extends CI_Controller {

	private $sku_exclude_id = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->model('Product_model');
		$this->load->model('Category_model');
		$this->load->model('Reservation_model');
		$this->load->library('upload');
		$this->config->load('form_rules');

		if ( ! $this->User_model->is_staff($this->session->userdata('role'))) {
			redirect('auth/login');
		}
	}

	public function dashboard()
	{
		$data = array(
			'title' => 'Dashboard - Staff',
			'active' => 'dashboard',
			'total' => $this->Product_model->get_counts(),
			'pending' => $this->Reservation_model->count_pending(),
			'completed' => $this->Reservation_model->count_completed(),
			'recent' => $this->Reservation_model->get_recent(5)
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('staff/dashboard', $data);
		$this->load->view('templates/footer', $data);
	}

	public function inventory()
	{
		$this->_render_inventory();
	}

	public function search_stock_products()
	{
		if ($this->input->method(TRUE) !== 'GET') {
			show_error('Method not allowed.', 405);
		}

		$query = substr(trim((string) $this->input->get('q', TRUE)), 0, 100);
		$products = $query === '' ? array() : $this->Product_model->search_for_stock_in($query);
		$results = array();
		foreach ($products as $product) {
			$results[] = array(
				'id' => (int) $product['id'],
				'name' => $product['name'],
				'sku' => (string) $product['sku'],
				'stock_quantity' => (int) $product['stock_quantity']
			);
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array('results' => $results)));
	}

	public function add_category()
	{
		$this->load->library('form_validation');
		$this->load->model('Category_model');

		$this->form_validation->set_rules('name', 'Category Name', 'trim|required|max_length[100]');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('staff/inventory');
			return;
		}

		$category_name = trim($this->input->post('name', TRUE));
		$query = $this->db->get_where('categories', array('name' => $category_name));

		if ($query->num_rows() > 0) {
			$this->session->set_flashdata('message', 'This category already exists.');
		} else {
			$this->Category_model->add(array('name' => $category_name));
			$this->session->set_flashdata('message', 'Category added successfully!');
		}

		redirect('staff/inventory');
	}

	public function add_product()
	{
		if ($this->input->method(TRUE) !== 'POST') {
			redirect('staff/inventory');
		}

		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('giftshop_product'));
		if ($this->form_validation->run() === FALSE) {
			$this->_render_inventory('addProductModal');
			return;
		}

		$upload = $this->_upload_product_image('product_' . time() . '_');
		if ($upload['error'] !== NULL) {
			$this->_render_inventory('addProductModal', $upload['error']);
			return;
		}

		$product = array(
			'name' => $this->input->post('name', TRUE),
			'category_id' => (int) $this->input->post('category_id'),
			'description' => $this->input->post('description', TRUE),
			'price' => $this->input->post('price'),
			'stock_quantity' => (int) $this->input->post('stock_quantity'),
			'sku' => $this->input->post('sku', TRUE),
			'size' => $this->input->post('size', TRUE),
			'color' => $this->input->post('color', TRUE),
			'status' => $this->input->post('status', TRUE),
			'low_stock_threshold' => $this->input->post('low_stock_threshold', TRUE) !== '' ? (int) $this->input->post('low_stock_threshold', TRUE) : 10,
			'image_url' => $upload['image_url']
		);

		// Pass current user ID for audit logging
		$product_id = $this->Product_model->add($product, $this->session->userdata('user_id'));
		if ($product_id) {
			set_notification('success', 'Product added successfully.');
		} else {
			set_notification('danger', 'Could not save the product. Please try again.');
		}
		redirect('staff/inventory');
	}

	public function process_stock_in()
	{
		if ($this->input->method(TRUE) !== 'POST') {
			redirect('staff/inventory');
		}

		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('giftshop_stock_in'));
		if ($this->form_validation->run() === FALSE) {
			$selected_product = $this->Product_model->get_by_id((int) $this->input->post('product_id', TRUE), FALSE);
			$this->_render_inventory('stockInModal', NULL, $selected_product);
			return;
		}

		$product_id = (int) $this->input->post('product_id', TRUE);
		$quantity = (int) $this->input->post('quantity', TRUE);
		$product = $this->Product_model->get_by_id($product_id, FALSE);
		$success = $this->Product_model->process_stock_in(
			$product_id,
			$quantity,
			$this->input->post('reference_no', TRUE),
			$this->input->post('supplier', TRUE),
			$this->input->post('notes', TRUE),
			$this->session->userdata('user_id')
		);
		if ($success) {
			set_notification('success', "Added {$quantity} unit(s) to {$product['name']}.");
		} else {
			set_notification('danger', 'Failed to update stock quantity. Please try again.');
		}
		redirect('staff/inventory');
	}

	public function delete_category($id)
	{
		if ($this->input->method(TRUE) !== 'POST') {
			redirect('staff/inventory');
		}

		if ($this->Category_model->delete((int) $id)) {
			set_notification('success', 'Category deleted successfully.');
		} else {
			set_notification('danger', 'Could not delete the category. Remove its products first.');
		}
		redirect('staff/inventory');
	}

	public function edit_product($id)
	{
		$this->load->helper('pricing');
		$this->sku_exclude_id = (int) $id;
		$product = $this->Product_model->get_by_id((int) $id, FALSE);

		if ( ! $product) {
			show_error('Product not found.');
		}

		$error = NULL;
		$manual_field_errors = array();
		if ($this->input->post('update')) {
			$this->form_validation->set_error_delimiters('', '');
			$rules = $this->config->item('giftshop_product');
			foreach ($rules as &$rule) {
				if ($rule['field'] === 'price') {
					$rule['label'] = 'Base Price';
					$rule['rules'] = 'required|trim|regex_match[/^[0-9]{1,8}(?:[.][0-9]{1,2})?$/D]|greater_than[0]';
					$rule['errors'] = array('regex_match' => 'Base Price must have up to two decimal places and be at most ₱99,999,999.99.', 'greater_than' => 'Base Price must be greater than zero.');
				}
			}
			unset($rule);
			$rules[] = array(
				'field' => 'markup_percent',
				'label' => 'Markup',
				'rules' => 'trim|regex_match[/^[0-9]{1,8}(?:[.][0-9]{1,2})?$/D]',
				'errors' => array('regex_match' => 'Markup must be a nonnegative percentage with up to two decimal places.')
			);
			$this->form_validation->set_rules($rules);

			$posted_price = $this->input->post('price');
			$posted_markup = $this->input->post('markup_percent');
			if (($posted_price !== NULL && ! is_string($posted_price)) || ($posted_markup !== NULL && ! is_string($posted_markup))) {
				$error = 'Base Price and Markup must each be a single numeric value.';
				$manual_field_errors['price'] = $error;
			} elseif ($this->form_validation->run() === FALSE) {
				$error = implode(' ', $this->form_validation->error_array());
			} else {
				$markup = $this->form_validation->set_value('markup_percent');
				$total_price = giftshop_price_with_markup($this->form_validation->set_value('price'), $markup === '' ? '0' : $markup);
				$sku = $this->input->post('sku', TRUE);

				if ($total_price === NULL) {
					$error = 'The selling price after markup cannot exceed ₱99,999,999.99.';
					$manual_field_errors['markup_percent'] = $error;
				} elseif ($sku && $this->Product_model->sku_exists($sku, (int) $id)) {
					$error = "Error: SKU '$sku' already exists!";
					$manual_field_errors['sku'] = $error;
				} else {
					$upload = $this->_upload_product_image('product_' . time() . '_');
					if ($upload['error'] !== NULL) {
						$error = $upload['error'];
						$manual_field_errors['product_image'] = $error;
					} else {
						$fields = array(
							'name' => $this->input->post('name', TRUE),
							'category_id' => (int) $this->input->post('category_id'),
							'description' => $this->input->post('description', TRUE),
							'price' => $total_price,
							'stock_quantity' => (int) $this->input->post('stock_quantity'),
							'sku' => $sku,
							'size' => $this->input->post('size', TRUE),
							'color' => $this->input->post('color', TRUE),
							'status' => $this->input->post('status', TRUE),
							'low_stock_threshold' => $this->input->post('low_stock_threshold', TRUE) !== '' ? (int) $this->input->post('low_stock_threshold', TRUE) : 10
						);
						if ($upload['image_url'] !== '') {
							$fields['image_url'] = $upload['image_url'];
						}

						$this->Product_model->update((int) $id, $fields);
						$this->session->set_flashdata('message', 'Product Updated Successfully');
						redirect('staff/inventory');
					}
				}
			}
		}

		$data = array(
			'title' => 'Edit Product',
			'active' => 'inventory',
			'error' => $error,
			'field_errors' => $manual_field_errors + $this->form_validation->error_array(),
			'product' => $product,
			'categories' => $this->Category_model->get_all()
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('staff/edit_product', $data);
		$this->load->view('templates/footer', $data);
	}

	public function delete_product($id)
	{
		if ($this->input->method(TRUE) !== 'POST') {
			redirect('staff/inventory');
		}

		// Pass user ID for tracking deletions
		if ($this->Product_model->delete((int) $id, $this->session->userdata('user_id'))) {
			set_notification('success', 'Product deleted.');
		} else {
			set_notification('danger', 'Could not delete the product.');
		}
		redirect('staff/inventory');
	}

	public function reservations($filter = 'all')
	{
		if ( ! in_array($filter, array_merge(array('all'), Reservation_model::STATUSES), TRUE)) {
			$filter = 'all';
		}

		$data = array(
			'title' => 'Reservations - Staff',
			'active' => 'reservations',
			'filter' => $filter,
			'counts' => $this->Reservation_model->get_status_counts(),
			'reservations' => $this->Reservation_model->get_all($filter),
			'res_message' => $this->session->flashdata('res_message')
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('staff/reservations', $data);
		$this->load->view('templates/footer', $data);
	}

	public function view_reservation($id)
	{
		$res = $this->Reservation_model->get_with_user((int)$id);

		if ( ! $res) {
			show_error('Reservation not found.');
		}

		$data = array(
			'title' => 'View Reservation',
			'active' => 'reservations',
			'res' => $res,
			'id' => (int) $id,
			'items' => $this->Reservation_model->get_items((int)$id)
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('staff/view_reservation', $data);
		$this->load->view('templates/footer', $data);
	}

	public function edit_reservation($id)
	{
		if ($this->input->post('update_status')) {
			$this->form_validation->set_rules($this->config->item('giftshop_reservation_status'));
			if ($this->form_validation->run() === TRUE) {
				$new_status = $this->input->post('new_status', TRUE);
				$this->Reservation_model->update_status((int) $id, $new_status);
				set_notification('success', 'Reservation status updated to ' . ucfirst($new_status) . '.');
			} else {
				set_notification('danger', 'Choose a valid reservation status.');
			}
			redirect('staff/reservations');
		}

		$res = $this->Reservation_model->get_with_user((int)$id);

		if ( ! $res) {
			show_error('Reservation not found.');
		}

		$data = array(
			'title' => 'Edit Reservation',
			'active' => 'reservations',
			'res' => $res
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('staff/edit_reservation', $data);
		$this->load->view('templates/footer', $data);
	}

	public function reports()
	{
		$monthly = $this->Reservation_model->monthly(6);
		$stock = $this->Reservation_model->stock_summary();

		$data = array(
			'title' => 'Reports - Staff',
			'active' => 'reports',
			'total_revenue' => $this->Reservation_model->total_revenue('completed'),
			'pending_revenue' => $this->Reservation_model->total_revenue('pending'),
			'total_res' => $this->Reservation_model->total_count(),
			'top_products' => $this->Reservation_model->top_products(5),
			'months' => $monthly['months'],
			'monthly_counts' => $monthly['counts'],
			'monthly_revenue' => $monthly['revenue'],
			'stock_ok' => $stock['ok'],
			'stock_low' => $stock['low'],
			'stock_out' => $stock['out']
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('staff/reports', $data);
		$this->load->view('templates/footer', $data);
	}

	public function callback_category_exists($category_id)
	{
		if ($this->Category_model->exists($category_id)) {
			return TRUE;
		}
		$this->form_validation->set_message('category_exists', 'Choose an available category.');
		return FALSE;
	}

	public function callback_category_name_available($name)
	{
		if ( ! $this->Category_model->name_exists($name)) {
			return TRUE;
		}
		$this->form_validation->set_message('category_name_available', 'This category already exists.');
		return FALSE;
	}

	public function callback_sku_available($sku)
	{
		if (trim((string) $sku) === '' || ! $this->Product_model->sku_exists($sku, $this->sku_exclude_id)) {
			return TRUE;
		}
		$this->form_validation->set_message('sku_available', 'This SKU is already in use.');
		return FALSE;
	}

	public function callback_stock_product_exists($product_id)
	{
		if ($this->Product_model->get_by_id((int) $product_id, FALSE)) {
			return TRUE;
		}
		$this->form_validation->set_message('stock_product_exists', 'Choose an available product.');
		return FALSE;
	}

	private function _render_inventory($open_modal = '', $upload_error = NULL, $selected_stock_product = NULL)
	{
		$filter = $this->input->get('filter', TRUE);
		if ( ! in_array($filter, array('active', 'low', 'out'), TRUE)) {
			$filter = 'all';
		}

		$data = array(
			'title' => 'Inventory - Staff',
			'active' => 'inventory',
			'filter' => $filter,
			'products' => $this->Product_model->get_by_filter($filter),
			'counts' => $this->Product_model->get_counts(),
			'categories' => $this->Category_model->get_all(),
			'inventory_logs' => $this->Product_model->get_inventory_logs(100), // <-- Added logs array
			'message' => $this->session->flashdata('message'),
			'open_modal' => $open_modal,
			'upload_error' => $upload_error,
			'selected_stock_product' => $selected_stock_product
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('staff/inventory', $data);
		$this->load->view('templates/footer', $data);
	}

	private function _upload_product_image($prefix)
	{
		if (empty($_FILES['product_image']['name'])) {
			return array('image_url' => '', 'error' => NULL);
		}

		$dir = FCPATH . 'uploads/products/';
		if ( ! is_dir($dir)) {
			if ( ! mkdir($dir, 0755, TRUE) && ! is_dir($dir)) {
				return array('image_url' => '', 'error' => 'The product upload folder could not be created.');
			}
		}

		$original = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['product_image']['name']));

		$this->upload->initialize(array(
			'upload_path' => $dir,
			'allowed_types' => 'gif|jpg|jpeg|png|webp',
			'max_size' => 5120,
			'file_name' => $prefix . $original,
			'overwrite' => FALSE
		));

		if ($this->upload->do_upload('product_image')) {
			$fdata = $this->upload->data();
			return array('image_url' => 'uploads/products/' . $fdata['file_name'], 'error' => NULL);
		}

		return array('image_url' => '', 'error' => 'Error: ' . strip_tags($this->upload->display_errors()));
	}
}