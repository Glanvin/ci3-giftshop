<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

	// Loads the database handle used by every user lookup below.
	// The query builder is the only dependency in this model.
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Inserts a new user and stamps created_at.
	// Uniqueness of email is left to the signup form's is_unique rule.
	public function register($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		return $this->db->insert('users', $data);
	}

	// Looks a user up by email for the login flow, returning a row or NULL.
	// Callers must still verify the password and status themselves.
	public function find_by_email($email)
	{
		$this->db->where('email', $email);
		return $this->db->get('users')->row_array();
	}

	// Fetches a user row by primary key, or NULL when the id is unknown.
	// This is the session user lookup for the student dashboard; the
	// password hash is deliberately left out of the selected columns.
	public function find_by_id($id)
	{
		$this->db->select('id, full_name, email, role, phone, student_id, department, status, last_login, created_at');
		return $this->db->get_where('users', array('id' => $id))->row_array();
	}

	// Records a successful sign-in by writing the current timestamp.
	// Called only after the password and status checks have already passed.
	public function update_last_login($id)
	{
		$this->db->where('id', $id);
		return $this->db->update('users', array('last_login' => date('Y-m-d H:i:s')));
	}

	// Single source of truth for "may this role use the staff area".
	// Both staff and admin pass; every other role, including NULL, does not.
	public function is_staff($role)
	{
		return in_array($role, array('staff', 'admin'), TRUE);
	}
}