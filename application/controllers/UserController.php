<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserController extends CI_Controller {

	// Loads the three models and cart library the student area relies on.
	// Redirects guests to login, so every action here assumes a session user.
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->model('Product_model');
		$this->load->model('Reservation_model');
		$this->load->library('cart');
		$this->config->load('form_rules');

		if ( ! $this->session->userdata('user_id')) {
			redirect('auth/login');
		}
	}

	// Student home: greets the user and summarises their reservation history.
	// Stats come from Reservation_model::user_stats so the view stays dumb.
	public function dashboard()
	{
		$user_id = (int) $this->session->userdata('user_id');
		$user_data = $this->User_model->find_by_id($user_id);

		$data = array(
			'title' => 'My Dashboard - ' . html_escape($user_data['full_name']),
			'active' => 'home',
			'user_data' => $user_data,
			'stats' => $this->Reservation_model->user_stats($user_id),
			'history' => $this->Reservation_model->get_user_reservations($user_id)
		);

		$this->load->view('templates/user_header', $data);
		$this->load->view('user/dashboard', $data);
		$this->load->view('templates/footer', $data);
	}

	// Product detail for a signed-in student, with add-to-cart enabled.
	// Unlike the public page this passes $active_only FALSE, so staff can
	// preview inactive products from the same screen.
	public function product($id)
	{
		$product = $this->Product_model->get_by_id($id, FALSE);

		if ( ! $product) {
			show_error('Product not found.');
		}

		$data = array(
			'title' => html_escape($product['name']) . ' - CU Giftshop',
			'active' => 'shop',
			'product' => $product,
			'stock' => (int) $product['stock_quantity'],
			'max_stock' => (int) $product['stock_quantity'],
			'img_url' => ShopController::img_url($product['image_url'])
		);

		$this->load->view('templates/user_header', $data);
		$this->load->view('user/product', $data);
		$this->load->view('templates/footer', $data);
	}

	// Lists every reservation belonging to the signed-in student, newest first.
	// The user_id filter is applied inside the model, not trusted from input.
	public function my_reservations()
	{
		$data = array(
			'title' => 'My Reservations - CU Giftshop',
			'active' => 'reservations',
			'reservations' => $this->Reservation_model->get_user_reservations((int) $this->session->userdata('user_id')),
			'receipt_success' => $this->session->flashdata('receipt_success'),
			'receipt_error' => $this->session->flashdata('receipt_error'),
			'receipt_open_id' => $this->session->flashdata('receipt_open_id')
		);

		$this->load->view('templates/user_header', $data);
		$this->load->view('user/my_reservations', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Renders one of the current user's reservations with its line items.
	 *
	 * The lookup is scoped by both id and `user_id`, so another student
	 * cannot read someone else's reservation. Also derives the days left
	 * before `expiry_date` and surfaces the receipt upload flashdata.
	 *
	 * @param  int    $id Reservation id from the URL.
	 * @return void
	 */
	public function view_reservation($id)
	{
		$user_id = (int) $this->session->userdata('user_id');
		$res = $this->Reservation_model->get_user_reservation((int) $id, $user_id);

		if ( ! $res) {
			show_error('Reservation not found.');
		}

		$expiry = strtotime($res['expiry_date']);
		$days_left = ceil(($expiry - time()) / 86400);

		$data = array(
			'title' => 'Reservation ' . html_escape($res['reservation_code']),
			'active' => 'reservations',
			'res' => $res,
			'items' => $this->Reservation_model->get_items((int) $res['id']),
			'expiry' => $expiry,
			'days_left' => $days_left,
			'is_expired' => ($expiry < time()),
			'receipt_success' => $this->session->flashdata('receipt_success'),
			'receipt_error' => $this->session->flashdata('receipt_error')
		);

		$this->load->view('templates/user_header', $data);
		$this->load->view('user/view_reservation', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Stores a receipt image against a reservation. POST `reservation_id` + `receipt_image`.
	 *
	 * The reservation is re-fetched scoped to the current user, and
	 * uploads are refused once the status is `completed` or `cancelled`.
	 * Files are written to `uploads/receipt/` as `receipt_<id>_<time>` with a
	 * 5 MB jpg/jpeg/png/gif cap, and only the relative path is stored.
	 *
	 * @return void
	 */
	public function upload_receipt()
	{
		$user_id = (int) $this->session->userdata('user_id');
		$this->form_validation->set_rules($this->config->item('giftshop_receipt'));
		$reservation_id = (int) $this->input->post('reservation_id');
		$return_to_list = $this->input->post('return_to', TRUE) === 'reservations';
		$redirect_to = $return_to_list ? 'user/reservations' : 'user/reservation/' . $reservation_id;

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('receipt_error', 'Choose a valid reservation before uploading.');
			redirect('user/reservations');
		}

		if (empty($_FILES['receipt_image']['name'])) {
			$this->session->set_flashdata('receipt_error', 'Receipt image is required.');
			if ($return_to_list) $this->session->set_flashdata('receipt_open_id', $reservation_id);
			redirect($redirect_to);
		}

		$res = $this->Reservation_model->get_user_reservation($reservation_id, $user_id);

		if ( ! $res) {
			$this->session->set_flashdata('receipt_error', 'Invalid reservation.');
			redirect($redirect_to);
		}

		if (in_array($res['status'], array('completed', 'cancelled'), TRUE)) {
			$this->session->set_flashdata('receipt_error', 'You cannot upload a receipt for a ' . $res['status'] . ' reservation.');
			redirect($redirect_to);
		}

		$upload_dir = FCPATH . 'uploads/receipt/';
		if ( ! is_dir($upload_dir)) {
			if ( ! mkdir($upload_dir, 0755, TRUE) && ! is_dir($upload_dir)) {
				$this->session->set_flashdata('receipt_error', 'The receipt upload folder could not be created.');
				if ($return_to_list) $this->session->set_flashdata('receipt_open_id', $reservation_id);
				redirect($redirect_to);
			}
		}

		$config = array(
			'upload_path' => $upload_dir,
			'allowed_types' => 'jpg|jpeg|png|gif',
			'max_size' => 5120,
			'file_name' => 'receipt_' . $reservation_id . '_' . time() . '_' . substr(md5(uniqid('', TRUE)), 0, 6),
			'overwrite' => FALSE
		);

		$this->load->library('upload', $config);

		if ($this->upload->do_upload('receipt_image')) {
			$fdata = $this->upload->data();
			$db_path = 'uploads/receipt/' . $fdata['file_name'];

			if ($this->Reservation_model->upload_receipt($reservation_id, $db_path)) {
				$this->session->set_flashdata('receipt_success', 'Receipt uploaded successfully!');
			} else {
				@unlink($fdata['full_path']);
				$this->session->set_flashdata('receipt_error', 'Could not save the receipt. Please try again.');
				if ($return_to_list) $this->session->set_flashdata('receipt_open_id', $reservation_id);
			}
		} else {
			$this->session->set_flashdata('receipt_error', strip_tags($this->upload->display_errors()));
			if ($return_to_list) $this->session->set_flashdata('receipt_open_id', $reservation_id);
		}

		redirect($redirect_to);
	}
}
