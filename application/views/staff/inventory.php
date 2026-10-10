<?php
    $active_modal = isset($open_modal) ? $open_modal : '';
    $modal_value = function ($modal_id, $field, $default = '') use ($active_modal) {
        return $active_modal === $modal_id ? set_value($field, $default) : $default;
    };
    $modal_error = function ($modal_id, $field) use ($active_modal) {
        return $active_modal === $modal_id ? form_error($field, '<small class="text-danger d-block">', '</small>') : '';
    };
    $modal_select = function ($modal_id, $field, $value) use ($active_modal) {
        return $active_modal === $modal_id ? set_select($field, $value) : '';
    };
?>
<div class="container-lg py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="shop-title mb-1">Inventory</h2>
            <p class="text-muted mb-0">Manage products, categories, and incoming stock.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#stockInModal">
                Stock In
            </button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                Add Category
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewCategoriesModal">
                Categories
            </button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addProductModal">
                Add Product
            </button>
        </div>
    </div>

    <?php if ( ! empty($message)): ?>
        <div class="alert alert-info" role="status"><?php echo html_escape($message); ?></div>
    <?php endif; ?>

    <!-- Colored Filter Buttons without numbers -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php 
            $filter_config = array(
                'all'    => array('label' => 'All',          'active_class' => 'btn-dark',                 'inactive_class' => 'btn-outline-dark'),
                'active' => array('label' => 'Active',       'active_class' => 'btn-success text-white',   'inactive_class' => 'btn-outline-success'),
                'low'    => array('label' => 'Low Stock',    'active_class' => 'btn-warning text-dark',    'inactive_class' => 'btn-outline-warning'),
                'out'    => array('label' => 'Out of Stock', 'active_class' => 'btn-danger text-white',    'inactive_class' => 'btn-outline-danger')
            );
        ?>
        <?php foreach ($filter_config as $key => $cfg): ?>
            <?php 
                $is_selected = ($filter === $key);
                $btn_class   = $is_selected ? $cfg['active_class'] . ' fw-bold' : $cfg['inactive_class'];
            ?>
            <a href="<?php echo site_url('staff/inventory?filter=' . $key); ?>"
               class="btn btn-sm <?php echo $btn_class; ?>">
                <?php echo $cfg['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Image</th><th>Name</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-5">No products found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                            if ($product['stock_quantity'] <= 0) {
                                $stock_state = 'Out of Stock';
                                $badge_class = 'bg-danger text-white';
                            } elseif ($product['stock_quantity'] <= $product['low_stock_threshold']) {
                                $stock_state = 'Low Stock';
                                $badge_class = 'bg-warning text-dark';
                            } else {
                                $raw_status = strtolower($product['status']);
                                if ($raw_status === 'active') {
                                    $stock_state = 'Active';
                                    $badge_class = 'bg-success text-white';
                                } else {
                                    $stock_state = ucfirst(str_replace('_', ' ', $product['status']));
                                    $badge_class = 'bg-secondary text-white';
                                }
                            }
                        ?>
                        <tr>
                            <td>
                                <?php if ( ! empty($product['image_url'])): ?>
                                    <img src="<?php echo base_url(html_escape(giftshop_product_image_path($product['image_url']))); ?>" alt="" class="rounded" style="width:44px;height:44px;object-fit:cover;">
                                <?php else: ?>
                                    <span class="text-muted"><i class="bi bi-image fs-4"></i></span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?php echo html_escape($product['name']); ?></td>
                            <td><?php echo html_escape($product['sku'] ?: '—'); ?></td>
                            <td><?php echo html_escape($product['category_name'] ?: '—'); ?></td>
                            <td>₱<?php echo number_format((float) $product['price'], 2); ?></td>
                            <td><?php echo (int) $product['stock_quantity']; ?></td>
                            <td><span class="badge rounded-pill <?php echo $badge_class; ?>"><?php echo html_escape($stock_state); ?></span></td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-success quick-stock-trigger" data-id="<?php echo (int) $product['id']; ?>" data-name="<?php echo html_escape($product['name']); ?>" data-sku="<?php echo html_escape($product['sku'] ?: ''); ?>" data-stock="<?php echo (int) $product['stock_quantity']; ?>" data-bs-toggle="modal" data-bs-target="#stockInModal" aria-label="Stock in <?php echo html_escape($product['name']); ?>">Stock In</button>
                                <a href="<?php echo site_url('staff/product/edit/' . (int) $product['id']); ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <?php echo form_open('staff/product/delete/' . (int) $product['id'], 'class="d-inline" onsubmit="return confirm(\'Delete this product?\');"'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                <?php echo form_close(); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- add product -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php echo form_open_multipart('staff/product/add'); ?>
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="addProductModalLabel">Add Product</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="new-product-name">Product Name</label><input id="new-product-name" type="text" name="name" class="form-control" value="<?php echo $modal_value('addProductModal', 'name'); ?>"><?php echo $modal_error('addProductModal', 'name'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-sku">SKU</label><input id="new-product-sku" type="text" name="sku" class="form-control" value="<?php echo $modal_value('addProductModal', 'sku'); ?>"><?php echo $modal_error('addProductModal', 'sku'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-category">Category</label><select id="new-product-category" name="category_id" class="form-select"><option value="">Select category</option><?php foreach ($categories as $category): ?><option value="<?php echo (int) $category['id']; ?>" <?php echo $modal_select('addProductModal', 'category_id', $category['id']); ?>><?php echo html_escape($category['name']); ?></option><?php endforeach; ?></select><?php echo $modal_error('addProductModal', 'category_id'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-price">Price</label><input id="new-product-price" type="number" name="price" class="form-control" min="0" max="99999999.99" step="0.01" value="<?php echo $modal_value('addProductModal', 'price'); ?>"><?php echo $modal_error('addProductModal', 'price'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-stock">Stock Quantity</label><input id="new-product-stock" type="number" name="stock_quantity" class="form-control" min="0" step="1" value="<?php echo $modal_value('addProductModal', 'stock_quantity', '0'); ?>"><?php echo $modal_error('addProductModal', 'stock_quantity'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-threshold">Low Stock Threshold</label><input id="new-product-threshold" type="number" name="low_stock_threshold" class="form-control" min="0" step="1" value="<?php echo $modal_value('addProductModal', 'low_stock_threshold', '10'); ?>"><?php echo $modal_error('addProductModal', 'low_stock_threshold'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-size">Size</label><input id="new-product-size" type="text" name="size" class="form-control" value="<?php echo $modal_value('addProductModal', 'size'); ?>"><?php echo $modal_error('addProductModal', 'size'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-color">Color</label><input id="new-product-color" type="text" name="color" class="form-control" value="<?php echo $modal_value('addProductModal', 'color'); ?>"><?php echo $modal_error('addProductModal', 'color'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-status">Status</label><select id="new-product-status" name="status" class="form-select"><option value="active" <?php echo $modal_select('addProductModal', 'status', 'active'); ?>>Active</option><option value="inactive" <?php echo $modal_select('addProductModal', 'status', 'inactive'); ?>>Inactive</option><option value="out_of_stock" <?php echo $modal_select('addProductModal', 'status', 'out_of_stock'); ?>>Out of Stock</option></select><?php echo $modal_error('addProductModal', 'status'); ?></div>
                        <div class="col-md-6"><label class="form-label" for="new-product-image">Product Image</label><input id="new-product-image" type="file" name="product_image" class="form-control" accept="image/*"><?php if ($active_modal === 'addProductModal' && ! empty($upload_error)): ?><small class="text-danger d-block"><?php echo html_escape($upload_error); ?></small><?php endif; ?><small class="text-muted">JPG, PNG, GIF or WebP, max 5MB.</small></div>
                        <div class="col-12"><label class="form-label" for="new-product-description">Description</label><textarea id="new-product-description" name="description" class="form-control" rows="3"><?php echo $modal_value('addProductModal', 'description'); ?></textarea><?php echo $modal_error('addProductModal', 'description'); ?></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Add Product</button></div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- add category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <?php echo form_open('StaffController/add_category'); ?>
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body"><label class="form-label" for="new-category-name">Category Name</label><input id="new-category-name" type="text" name="name" class="form-control" maxlength="100" value="<?php echo $modal_value('addCategoryModal', 'name'); ?>"><?php echo $modal_error('addCategoryModal', 'name'); ?></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Save Category</button></div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- stockin -->
<div class="modal fade" id="stockInModal" tabindex="-1" aria-labelledby="stockInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <?php echo form_open('staff/process_stock_in'); ?>
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="stockInModalLabel">Receive Stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="stock-in-search">Search Product</label>
                        <div class="position-relative">
                            <input type="search" id="stock-in-search" class="form-control" autocomplete="off" placeholder="Type a product name or SKU" role="combobox" aria-autocomplete="list" aria-controls="stock-in-product-results" aria-expanded="false" value="<?php echo isset($selected_stock_product['name']) ? html_escape($selected_stock_product['name']) : ''; ?>">
                            <input type="hidden" id="stock-in-product" name="product_id" value="<?php echo isset($selected_stock_product['id']) ? (int) $selected_stock_product['id'] : ''; ?>">
                            <div id="stock-in-product-results" class="list-group stock-product-results" role="listbox" hidden></div>
                        </div>
                        <div id="stock-in-search-status" class="form-text" role="status" aria-live="polite">Search by product name or SKU. Results appear after a short pause.</div>
                        <?php echo $modal_error('stockInModal', 'product_id'); ?>
                    </div>
                    <div class="mb-3"><label class="form-label" for="stock-in-quantity">Quantity</label><input id="stock-in-quantity" type="number" name="quantity" class="form-control" min="1" step="1" value="<?php echo $modal_value('stockInModal', 'quantity'); ?>"><?php echo $modal_error('stockInModal', 'quantity'); ?></div>
                    <div class="mb-3"><label class="form-label" for="stock-in-reference">PO / Delivery Receipt # <span class="text-muted">(optional)</span></label><input id="stock-in-reference" type="text" name="reference_no" class="form-control" value="<?php echo $modal_value('stockInModal', 'reference_no'); ?>"><?php echo $modal_error('stockInModal', 'reference_no'); ?></div>
                    <div class="mb-3"><label class="form-label" for="stock-in-supplier">Supplier <span class="text-muted">(optional)</span></label><input id="stock-in-supplier" type="text" name="supplier" class="form-control" value="<?php echo $modal_value('stockInModal', 'supplier'); ?>"><?php echo $modal_error('stockInModal', 'supplier'); ?></div>
                    <div><label class="form-label" for="stock-in-notes">Notes</label><textarea id="stock-in-notes" name="notes" class="form-control" rows="2"><?php echo $modal_value('stockInModal', 'notes'); ?></textarea><?php echo $modal_error('stockInModal', 'notes'); ?></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Confirm Stock In</button></div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- categories -->
<div class="modal fade" id="viewCategoriesModal" tabindex="-1" aria-labelledby="viewCategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="viewCategoriesModalLabel">Categories</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="categorySearchInput" class="form-control" placeholder="Search categories...">
                </div>
                <ul class="list-group" id="categoryList">
                    <?php if (empty($categories)): ?><li class="list-group-item text-muted text-center empty-msg">No categories found.</li><?php endif; ?>
                    <?php foreach ($categories as $category): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center category-item">
                            <span class="category-name"><?php echo html_escape($category['name']); ?></span>
                            <?php echo form_open('StaffController/delete_category/' . (int) $category['id'], 'class="d-inline" onsubmit="return confirm(\'Delete this category?\');"'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            <?php echo form_close(); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('stock-in-search');
    var productInput = document.getElementById('stock-in-product');
    var resultsBox = document.getElementById('stock-in-product-results');
    var searchStatus = document.getElementById('stock-in-search-status');
    var searchUrl = <?php echo json_encode(site_url('staff/inventory/search-products')); ?>;
    var searchTimer = null;
    var requestNumber = 0;

    function hideProductResults() {
        if (!resultsBox || !searchInput) return;
        resultsBox.hidden = true;
        resultsBox.innerHTML = '';
        searchInput.setAttribute('aria-expanded', 'false');
    }

    function selectProduct(product) {
        if (!searchInput || !productInput) return;
        requestNumber++;
        if (searchTimer) window.clearTimeout(searchTimer);
        productInput.value = product.id;
        searchInput.value = product.name;
        hideProductResults();
        if (searchStatus) {
            searchStatus.textContent = 'Selected ' + product.name + (product.sku ? ' · SKU ' + product.sku : '') + ' · Current stock: ' + product.stock_quantity + '.';
        }
    }

    function showProductResults(products) {
        if (!resultsBox || !searchInput) return;
        resultsBox.innerHTML = '';
        if (!products.length) {
            var emptyMessage = document.createElement('div');
            emptyMessage.className = 'list-group-item text-muted';
            emptyMessage.textContent = 'No matching products found.';
            resultsBox.appendChild(emptyMessage);
        } else {
            products.forEach(function (product) {
                var option = document.createElement('button');
                option.type = 'button';
                option.className = 'list-group-item list-group-item-action';
                option.setAttribute('role', 'option');
                option.setAttribute('aria-selected', 'false');
                option.textContent = product.name + (product.sku ? ' · SKU ' + product.sku : '') + ' · Stock: ' + product.stock_quantity;
                option.addEventListener('click', function () { selectProduct(product); });
                resultsBox.appendChild(option);
            });
        }
        resultsBox.hidden = false;
        searchInput.setAttribute('aria-expanded', 'true');
    }

    if (searchInput && productInput && resultsBox) {
        searchInput.addEventListener('input', function () {
            var query = searchInput.value.trim();
            productInput.value = '';
            requestNumber++;
            var thisRequest = requestNumber;
            if (searchTimer) window.clearTimeout(searchTimer);
            hideProductResults();
            if (!query) {
                if (searchStatus) searchStatus.textContent = 'Search by product name or SKU.';
                return;
            }

            if (searchStatus) searchStatus.textContent = 'Searching...';
            searchTimer = window.setTimeout(function () {
                if (searchStatus) searchStatus.textContent = 'Searching products…';
                fetch(searchUrl + '?q=' + encodeURIComponent(query), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        if (!response.ok) throw new Error('Product search failed.');
                        return response.json();
                    })
                    .then(function (payload) {
                        if (thisRequest !== requestNumber) return;
                        var products = payload && Array.isArray(payload.results) ? payload.results : [];
                        showProductResults(products);
                        if (searchStatus) searchStatus.textContent = products.length ? 'Select a product from the results.' : 'No matching products found.';
                    })
                    .catch(function () {
                        if (thisRequest !== requestNumber) return;
                        hideProductResults();
                        if (searchStatus) searchStatus.textContent = 'Could not search products. Please try again.';
                    });
            }, 1000);
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('#stock-in-search') && !event.target.closest('#stock-in-product-results')) {
                hideProductResults();
            }
        });
    }

    document.querySelectorAll('.quick-stock-trigger').forEach(function (button) {
        button.addEventListener('click', function () {
            selectProduct({
                id: button.getAttribute('data-id') || '',
                name: button.getAttribute('data-name') || '',
                sku: button.getAttribute('data-sku') || '',
                stock_quantity: button.getAttribute('data-stock') || '0'
            });
        });
    });
    var modalId = <?php echo json_encode(isset($open_modal) ? $open_modal : ''); ?>;
    var modalElement = modalId ? document.getElementById(modalId) : null;
    if (modalElement && window.bootstrap) bootstrap.Modal.getOrCreateInstance(modalElement).show();

    var stockModal = document.getElementById('stockInModal');
    if (stockModal) {
        stockModal.addEventListener('hidden.bs.modal', function () {
            if (searchTimer) window.clearTimeout(searchTimer);
            requestNumber++;
            if (searchInput) searchInput.value = '';
            if (productInput) productInput.value = '';
            hideProductResults();
            if (searchStatus) searchStatus.textContent = 'Search by product name or SKU.';
        });
    }

    var categorySearchInput = document.getElementById('categorySearchInput');
    if (categorySearchInput) {
        categorySearchInput.addEventListener('keyup', function () {
            var filter = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('#categoryList .category-item');
            items.forEach(function (item) {
                var nameText = item.querySelector('.category-name').textContent.toLowerCase();
                if (nameText.indexOf(filter) > -1) {
                    item.style.setProperty('display', 'flex', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        });
    }
});
</script>
