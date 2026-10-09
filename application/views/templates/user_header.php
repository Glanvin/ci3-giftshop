<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : 'User Area'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body class="area-user">

<!-- Logout Modal -->
<div id="logoutOverlay" class="logout-overlay">
    <div class="logout-modal">
        <h5>Are you sure you want to log out?</h5>
        <div class="d-flex gap-2 justify-content-center">
            <button onclick="closeLogoutModal()" class="btn btn-secondary btn-sm px-4">Cancel</button>
            <a href="<?php echo site_url('auth/logout'); ?>" class="btn btn-sm px-4" style="background:#6d1223; color:white;">Yes, Log Out</a>
        </div>
    </div>
</div>

<!-- Navigation bar -->
<nav class="navbar navbar-expand-sm customnav">
    <div class="navbar-nav d-flex justify-content-between align-items-center w-100">

        <!-- Logo -->
        <div class="d-flex align-items-center">
            <img src="<?php echo base_url('product-images/culogo.jpg'); ?>" class="logo-gold-outline" alt="Logo">
            <div class="brand-text">
                <h1>Capitol University</h1>
                <span>Official Giftshop</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="center-links">
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($active) && $active == 'home') ? 'active' : ''; ?>" href="<?php echo site_url('user'); ?>">
                    <i class="bi bi-house"></i> Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($active) && $active == 'shop') ? 'active' : ''; ?>" href="<?php echo site_url('shop/browse'); ?>">
                    <i class="bi bi-bag"></i> Shop
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($active) && $active == 'reservations') ? 'active' : ''; ?>" href="<?php echo site_url('user/reservations'); ?>">
                    <i class="bi bi-clipboard-check"></i> My Reservations
                </a>
            </li>
        </div>

        <div class="d-flex align-items-center gap-3">

            <!-- Cart Button -->
            <a href="<?php echo site_url('cart'); ?>" class="cart-btn">
                <i class="bi bi-cart-fill"></i>
                Cart
                <?php $cart_count = isset($this->cart) ? (int) $this->cart->total_items() : 0; ?>
                <span id="cart-count" class="badge rounded-pill bg-danger" style="<?php echo $cart_count > 0 ? '' : 'display:none;'; ?> font-size:11px;"><?php echo $cart_count; ?></span>
            </a>

            <!-- Profile Dropdown -->
            <div class="profile-dropdown">
                <button class="profile-btn" onclick="toggleDropdown(event)">
                    <i class="bi bi-person-circle"></i>
                    <span id="header-username">
                        <?php echo html_escape($this->session->userdata('full_name') ?: 'Account'); ?>
                    </span>
                    <i class="bi bi-chevron-down" style="font-size:11px;"></i>
                </button>
                <div class="dropdown-menu-custom" id="profileDropdown">
                    <div class="dropdown-header-custom">
                        <div class="name">
                            <?php echo html_escape($this->session->userdata('full_name') ?: 'Guest'); ?>
                        </div>
                        <div class="role">
                            <?php echo ucfirst($this->session->userdata('role')); ?>
                        </div>
                    </div>
                    <a href="<?php echo site_url('user'); ?>" class="dropdown-item-custom">
                        <i class="bi bi-person"></i> My Profile
                    </a>
                    <a href="<?php echo site_url('user/reservations'); ?>" class="dropdown-item-custom">
                        <i class="bi bi-clipboard-check"></i> My Reservations
                    </a>
                    <div class="dropdown-divider-custom"></div>
                    <a href="<?php echo site_url('auth/logout'); ?>" class="dropdown-item-custom logout" onclick="showLogoutModal(); return false;">
                        <i class="bi bi-box-arrow-right"></i> Log Out
                    </a>
                </div>
            </div>

        </div>
    </div>
</nav>
<?php $this->load->view('templates/notifications'); ?>

<script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
