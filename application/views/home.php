<div class="container-fluid bgcustom text-white text-center size p-5">
        <h1 class="welcome">
            <span class="welcome-top">Welcome to</span><br>
            Capitol University <br>Official Giftshop
        </h1>
        <p class="fs-4 mt-3"> Your one-stop destination for textbooks, uniforms, and official university merchandise </p>
        <div class="d-flex gap-4 mt-5 justify-content-center">
            <a href="<?php echo site_url('auth/login'); ?>" class="btn btn-danger btn-lg px-5">Shop now</a>
            <a href="<?php echo site_url('auth/login'); ?>" class="btn btn-logins btn-lg px-5 sizelogin">Sign In</a>
        </div>
    </div>

    <div class="shopcateg" style="padding-top: 60px; padding-bottom: 60px;">
        <div class="text-center mb-5">
            <p class="welcome category-title" style="color: #800000;">Total Person Development</p>
            <hr class="w-50 mx-auto border-danger opacity-50">
            <p class="fs-5">Browse our extensive collection of university essentials</p>
        </div>

        <!-- Carousel -->
        <div id="demo" class="carousel slide" data-bs-ride="carousel">

            <!-- Indicators/dots -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#demo" data-bs-slide-to="3"></button>
            </div>

            <!-- The slideshow/carousel -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="<?php echo base_url('product-images/carousel1.jpg'); ?>" alt="Slide 1" class="d-block w-100">
                </div>
                <div class="carousel-item">
                    <img src="<?php echo base_url('product-images/carousel2.jpg'); ?>" alt="Slide 2" class="d-block w-100">
                </div>
                <div class="carousel-item">
                    <img src="<?php echo base_url('product-images/carousel3.jpg'); ?>" alt="Slide 3" class="d-block w-100">
                </div>
                <div class="carousel-item">
                    <img src="<?php echo base_url('product-images/carousel4.jpg'); ?>" alt="Slide 4" class="d-block w-100">
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>