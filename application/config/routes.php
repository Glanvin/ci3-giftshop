<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'HomeController';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['home'] = 'HomeController/index';

$route['auth'] = 'AuthController/index';
$route['auth/login'] = 'AuthController/login';
$route['auth/signup'] = 'AuthController/signup';
$route['auth/logout'] = 'AuthController/logout';

$route['shop'] = 'ShopController/index';
$route['shop/product/(:num)'] = 'ShopController/product/$1';
$route['shop/browse'] = 'ShopController/browse';

$route['user'] = 'UserController/dashboard';
$route['user/product/(:num)'] = 'UserController/product/$1';
$route['user/reservations'] = 'UserController/my_reservations';
$route['user/reservation/(:num)'] = 'UserController/view_reservation/$1';
$route['user/upload_receipt'] = 'UserController/upload_receipt';

$route['cart'] = 'CartController/index';
$route['cart/add'] = 'CartController/add';
$route['cart/increase/(:any)'] = 'CartController/increase/$1';
$route['cart/decrease/(:any)'] = 'CartController/decrease/$1';
$route['cart/remove/(:any)'] = 'CartController/remove/$1';
$route['cart/submit'] = 'CartController/submit';

$route['staff'] = 'StaffController/dashboard';
$route['staff/inventory'] = 'StaffController/inventory';
$route['staff/inventory/search-products'] = 'StaffController/search_stock_products';
$route['staff/product/add'] = 'StaffController/add_product';
$route['staff/product/edit/(:num)'] = 'StaffController/edit_product/$1';
$route['staff/product/delete/(:num)'] = 'StaffController/delete_product/$1';
$route['staff/reservations/(:any)'] = 'StaffController/reservations/$1';
$route['staff/reservations'] = 'StaffController/reservations';
$route['staff/reservation/edit/(:num)'] = 'StaffController/edit_reservation/$1';
$route['staff/reservation/(:num)'] = 'StaffController/view_reservation/$1';
$route['staff/reports'] = 'StaffController/reports';

// Stock In Route
$route['staff/process_stock_in'] = 'staffController/process_stock_in';

// Product CRUD Routes
$route['staff/inventory']        = 'staffController/inventory';
$route['staff/product/add']      = 'staffController/add_product';
$route['staff/product/edit/(:num)'] = 'staffController/edit_product/$1';
$route['staff/product/delete/(:num)'] = 'staffController/delete_product/$1';
