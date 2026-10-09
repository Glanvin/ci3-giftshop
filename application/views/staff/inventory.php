<<<<<<< HEAD
<div class="container-lg py-5 px-4 px-md-5">

    <div class="d-flex justify-content-between align-items-start mb-4">
=======
<style>
/* Custom Modern Theme Styles */
:root {
    --primary-red: #dc3545;
    --primary-red-hover: #bb2d3b;
    --success-green: #198754;
    --success-green-hover: #157347;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --bg-surface: #ffffff;
    --bg-canvas: #f8fafc;
    --border-color: #e2e8f0;
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 16px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
    --shadow-md: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.02);
}

.custom-inventory-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
    color: var(--text-dark);
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
}

/* Page Header */
.custom-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.75rem;
}
.custom-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    letter-spacing: -0.025em;
}
.custom-subtitle {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.95rem;
}
.custom-actions {
    display: flex;
    gap: 0.75rem;
}

/* Custom Buttons */
.custom-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.15rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: var(--radius-sm);
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    text-decoration: none;
}
.custom-btn-danger {
    background-color: var(--primary-red);
    color: #fff;
}
.custom-btn-danger:hover {
    background-color: var(--primary-red-hover);
    color: #fff;
}
.custom-btn-outline-success {
    background: transparent;
    border-color: var(--success-green);
    color: var(--success-green);
}
.custom-btn-outline-success:hover {
    background-color: var(--success-green);
    color: #fff;
}
.custom-btn-secondary {
    background-color: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
}
.custom-btn-secondary:hover {
    background-color: #e2e8f0;
}
.custom-btn-sm {
    padding: 0.35rem 0.65rem;
    font-size: 0.8rem;
}

/* Custom Alerts */
.custom-alert {
    padding: 0.85rem 1.25rem;
    border-radius: var(--radius-sm);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.9rem;
    font-weight: 500;
}
.custom-alert-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.custom-alert-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.custom-alert-danger { background-color: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }

/* Filter Pills */
.custom-filters-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}
.custom-pill {
    padding: 0.4rem 0.9rem;
    border-radius: 9999px;
    font-size: 0.825rem;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid var(--border-color);
    background: var(--bg-surface);
    color: var(--text-muted);
    transition: all 0.15s ease;
}
.custom-pill:hover { background-color: #f1f5f9; }
.custom-pill.active {
    background-color: var(--text-dark);
    color: #fff;
    border-color: var(--text-dark);
}

/* Data Card & Table */
.custom-table-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.custom-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 0.875rem;
}
.custom-table th {
    background-color: #f8fafc;
    padding: 0.85rem 1rem;
    font-weight: 600;
    color: #475569;
    border-bottom: 1px solid var(--border-color);
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}
.custom-table td {
    padding: 0.9rem 1rem;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}
.custom-table tr:last-child td { border-bottom: none; }
.custom-table tr:hover td { background-color: #f8fafc; }

/* Product Details */
.product-img-thumb {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-sm);
    object-fit: cover;
    border: 1px solid var(--border-color);
}
.product-img-placeholder {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-sm);
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
}

