<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//Sign In
$config['giftshop_signin'] = array(
	array(
		'field' => 'email',
		'label' => 'Email Address',
		'rules' => 'required|trim|valid_email',
		'errors' => array(
			'required' => 'Email is required.',
			'valid_email' => 'Invalid email format.'
		)
	),
	array(
		'field' => 'password',
		'label' => 'Password',
		'rules' => 'required',
		'errors' => array(
			'required' => 'Password is required.'
		)
	)
);

//Sign Up
$config['giftshop_signup'] = array(
	array(
		'field' => 'full_name',
		'label' => 'Full Name',
		'rules' => 'required|trim|regex_match[/^[a-zA-Z\s\-]+$/]',
		'errors' => array(
			'required' => 'Full name is required.',
			'regex_match' => 'Letters, spaces and dashes only.'
		)
	),
	array(
		'field' => 'email',
		'label' => 'Email Address',
		'rules' => 'required|trim|valid_email|is_unique[users.email]',
		'errors' => array(
			'required' => 'Email is required.',
			'valid_email' => 'Invalid email format.',
			'is_unique' => 'Email already registered.'
		)
	),
	array(
		'field' => 'password',
		'label' => 'Password',
		'rules' => 'required|min_length[6]',
		'errors' => array(
			'required' => 'Password is required.',
			'min_length' => 'Minimum 6 characters.'
		)
	),
	array(
		'field' => 'confirm_password',
		'label' => 'Confirm Password',
		'rules' => 'required|matches[password]',
		'errors' => array(
			'required' => 'Please confirm password.',
			'matches' => 'Passwords do not match.'
		)
	),
	array(
		'field' => 'phone',
		'label' => 'Phone Number',
		'rules' => 'trim|regex_match[/^[0-9+\-\s]*$/]',
		'errors' => array(
			'regex_match' => 'Numbers only.'
		)
	),
	array(
		'field' => 'student_id',
		'label' => 'Student ID',
		'rules' => 'trim'
	),
	array(
		'field' => 'department',
		'label' => 'Department',
		'rules' => 'trim'
	)
);

//Product add / update
$config['giftshop_product'] = array(
	array(
		'field' => 'name',
		'label' => 'Product Name',
		'rules' => 'required|trim|max_length[200]',
		'errors' => array(
			'required' => 'Product name is required.'
		)
	),
	array(
		'field' => 'category_id',
		'label' => 'Category',
		'rules' => 'required|numeric',
		'errors' => array(
			'required' => 'Category is required.'
		)
	),
	array(
		'field' => 'price',
		'label' => 'Price',
		'rules' => 'required|numeric'
	),
	array(
		'field' => 'stock_quantity',
		'label' => 'Stock Quantity',
		'rules' => 'numeric'
	),
	array(
		'field' => 'sku',
		'label' => 'SKU',
		'rules' => 'trim|max_length[50]'
	),
	array(
		'field' => 'size',
		'label' => 'Size',
		'rules' => 'trim|max_length[50]'
	),
	array(
		'field' => 'color',
		'label' => 'Color',
		'rules' => 'trim|max_length[50]'
	),
	array(
		'field' => 'status',
		'label' => 'Status',
		'rules' => 'required|in_list[active,inactive,out_of_stock]'
	),
	array(
		'field' => 'description',
		'label' => 'Description',
		'rules' => 'trim'
	),
	array(
		'field' => 'low_stock_threshold',
		'label' => 'Low Stock Threshold',
		'rules' => 'numeric'
	)
);