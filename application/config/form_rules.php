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
		'rules' => 'required|integer|callback_category_exists',
		'errors' => array(
			'required' => 'Category is required.'
		)
	),
	array(
		'field' => 'price',
		'label' => 'Price',
		'rules' => 'required|regex_match[/^[0-9]{1,8}(?:[.][0-9]{1,2})?$/D]|greater_than[0]',
		'errors' => array('regex_match' => 'Enter a price with up to two decimal places.', 'greater_than' => 'Price must be greater than zero.')
	),
	array(
		'field' => 'stock_quantity',
		'label' => 'Stock Quantity',
		'rules' => 'required|integer|greater_than_equal_to[0]'
	),
	array(
		'field' => 'sku',
		'label' => 'SKU',
		'rules' => 'trim|max_length[50]|callback_sku_available',
		'errors' => array('sku_available' => 'This SKU is already in use.')
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
		'rules' => 'integer|greater_than_equal_to[0]'
	)
);

// Inventory category modal. The input name remains the key used by both
// server-side feedback and the form's inline error message.
$config['giftshop_category'] = array(
	array(
		'field' => 'name',
		'label' => 'Category Name',
		'rules' => 'required|trim|max_length[100]|callback_category_name_available',
		'errors' => array('required' => 'Category name is required.', 'category_name_available' => 'This category already exists.')
	)
);

// Stock-in modal.
$config['giftshop_stock_in'] = array(
	array(
		'field' => 'product_id',
		'label' => 'Product',
		'rules' => 'required|integer|callback_stock_product_exists',
		'errors' => array('required' => 'Select a product.')
	),
	array(
		'field' => 'quantity',
		'label' => 'Quantity',
		'rules' => 'required|integer|greater_than[0]',
		'errors' => array('required' => 'Enter a quantity.', 'greater_than' => 'Quantity must be at least 1.')
	),
	array('field' => 'reference_no', 'label' => 'Reference Number', 'rules' => 'trim|max_length[100]'),
	array('field' => 'supplier', 'label' => 'Supplier', 'rules' => 'trim|max_length[150]'),
	array('field' => 'notes', 'label' => 'Notes', 'rules' => 'trim|max_length[500]')
);

// The cart reservation form has a checkbox group plus optional staff notes.
$config['giftshop_reservation'] = array(
	array('field' => 'notes', 'label' => 'Notes', 'rules' => 'trim|max_length[500]')
);

// Cart add forms use product_id, quantity and redirect as their input names.
$config['giftshop_cart_add'] = array(
	array('field' => 'product_id', 'label' => 'Product', 'rules' => 'required|integer|greater_than[0]'),
	array('field' => 'quantity', 'label' => 'Quantity', 'rules' => 'required|integer|greater_than[0]'),
	array('field' => 'redirect', 'label' => 'Return Page', 'rules' => 'trim|in_list[browse,view]')
);

$config['giftshop_reservation_status'] = array(
	array('field' => 'new_status', 'label' => 'Status', 'rules' => 'required|in_list[pending,confirmed,ready,completed,cancelled]')
);

$config['giftshop_receipt'] = array(
	array('field' => 'reservation_id', 'label' => 'Reservation', 'rules' => 'required|integer|greater_than[0]')
);
