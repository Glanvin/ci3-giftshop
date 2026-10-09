<div class="shop-page">
    <div class="container-lg py-5">

        <div class="row mb-4 align-items-end">
            <div class="col-md-7">
                <h2 class="shop-title">Browse Products</h2>
                <p class="text-muted mb-0">Showing <?php echo count($products); ?> of <?php echo (int) $total; ?> products</p>
            </div>
            <div class="col-md-5">
                <form method="get" action="<?php echo site_url('shop/browse'); ?>" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search products..."
                           value="<?php echo html_escape($search); ?>">
                    <button class="btn btn-danger px-3" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3 mb-4 align-items-center">
            <span class="text-muted small">Filter:</span>
            <a href="<?php echo site_url('shop/browse'); ?>" class="btn btn-sm <?php echo $category == 0 ? 'btn-danger' : 'btn-outline-danger'; ?>">All</a>
            <?php foreach ($category_map as $cat_id => $cat_name): ?>
                <a href="<?php echo site_url('shop/browse?category=' . $cat_id); ?>" class="btn btn-sm <?php echo $category == $cat_id ? 'btn-danger' : 'btn-outline-danger'; ?>">
                    <?php echo html_escape($cat_name); ?>
                </a>
            <?php endforeach; ?>

            <span class="ms-auto text-muted small">Sort:</span>
            <?php $qs = http_build_query(array_filter(array('search' => $search, 'category' => $category ?: NULL))); ?>
            <?php foreach (array('newest' => 'Newest', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low') as $s_key => $s_label): ?>
                <a href="<?php echo site_url('shop/browse' . ($qs ? '?' . $qs : '') . ($qs ? '&' : '?') . 'sort=' . $s_key); ?>"
                   class="btn btn-sm <?php echo $sort == $s_key ? 'btn-outline-danger fw-bold' : 'btn-outline-secondary'; ?>">
                    <?php echo $s_label; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="bi bi-box" style="font-size:3rem; color:#ccc;"></i>
                <p class="mt-3 text-muted">No products found.</p>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <a href="<?php echo site_url('user/product/' . (int) $product['id']); ?>" class="text-decoration-none">
                            <img src="<?php echo base_url(html_escape(giftshop_product_image_path($product['image_url']))); ?>" alt="<?php echo html_escape($product['name']); ?>" class="card-img-top">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <a href="<?php echo site_url('user/product/' . (int) $product['id']); ?>" class="text-decoration-none">
                                <h5 class="product-name"><?php echo html_escape($product['name']); ?></h5>
                            </a>
                            <div class="product-meta">
                                <span class="product-price">₱<?php echo number_format((float) $product['price'], 2); ?></span>
                                <small class="product-category text-muted"><?php echo html_escape($product['category_name']); ?></small>
                            </div>
                            <?php echo form_open('cart/add', 'class="pt-3"'); ?>
                                <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="redirect" value="browse">
                                <button type="submit" class="btn btn-danger btn-sm w-100"
                                        <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

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
                            <a class="page-link" href="<?php echo site_url('shop/browse?' . http_build_query(array_filter($links))); ?>"><?php echo $p; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
