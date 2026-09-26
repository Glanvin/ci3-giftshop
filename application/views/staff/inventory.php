<div class="container-lg py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Inventory</h3>
            <p class="text-muted mb-0">Manage all products in the giftshop</p>
        </div>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg"></i> Add Product
        </button>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Filter pills -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php
            $filters = array(
                'all' => array('All', 'primary'),
                'active' => array('Active', 'success'),
                'low' => array('Low Stock', 'warning'),
                'out' => array('Out of Stock', 'danger')
            );
        ?>
        <?php foreach ($filters as $key => $label): ?>
            <a href="<?php echo site_url('staff/inventory?filter=' . $key); ?>"
               class="btn btn-sm btn-outline-<?php echo $label[1]; ?> <?php echo $filter == $key ? 'active fw-bold' : ''; ?>">
                <?php echo $label[0]; ?> (<?php echo (int) ($counts[$key] ?? 0); ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Products table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-5">No products found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($products as $p): ?>
                        <?php
                            $st = $p['status'];
                            if ($p['stock_quantity'] <= 0) { $st = 'out'; }
                            elseif ($p['stock_quantity'] <= $p['low_stock_threshold']) { $st = 'low'; }
                            $st_badge = array(
                                'active' => 'bg-success',
                                'inactive' => 'bg-secondary',
                                'low' => 'bg-warning text-dark',
                                'out' => 'bg-danger'
                            );
                        ?>
                        <tr>
                            <td>
                                <?php if ($p['image_url']): ?>
                                    <img src="<?php echo base_url(html_escape($p['image_url'])); ?>" class="rounded" style="width:50px; height:50px; object-fit:cover;">
                                <?php else: ?>
                                    <i class="bi bi-image text-muted fs-3"></i>
                                <?php endif; ?>
                            </td>
                            <td><b><?php echo html_escape($p['name']); ?></b></td>
                            <td><?php echo html_escape($p['sku'] ?? '—'); ?></td>
                            <td><?php echo html_escape($p['category_name'] ?? '—'); ?></td>
                            <td>₱<?php echo number_format((float) $p['price'], 2); ?></td>
                            <td><?php echo (int) $p['stock_quantity']; ?></td>
                            <td><span class="badge <?php echo $st_badge[$st]; ?>"><?php echo ucfirst($st); ?></span></td>
                            <td class="text-end">
                                <a href="<?php echo site_url('staff/product/edit/' . (int) $p['id']); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php echo form_open('staff/product/delete/' . (int) $p['id'], 'style="display:inline;" onsubmit="return confirm(\'Delete this product?\');"'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php echo form_close(); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <?php echo form_open_multipart('staff/product/add'); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" required value="<?php echo set_value('name'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" placeholder="e.g. CU-TSH-XL">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo (int) $cat['id']; ?>" <?php echo set_select('category_id', $cat['id']); ?>>
                                        <?php echo html_escape($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price (₱)</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required value="<?php echo set_value('price'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" min="0" required value="<?php echo set_value('stock_quantity', '0'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" class="form-control" min="0" value="<?php echo set_value('low_stock_threshold', '10'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Size</label>
                            <input type="text" name="size" class="form-control" placeholder="e.g. XL">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" class="form-control" placeholder="e.g. Maroon">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="product_image" class="form-control" accept="image/*">
                            <small class="text-muted">JPG, PNG, GIF or WebP &bull; max 5MB</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Add Product</button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>