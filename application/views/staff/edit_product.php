<div class="container-lg py-5" style="max-width: 900px;">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo site_url('staff/inventory'); ?>">Inventory</a></li>
            <li class="breadcrumb-item active">Edit Product</li>
        </ol>
    </nav>

    <button>Stock In</button>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0 fw-bold">Edit Product</h5></div>
        <div class="card-body">
            <?php echo form_open_multipart('staff/product/edit/' . (int) $product['id']); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo set_value('name', $product['name']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="<?php echo set_value('sku', $product['sku']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo (int) $cat['id']; ?>"
                                    <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo html_escape($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" required value="<?php echo set_value('price', $product['price']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_quantity" class="form-control" min="0" required value="<?php echo set_value('stock_quantity', $product['stock_quantity']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" class="form-control" min="0" value="<?php echo set_value('low_stock_threshold', $product['low_stock_threshold']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo $product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Size</label>
                        <input type="text" name="size" class="form-control" value="<?php echo set_value('size', $product['size']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="<?php echo set_value('color', $product['color']); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo set_value('description', $product['description']); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Product Image</label>
                        <?php if ($product['image_url']): ?>
                            <div class="mb-2">
                                <img src="<?php echo base_url(html_escape($product['image_url'])); ?>" class="rounded border" style="height:70px;">
                                <small class="text-muted ms-2">Leave blank to keep current image.</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
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