<div class="container-lg py-5" style="max-width: 900px;">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo site_url('staff/inventory'); ?>">Inventory</a></li>
            <li class="breadcrumb-item active">Edit Product</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0 fw-bold">Edit Product</h5></div>
        <div class="card-body">
            <?php if ($error !== NULL): ?>
                <div class="alert alert-danger" role="alert"><?php echo html_escape($error); ?></div>
            <?php endif; ?>
            <?php echo form_open_multipart('staff/product/edit/' . (int) $product['id']); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo set_value('name', $product['name']); ?>">
                        <?php if ( ! empty($field_errors['name'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['name']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="<?php echo set_value('sku', $product['sku']); ?>">
                        <?php if ( ! empty($field_errors['sku'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['sku']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo (int) $cat['id']; ?>"
                                    <?php echo set_select('category_id', $cat['id'], $cat['id'] == $product['category_id']); ?>>
                                    <?php echo html_escape($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ( ! empty($field_errors['category_id'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['category_id']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="product-price" class="form-label">Base Price (₱)</label>
                        <input type="number" id="product-price" name="price" class="form-control" step="0.01" min="0" max="99999999.99" value="<?php echo set_value('price', $product['price']); ?>">
                        <?php if ( ! empty($field_errors['price'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['price']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="product-markup" class="form-label">Markup (%)</label>
                        <input type="number" id="product-markup" name="markup_percent" class="form-control" step="0.01" min="0" max="99999999.99" value="<?php echo set_value('markup_percent', '0'); ?>" aria-describedby="markup-help">
                        <?php if ( ! empty($field_errors['markup_percent'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['markup_percent']); ?></small><?php endif; ?>
                        <small id="markup-help" class="text-muted d-block">Applied once when saved. Reopening starts from the saved price with 0% markup.</small>
                    </div>
                    <div class="col-md-6">
                        <label for="selling-price-preview" class="form-label">Selling Price After Markup</label>
                        <?php
                            $preview_price = giftshop_price_with_markup(
                                set_value('price', $product['price'], FALSE),
                                set_value('markup_percent', '0', FALSE) ?: '0'
                            );
                        ?>
                        <output id="selling-price-preview" for="product-price product-markup" class="d-block fs-4 fw-bold text-danger" aria-live="polite"><?php echo $preview_price !== NULL ? '₱' . number_format((float) $preview_price, 2) : 'Enter a valid price and markup.'; ?></output>
                        <small class="text-muted d-block">This is the price customers will see after saving.</small>
                        <noscript><small class="text-muted d-block">Save Changes calculates the total. Enable JavaScript for a live preview.</small></noscript>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_quantity" class="form-control" min="0" value="<?php echo set_value('stock_quantity', $product['stock_quantity']); ?>">
                        <?php if ( ! empty($field_errors['stock_quantity'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['stock_quantity']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" class="form-control" min="0" value="<?php echo set_value('low_stock_threshold', $product['low_stock_threshold']); ?>">
                        <?php if ( ! empty($field_errors['low_stock_threshold'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['low_stock_threshold']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo set_select('status', 'active', $product['status'] == 'active'); ?>>Active</option>
                            <option value="inactive" <?php echo set_select('status', 'inactive', $product['status'] == 'inactive'); ?>>Inactive</option>
                            <option value="out_of_stock" <?php echo set_select('status', 'out_of_stock', $product['status'] == 'out_of_stock'); ?>>Out of Stock</option>
                        </select>
                        <?php if ( ! empty($field_errors['status'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['status']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Size</label>
                        <input type="text" name="size" class="form-control" value="<?php echo set_value('size', $product['size']); ?>">
                        <?php if ( ! empty($field_errors['size'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['size']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="<?php echo set_value('color', $product['color']); ?>">
                        <?php if ( ! empty($field_errors['color'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['color']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo set_value('description', $product['description']); ?></textarea>
                        <?php if ( ! empty($field_errors['description'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['description']); ?></small><?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Product Image</label>
                        <?php if ($product['image_url']): ?>
                            <div class="mb-2">
                                <img src="<?php echo base_url(html_escape(giftshop_product_image_path($product['image_url']))); ?>" class="rounded border" style="height:70px;">
                                <small class="text-muted ms-2">Leave blank to keep current image.</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                        <?php if ( ! empty($field_errors['product_image'])): ?><small class="text-danger d-block"><?php echo html_escape($field_errors['product_image']); ?></small><?php endif; ?>
                        <small class="text-muted">JPG, PNG, GIF or WebP &bull; max 5MB</small>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="update" value="1" class="btn btn-danger px-4">Save Changes</button>
                    <a href="<?php echo site_url('staff/inventory'); ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/product-pricing.js'); ?>"></script>
