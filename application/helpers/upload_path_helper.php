<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Convert legacy product image paths to the current uploads folder. */
function giftshop_product_image_path($path)
{
	$path = str_replace('\\', '/', trim((string) $path));
	return preg_replace('#^(?:\./)?(?:product-images|product_images|uploads/products)/#i', 'uploads/products/', $path);
}

/** Convert legacy receipt paths to the current uploads folder. */
function giftshop_receipt_image_path($path)
{
	$path = str_replace('\\', '/', trim((string) $path));
	return preg_replace('#^(?:\./)?(?:receipts|receipt|uploads/receipts|uploads/receipt)/#i', 'uploads/receipt/', $path);
}
