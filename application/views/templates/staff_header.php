<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : 'Staff Panel'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body class="area-staff">

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

        <div class="d-flex align-items-center">
            <img src="<?php echo base_url('product-images/culogo.jpg'); ?>" class="logo-gold-outline" alt="Logo">
            <div class="brand-text">
                <h1>Staff Panel</h1>
                <span>Official Giftshop</span>
            </div>
        </div>

        <!-- Staff Navigation Links -->
        <div class="center-links">
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($active) && $active == 'dashboard') ? 'active' : ''; ?>" href="<?php echo site_url('staff'); ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($active) && $active == 'inventory') ? 'active' : ''; ?>" href="<?php echo site_url('staff/inventory'); ?>">
                    <i class="bi bi-box-seam"></i> Inventory
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($active) && $active == 'reservations') ? 'active' : ''; ?>" href="<?php echo site_url('staff/reservations'); ?>">
                    <i class="bi bi-clipboard-check"></i> Reservations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($active) && $active == 'reports') ? 'active' : ''; ?>" href="<?php echo site_url('staff/reports'); ?>">
                    <i class="bi bi-bar-chart"></i> Reports
                </a>
            </li>
        </div>

        <!-- Profile Dropdown -->
        <div class="profile-dropdown">
            <button class="profile-btn" onclick="toggleDropdown(event)">
                <i class="bi bi-person-circle"></i>
                <span>
                    <?php echo html_escape($this->session->userdata('full_name') ?: 'Staff'); ?>
                </span>
                <i class="bi bi-chevron-down" style="font-size:11px;"></i>
            </button>
            <div class="dropdown-menu-custom" id="profileDropdown">
                <div class="dropdown-header-custom">
                    <div class="name">
                        <?php echo html_escape($this->session->userdata('full_name') ?: 'Staff'); ?>
                    </div>
                    <div class="role">
                        <?php echo ucfirst($this->session->userdata('role')); ?>
                    </div>
                </div>
                <div class="dropdown-divider-custom"></div>
                <a href="<?php echo site_url('auth/logout'); ?>" class="dropdown-item-custom logout" onclick="showLogoutModal(); return false;">
                    <i class="bi bi-box-arrow-right"></i> Log Out
                </a>
            </div>
        </div>

    </div>
</nav>

<script src="<?php echo base_url('assets/js/main.js'); ?>"></script>