<div class="shop-page">
    <div class="container-lg py-5">

        <!-- Search / Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="shop-title">Shop</h2>
                <p class="text-muted mb-0">
                    Showing <?php echo count($products); ?> of <?php echo (int) $total; ?> products
                </p>
            </div>
            <div class="col-md-4">
                <form method="get" action="<?php echo site_url('shop'); ?>" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search products..."
                           value="<?php echo html_escape($search); ?>">
                    <button class="btn btn-danger px-3" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-md-3">
                <div class="card filter-card mb-4">
                    <div class="card-body">
                        <h6 class="text-danger fw-bold mb-3">Categories</h6>
                        <a href="<?php echo site_url('shop'); ?>" class="filter-link <?php echo $category == 0 ? 'text-danger fw-bold' : ''; ?>">
                            All Products
                        </a>
                        <?php foreach ($category_map as $cat_id => $cat_name): ?>
                            <a href="<?php echo site_url('shop?category=' . $cat_id); ?>" class="filter-link <?php echo $category == $cat_id ? 'text-danger fw-bold' : ''; ?>">
                                <?php echo html_escape($cat_name); ?>
                            </a>
                        <?php endforeach; ?>
                        <hr>
                        <h6 class="text-danger fw-bold mb-3">Sort By</h6>
                        <?php $qs = http_build_query(array_filter(array('search' => $search, 'category' => $category ?: NULL))); ?>
                        <?php foreach (array('newest' => 'Newest', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low') as $s_key => $s_label): ?>
                            <a href="<?php echo site_url('shop' . ($qs ? '?' . $qs : '') . ($qs ? '&' : '?') . 'sort=' . $s_key); ?>"
                               class="filter-link <?php echo $sort == $s_key ? 'text-danger fw-bold' : ''; ?>">
                                <?php echo $s_label; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-md-9">
                <?php if (empty($products)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-box" style="font-size:3rem; color:#ccc;"></i>
                        <p class="mt-3 text-muted">No products found.</p>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4">
                            <a href="<?php echo site_url('shop/product/' . (int) $product['id']); ?>" class="text-decoration-none d-block h-100">
                                <div class="card product-card h-100">
                                    <img src="<?php echo base_url(html_escape(giftshop_product_image_path($product['image_url']))); ?>" alt="<?php echo html_escape($product['name']); ?>" class="card-img-top">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="product-name"><?php echo html_escape($product['name']); ?></h5>
                                        <div class="product-meta">
                                            <span class="product-price">₱<?php echo number_format((float) $product['price'], 2); ?></span>
                                            <small class="product-category text-muted"><?php echo html_escape($product['category_name']); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                <li class="page-item <?php echo $page == $p ? 'active' : ''; ?>">
                                    <?php
                                        $links = array(
                                            'search' => $search != '' ? $search : NULL,
                                            'category' => $category > 0 ? $category : NULL,
                                            'sort' => $sort,
                                            'page' => $p > 1 ? $p : NULL
                                        );
                                    ?>
                                    <a class="page-link" href="<?php echo site_url('shop?' . http_build_query(array_filter($links))); ?>"><?php echo $p; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