/* Badges */
.custom-badge {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.725rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}
.custom-badge-active { background: #d1fae5; color: #065f46; }
.custom-badge-inactive { background: #e2e8f0; color: #475569; }
.custom-badge-low { background: #fef3c7; color: #92400e; }
.custom-badge-out { background: #ffe4e6; color: #9f1239; }

/* Modern Modal Overrides */
.modal-content {
    border: none !important;
    border-radius: var(--radius-lg) !important;
    box-shadow: var(--shadow-md) !important;
    overflow: hidden;
}
.modal-header {
    border-bottom: 1px solid var(--border-color) !important;
    padding: 1.25rem 1.5rem !important;
}
.modal-body {
    padding: 1.5rem !important;
}
.modal-footer {
    border-top: 1px solid var(--border-color) !important;
    padding: 1rem 1.5rem !important;
}
.custom-input, .custom-select, .custom-textarea {
    width: 100%;
    padding: 0.55rem 0.75rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-color);
    background-color: #fff;
    font-size: 0.875rem;
    outline: none;
    transition: border-color 0.15s ease;
}
.custom-input:focus, .custom-select:focus, .custom-textarea:focus {
    border-color: #94a3b8;
    box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
}
.custom-form-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.35rem;
    color: #334155;
}
</style>

<div class="custom-inventory-wrap">

    <div class="custom-page-header">
>>>>>>> db2fa71246e6267f1aa163181021ae250f5340f2
        <div>
            <h3 class="custom-title">Inventory</h3>
            <p class="custom-subtitle">Manage all products in the giftshop</p>
        </div>
        <div class="custom-actions">
            <!-- Stock In Header Button -->
            <button type="button" class="custom-btn custom-btn-outline-success" data-bs-toggle="modal" data-bs-target="#stockInModal">
                <i class="bi bi-box-arrow-in-down"></i> Stock In
            </button>
            <button type="button" class="custom-btn custom-btn-danger" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-lg"></i> Add Product
            </button>
        </div>
<<<<<<< HEAD
        <div class="d-flex flex-column align-items-end gap-2">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-folder-plus"></i> Add Category
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-lg"></i> Add Product
                </button>
            </div>
            <!-- Positioned below Add Product on the right -->
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewCategoriesModal">
                <i class="bi bi-list-ul me-1"></i> View All Categories
            </button>
        </div>
=======
>>>>>>> db2fa71246e6267f1aa163181021ae250f5340f2
    </div>

    <?php if (isset($message)): ?>
        <div class="custom-alert custom-alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Flash Messages for Stock In Operations -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="custom-alert custom-alert-success" role="alert">
            <span><?php echo $this->session->flashdata('success'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="custom-alert custom-alert-danger" role="alert">
            <span><?php echo $this->session->flashdata('error'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Pills -->
    <div class="custom-filters-bar">
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
               class="custom-pill <?php echo $filter == $key ? 'active' : ''; ?>">
                <?php echo $label[0]; ?> (<?php echo (int) ($counts[$key] ?? 0); ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Products Table -->
    <div class="custom-table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">No products found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($products as $p): ?>
                        <?php
                            $st = $p['status'];
                            if ($p['stock_quantity'] <= 0) { $st = 'out'; }
                            elseif ($p['stock_quantity'] <= $p['low_stock_threshold']) { $st = 'low'; }
                            $badge_class = 'custom-badge-' . $st;
                        ?>
                        <tr>
                            <td>
                                <?php if ($p['image_url']): ?>
                                    <img src="<?php echo base_url(html_escape($p['image_url'])); ?>" class="product-img-thumb">
                                <?php else: ?>
                                    <div class="product-img-placeholder"><i class="bi bi-image fs-5"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color: #0f172a;"><?php echo html_escape($p['name']); ?></strong></td>
                            <td style="color: var(--text-muted);"><?php echo html_escape($p['sku'] ?? '—'); ?></td>
                            <td><?php echo html_escape($p['category_name'] ?? '—'); ?></td>
                            <td style="font-weight: 600;">₱<?php echo number_format((float) $p['price'], 2); ?></td>
                            <td><span style="font-weight: 600;"><?php echo (int) $p['stock_quantity']; ?></span></td>
                            <td><span class="custom-badge <?php echo $badge_class; ?>"><?php echo ucfirst($st); ?></span></td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" 
                                        class="custom-btn custom-btn-outline-success custom-btn-sm btn-quick-stock" 
                                        data-id="<?php echo (int) $p['id']; ?>" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#stockInModal"
                                        title="Quick Stock In">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                                <a href="<?php echo site_url('staff/product/edit/' . (int) $p['id']); ?>" class="custom-btn custom-btn-secondary custom-btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php echo form_open('staff/product/delete/' . (int) $p['id'], 'style="display:inline;" onsubmit="return confirm(\'Delete this product?\');"'); ?>
                                    <button type="submit" class="custom-btn custom-btn-secondary custom-btn-sm" style="color: var(--primary-red);">
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
                    <h5 class="modal-title" style="font-weight: 700; font-size: 1.15rem;">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="custom-form-label">Product Name</label>
                            <input type="text" name="name" class="custom-input" required value="<?php echo set_value('name'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">SKU</label>
                            <input type="text" name="sku" class="custom-input" placeholder="e.g. CU-TSH-XL">
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">Category</label>
                            <select name="category_id" class="custom-select" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo (int) $cat['id']; ?>" <?php echo set_select('category_id', $cat['id']); ?>>
                                        <?php echo html_escape($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">Price (₱)</label>
                            <input type="number" name="price" class="custom-input" step="0.01" min="0" required value="<?php echo set_value('price'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="custom-input" min="0" required value="<?php echo set_value('stock_quantity', '0'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" class="custom-input" min="0" value="<?php echo set_value('low_stock_threshold', '10'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">Size</label>
                            <input type="text" name="size" class="custom-input" placeholder="e.g. XL">
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">Color</label>
                            <input type="text" name="color" class="custom-input" placeholder="e.g. Maroon">
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">Status</label>
                            <select name="status" class="custom-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="custom-form-label">Product Image</label>
                            <input type="file" name="product_image" class="custom-input" accept="image/*">
                            <small style="color: var(--text-muted); font-size: 0.75rem;">JPG, PNG, GIF or WebP &bull; max 5MB</small>
                        </div>
                        <div class="col-12">
                            <label class="custom-form-label">Description</label>
                            <textarea name="description" class="custom-textarea" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="custom-btn custom-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="custom-btn custom-btn-danger">Add Product</button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<<<<<<< HEAD
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <?php echo form_open('StaffController/add_category'); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Uniforms, Textbooks, Merchandise" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Save Category</button>
=======
<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1" aria-labelledby="stockInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <?php echo form_open('staff/process_stock_in'); ?>
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--success-green); color: white;">
                    <h5 class="modal-title" id="stockInModalLabel" style="font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="bi bi-box-arrow-in-down"></i> Receive Stock (Stock In)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="custom-form-label">Select Product</label>
                        <select name="product_id" id="stock_in_product_id" class="custom-select" required>
                            <option value="">-- Select Product --</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?php echo (int) $prod['id']; ?>">
                                    <?php echo html_escape($prod['name']); ?> (Current Stock: <?php echo (int) $prod['stock_quantity']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="custom-form-label">Quantity Added</label>
                        <input type="number" name="quantity" class="custom-input" min="1" placeholder="e.g. 10" required>
                    </div>
                    <div class="mb-3">
                        <label class="custom-form-label">PO / Delivery Receipt # <span style="color: var(--text-muted); font-weight: normal;">(Optional)</span></label>
                        <input type="text" name="reference_no" class="custom-input" placeholder="e.g. DR-2026-001">
                    </div>
                    <div class="mb-3">
                        <label class="custom-form-label">Supplier <span style="color: var(--text-muted); font-weight: normal;">(Optional)</span></label>
                        <input type="text" name="supplier" class="custom-input" placeholder="e.g. CU Printing Services">
                    </div>
                    <div class="mb-3">
                        <label class="custom-form-label">Notes</label>
                        <textarea name="notes" class="custom-textarea" rows="2" placeholder="e.g. Shipment arrival for second semester"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="custom-btn custom-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="custom-btn custom-btn-outline-success" style="background-color: var(--success-green); color: white;">Confirm Stock In</button>
>>>>>>> db2fa71246e6267f1aa163181021ae250f5340f2
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<<<<<<< HEAD
<!-- View Categories Modal -->
<div class="modal fade" id="viewCategoriesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Existing Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php if (empty($categories)): ?>
                        <li class="list-group-item text-muted text-center">No categories found.</li>
                    <?php endif; ?>
                    <?php foreach ($categories as $cat): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><b>#<?php echo (int)$cat['id']; ?></b> — <?php echo html_escape($cat['name']); ?></span>
                            <?php echo form_open('StaffController/delete_category/' . (int)$cat['id'], 'style="display:inline;" onsubmit="return confirm(\'Delete this category?\');"'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php echo form_close(); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
=======
<script>
document.addEventListener('DOMContentLoaded', function () {
    const quickStockBtns = document.querySelectorAll('.btn-quick-stock');
    const productSelect = document.getElementById('stock_in_product_id');

    quickStockBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            if (productSelect) {
                productSelect.value = productId;
            }
        });
    });
});
</script>
>>>>>>> db2fa71246e6267f1aa163181021ae250f5340f2
