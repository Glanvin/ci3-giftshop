<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

	// Loads the model, string helper and form rule set every auth action needs.
	// No session guard here: login and signup must stay reachable to guests.
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->helper('string');
		$this->config->load('form_rules');
	}

	// Entry point for `/auth`: sends signed-in users to their own area.
	// Everyone else falls through to the login form instead of a view.
	public function index()
	{
		if ($this->session->userdata('user_id')) {
			redirect($this->staff_redirect_target());
		}

		$this->login();
	}

	// Guard for the student area: guests are sent to the login form.
	// Signed-in students pass through untouched, so it is safe to call inline.
	public function home_redirect_target()
	{
		if ( ! $this->session->userdata('user_id')) {
			redirect('auth/login');
		}
	}

	/**
	 * Renders the sign-in form and processes the login POST.
	 *
	 * Credentials are compared as plain text to match the legacy `users`
	 * table, and the account must be `active`. On success the user record
	 * is copied into the session, `last_login` is stamped, and the visitor
	 * is sent to their area (staff/admin to `staff`, everyone else to
	 * `user`) unless a guest `post_login_redirect` intent is pending.
	 *
	 * @return void
	 */
	public function login()
	{
		if ($this->session->userdata('user_id')) {
			redirect($this->staff_redirect_target());
		}

		$this->form_validation->set_rules($this->config->item('giftshop_signin'));
		$this->form_validation->set_error_delimiters('', '');

		$data = array(
			'title' => 'Sign In - CU Giftshop'
		);

		$valid = $this->form_validation->run();

		if ($valid === TRUE) {
			$email = $this->input->post('email', TRUE);
			$password = (string) $this->input->post('password', TRUE);

			$user = $this->User_model->find_by_email($email);

			if ( ! $user || (string) $user['password'] !== $password || $user['status'] !== 'active') {
				set_notification('danger', 'Wrong email or password. Please try again.');
			} else {
				$this->session->set_userdata(array(
					'user_id' => $user['id'],
					'email' => $user['email'],
					'full_name' => $user['full_name'],
					'role' => $user['role']
				));
				$this->User_model->update_last_login($user['id']);

				$after_login = $this->session->userdata('post_login_redirect');
				$this->session->unset_userdata('post_login_redirect');

				if ( ! $this->User_model->is_staff($user['role']) && $after_login) {
					redirect($after_login);
				}

				redirect($this->User_model->is_staff($user['role']) ? 'staff' : 'user');
			}
		} elseif ($this->input->method(TRUE) === 'POST') {
			set_notification('danger', 'Please correct the highlighted fields.');
		}

		$this->load->view('templates/auth_header', $data);
		$this->load->view('auth/login', $data);
		$this->load->view('templates/auth_footer');
	}

	/**
	 * Renders the registration form and creates a new user account.
	 *
	 * `role` is read from the POST and forced to `student` unless it is
	 * explicitly `staff`. The `student_id` and `department` fields are
	 * only persisted for students. Validation failures repopulate the
	 * form; a failed insert sets `db_error` instead of redirecting.
	 *
	 * @return void
	 */
	public function signup()
	{
		if ($this->session->userdata('user_id')) {
			redirect($this->staff_redirect_target());
		}

		// default to student like the original app
		$role = $this->input->post('role', TRUE);
		if ( ! in_array($role, array('student', 'staff'), TRUE)) {
			$role = 'student';
		}

		$this->form_validation->set_rules($this->config->item('giftshop_signup'));
		$this->form_validation->set_error_delimiters('', '');

		$data = array(
			'title' => 'Sign Up - CU Giftshop',
			'selected_role' => $role
		);

		$valid = $this->form_validation->run();

		if ($valid === TRUE) {
			$is_staff = ($role === 'staff');

			$new_user = array(
				'full_name' => $this->input->post('full_name', TRUE),
				'email' => $this->input->post('email', TRUE),
				'password' => $this->input->post('password', TRUE),
				'role' => $role,
				'phone' => $this->input->post('phone', TRUE),
				'student_id' => $is_staff ? '' : $this->input->post('student_id', TRUE),
				'department' => $is_staff ? '' : $this->input->post('department', TRUE),
				'status' => 'active'
			);

			if ($this->User_model->register($new_user)) {
				set_notification('success', 'Account created successfully. You can now sign in.');
				redirect('auth/login');
			} else {
				set_notification('danger', 'Something went wrong. Please try again.');
			}
		} elseif ($this->input->method(TRUE) === 'POST') {
			set_notification('danger', 'Please correct the highlighted fields.');
		}

		$this->load->view('templates/auth_header', $data);
		$this->load->view('auth/signup', $data);
		$this->load->view('templates/auth_footer');
	}

	// Destroys the whole session, so every cached userdata key goes with it.
	// Redirects to the site root, which routes to HomeController.
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('');
	}

	/* ------------------------------------------------------------------ */
	/*  Helpers                                                            */
	/* ------------------------------------------------------------------ */

	// Maps the session role onto a landing path for staff/admin and students.
	// Read from session rather than passed in, so callers cannot get it wrong.
	private function staff_redirect_target()
	{
		$role = $this->session->userdata('role');
		return $this->User_model->is_staff($role) ? 'staff' : 'user';
	}
}