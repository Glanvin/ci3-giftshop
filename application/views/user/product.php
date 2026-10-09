<div class="container-lg py-5">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo site_url('user'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo site_url('shop/browse'); ?>">Shop</a></li>
            <li class="breadcrumb-item active"><?php echo html_escape($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image -->
        <div class="col-md-6">
            <img src="<?php echo base_url(html_escape(giftshop_product_image_path($product['image_url']))); ?>"
                 alt="<?php echo html_escape($product['name']); ?>" class="img-fluid rounded shadow">
        </div>

        <!-- Product Details + Add to Cart -->
        <div class="col-md-6">
            <h2 class="fw-bold"><?php echo html_escape($product['name']); ?></h2>
            <span class="badge bg-danger-subtle text-danger mb-2"><?php echo html_escape($product['category_name']); ?></span>
            <?php if ($product['sku']): ?>
                <p class="text-muted mb-1"><strong>SKU:</strong> <?php echo html_escape($product['sku']); ?></p>
            <?php endif; ?>
            <h3 class="text-danger fw-bold">₱<?php echo number_format((float) $product['price'], 2); ?></h3>

            <?php if ($stock <= 0): ?>
                <div class="alert alert-danger py-2">This item is currently <b>Out of Stock</b>.</div>
            <?php else: ?>
                <p class="text-muted"><strong>Available:</strong>
                    <?php echo $stock <= $product['low_stock_threshold'] ? 'Low Stock' : 'In Stock'; ?>
                    (<?php echo $stock; ?> pcs)
                </p>
            <?php endif; ?>

            <?php if ($product['description']): ?>
                <p><?php echo nl2br(html_escape($product['description'])); ?></p>
            <?php endif; ?>

            <hr>
            <div class="mb-3">
                <?php if ($product['size']): ?><span class="badge bg-light text-dark border">Size: <?php echo html_escape($product['size']); ?></span><?php endif; ?>
                <?php if ($product['color']): ?><span class="badge bg-light text-dark border">Color: <?php echo html_escape($product['color']); ?></span><?php endif; ?>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <div class="input-group" style="max-width:130px;">
                    <button class="btn btn-outline-danger" type="button" onclick="changeQty(-1)">−</button>
                    <input type="number" id="qty" class="form-control text-center" value="1" min="1" max="<?php echo (int) $max_stock; ?>">
                    <button class="btn btn-outline-danger" type="button" onclick="changeQty(1)">+</button>
                </div>

                <?php echo form_open('cart/add'); ?>
                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                    <input type="hidden" name="quantity" id="qty-field" value="1">
                    <input type="hidden" name="redirect" value="view">
                    <button type="submit" class="btn btn-danger btn-lg px-4" <?php echo $stock <= 0 ? 'disabled' : ''; ?>>
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
function changeQty(delta) {
    var qty = parseInt(document.getElementById('qty').value, 10) || 1;
    var max = parseInt(document.getElementById('qty').max, 10) || 1;
    var next = qty + delta;
    if (next >= 1 && next <= max) {
        document.getElementById('qty').value = next;
        document.getElementById('qty-field').value = next;
    }
}
</script>
