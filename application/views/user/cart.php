<div class="container-lg py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="shop-title mb-0">Your Cart</h2>
        <a href="<?php echo site_url('shop/browse'); ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-arrow-left"></i> Continue Shopping
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo html_escape($error); ?></div>
    <?php endif; ?>

    <?php if (isset($reservation_success) && $reservation_success): ?>
        <div class="alert alert-success">
            Your reservation was submitted successfully!
            Your Reservation Code is <b><?php echo html_escape($reservation_code); ?></b>.
            <a href="<?php echo site_url('user/reservations'); ?>" class="alert-link">View your reservations <i class="bi bi-arrow-right"></i></a>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size:4rem; color:#ccc;"></i>
            <h5 class="mt-3 text-muted">Your cart is empty</h5>
            <a href="<?php echo site_url('shop/browse'); ?>" class="btn btn-danger mt-2">Browse Products</a>
        </div>
    <?php else: ?>
        <form method="post" action="<?php echo site_url('cart/submit'); ?>" id="reservation-form">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;"></th>
                                <th colspan="2">Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input item-check"
                                               name="selected_items[]" value="<?php echo html_escape($item['rowid']); ?>"
                                               checked>
                                    </td>
                                    <td style="width:80px;">
                                        <?php if ( ! empty($item['image_url'])): ?>
                                            <img src="<?php echo base_url(html_escape(giftshop_product_image_path($item['image_url']))); ?>"
                                                 class="rounded" style="width:60px; height:60px; object-fit:cover;">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <b><?php echo html_escape($item['name']); ?></b>
                                        <?php if ( ! empty($item['size'])): ?>
                                            <br><small class="text-muted">Size: <?php echo html_escape($item['size']); ?></small>
                                        <?php endif; ?>
                                        <?php if ( ! empty($item['color'])): ?>
                                            <br><small class="text-muted">Color: <?php echo html_escape($item['color']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>₱<?php echo number_format((float) $item['price'], 2); ?></td>
                                    <td>
                                        <div class="input-group input-group-sm" style="max-width:110px;">
                                            <button class="btn btn-outline-secondary" type="button"
                                                    onclick="location.href='<?php echo site_url('cart/decrease/' . $item['rowid']); ?>'">−</button>
                                            <span class="form-control text-center"><?php echo (int) $item['qty']; ?></span>
                                            <button class="btn btn-outline-secondary" type="button"
                                                    <?php echo $item['qty'] >= $item['max_qty'] ? 'disabled' : ''; ?>
                                                    onclick="location.href='<?php echo site_url('cart/increase/' . $item['rowid']); ?>'">+</button>
                                        </div>
                                        <?php if ($item['qty'] >= $item['max_qty']): ?>
                                            <small class="text-danger d-block">Max in stock (<?php echo (int) $item['max_qty']; ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><b>₱<?php echo number_format((float) $item['subtotal'], 2); ?></b></td>
                                    <td class="text-end">
                                        <a href="<?php echo site_url('cart/remove/' . $item['rowid']); ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Remove this item from cart?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold">Notes for Staff</h6>
                            <textarea name="notes" class="form-control" rows="3" maxlength="500"
                                      placeholder="Optional notes about your reservation..."><?php echo set_value('notes'); ?></textarea>
                            <?php echo form_error('notes', '<small class="text-danger d-block mt-1">', '</small>'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold">Cart Summary</h6>
                            <div class="d-flex justify-content-between">
                                <span>Items</span>
                                <span><b id="summary-items"><?php echo (int) $total_items; ?></b></span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                <span class="fs-5">Total</span>
                                <span class="fs-4 text-danger fw-bold" id="summary-total">
                                    ₱<?php echo number_format((float) $total, 2); ?>
                                </span>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-danger btn-lg w-100" id="submit-reservation">
                                Create Reservation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelectorAll('.item-check').length === 0) { return; }
    function refreshSummary() {
        var form = document.getElementById('reservation-form');
        var checkboxes = form.querySelectorAll('.item-check');
        var items = 0, total = 0;
        checkboxes.forEach(function (cb, i) {
            var row = cb.closest('tr');
            var sub = parseFloat((row.querySelectorAll('td')[5].innerText || '0').replace(/[^\d.]/g, ''));
            if (cb.checked) { items++; total += sub; }
        });
        document.getElementById('summary-items').textContent = items;
        document.getElementById('summary-total').textContent = '₱' + total.toFixed(2);
    }
    document.querySelectorAll('.item-check').forEach(function (cb) { cb.addEventListener('change', refreshSummary); });
});
</script>
