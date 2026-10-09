<div class="container-lg py-5">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo html_escape($error_message); ?></div>
    <?php else: ?>
        <div class="row g-5">
            <!-- Product Image -->
            <div class="col-md-6">
                <img src="<?php echo base_url(html_escape($product['image_url'])); ?>"
                     alt="<?php echo html_escape($product['name']); ?>" class="img-fluid rounded shadow">
            </div>

            <!-- Product Details + Add to Cart -->
            <div class="col-md-6">
                <h2 class="fw-bold"><?php echo html_escape($product['name']); ?></h2>
                <span class="badge bg-danger-subtle text-danger mb-2">
                    <?php echo html_escape($product['category_name']); ?>
                </span>
                <?php if ($product['sku']): ?>
                    <p class="text-muted mb-1"><strong>SKU:</strong> <?php echo html_escape($product['sku']); ?></p>
                <?php endif; ?>
                <h3 class="text-danger fw-bold">₱<?php echo number_format((float) $product['price'], 2); ?></h3>
                <p class="text-muted mb-1"><strong>Available:</strong>
                    <?php
                        $stock_label = 'In Stock';
                        if ($product['stock_quantity'] <= 0) {
                            $stock_label = 'Out of Stock';
                        } elseif ($product['stock_quantity'] <= $product['low_stock_threshold']) {
                            $stock_label = 'Low Stock';
                        }
                        echo $stock_label . ' (' . (int) $product['stock_quantity'] . ' pcs)';
                    ?>
                </p>

                <?php if ($product['description']): ?>
                    <p class="mt-3"><?php echo nl2br(html_escape($product['description'])); ?></p>
                <?php endif; ?>

                <hr>
                <div class="mb-3">
                    <?php if ($product['size']): ?><span class="badge bg-light text-dark border">Size: <?php echo html_escape($product['size']); ?></span><?php endif; ?>
                    <?php if ($product['color']): ?><span class="badge bg-light text-dark border">Color: <?php echo html_escape($product['color']); ?></span><?php endif; ?>
                </div>

                <a href="<?php echo site_url('auth/login'); ?>" class="btn btn-danger btn-lg px-5">
                    Sign in to Reserve / Buy
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>