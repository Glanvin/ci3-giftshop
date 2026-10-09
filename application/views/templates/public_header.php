<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : 'Capitol University Official Giftshop'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body class="area-public">
    <nav class="navbar navbar-expand-sm customnav">
        <div class="navbar-nav">
            <div class="d-flex align-items-center">
                <img src="<?php echo base_url('product-images/culogo.jpg'); ?>" class="<?php echo ($active ?? '') != 'home' ? 'rounded-circle ' : ''; ?>logo-gold-outline" alt="Logo">
                <div class="brand-text">
                    <h1>Capitol University</h1>
                    <span>Official Giftshop</span>
                </div>
            </div>

            <div class="center-links">
                <li class="nav-item">
                    <a class="nav-link <?php echo (($active ?? '') == 'home') ? 'active' : ''; ?>" href="<?php echo site_url('/'); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (($active ?? '') == 'shop') ? 'active' : ''; ?>" href="<?php echo site_url('shop'); ?>">Shop</a>
                </li>
            </div>

            <div class="d-flex gap-2">
                <a href="<?php echo site_url('auth/login'); ?>" class="btn btn-login btn-sm">Sign In</a>
                <a href="<?php echo site_url('auth/signup'); ?>" class="btn btn-logins btn-sm">Sign Up</a>
            </div>
        </div>
    </nav>