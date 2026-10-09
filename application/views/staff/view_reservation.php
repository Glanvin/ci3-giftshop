<div class="container-lg py-5">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo site_url('staff/reservations'); ?>">Reservations</a></li>
            <li class="breadcrumb-item active"><?php echo html_escape($res['reservation_code']); ?></li>
        </ol>
    </nav>

    <?php
        $sb = array(
            'pending' => 'bg-warning text-dark',
            'confirmed' => 'bg-info text-dark',
            'ready' => 'bg-primary text-white',
            'completed' => 'bg-success text-white',
            'cancelled' => 'bg-secondary text-white'
        );
    ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><b>Reservation <?php echo html_escape($res['reservation_code']); ?></b></span>
                    <span class="badge <?php echo $sb[$res['status']] ?? 'bg-secondary'; ?>"><?php echo ucfirst(html_escape($res['status'])); ?></span>
                </div>
                <div class="card-body">
                    <div class="row small text-muted mb-3">
                        <div class="col-md-4">Created: <b><?php echo date('M d, Y h:i A', strtotime($res['created_at'])); ?></b></div>
                        <div class="col-md-4">Reserve until: <b><?php echo $res['expiry_date'] ? date('M d, Y', strtotime($res['expiry_date'])) : '—'; ?></b></div>
                        <div class="col-md-4">Total: <b class="text-danger">₱<?php echo number_format((float) $res['total_amount'], 2); ?></b></div>
                    </div>
                    <p class="small mb-1"><b>Customer:</b> <?php echo html_escape($res['full_name']); ?> (<?php echo html_escape($res['email']); ?>)</p>
                    <?php if ($res['notes']): ?>
                        <p class="small"><b>Notes:</b> <?php echo nl2br(html_escape($res['notes'])); ?></p>
                    <?php endif; ?>

                    <?php if ($res['receipt_image']): ?>
                        <h6 class="fw-bold mt-3">Proof of Payment</h6>
                        <?php $receipt_src = base_url(html_escape(giftshop_receipt_image_path($res['receipt_image']))); ?>
                        <button type="button" class="btn p-0 border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#receiptPreviewModal" aria-label="View receipt image">
                            <img src="<?php echo $receipt_src; ?>" class="img-fluid rounded border" alt="Receipt" style="max-width: 320px; cursor: zoom-in;">
                        </button>
                    <?php else: ?>
                        <p class="text-warning small mt-3"><i class="bi bi-exclamation-triangle"></i> No proof of payment uploaded yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><b>Items</b></div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Current Stock</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['image_url']): ?>
                                            <img src="<?php echo base_url(html_escape(giftshop_product_image_path($item['image_url']))); ?>" class="rounded me-2" style="width:45px; height:45px; object-fit:cover;">
                                        <?php endif; ?>
                                        <?php echo html_escape($item['name']); ?>
                                    </td>
                                    <td>₱<?php echo number_format((float) $item['price'], 2); ?></td>
                                    <td><?php echo (int) $item['quantity']; ?></td>
                                    <td>₱<?php echo number_format((float) $item['subtotal'], 2); ?></td>
                                    <td><?php echo (int) $item['current_stock']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-light">
                                <td colspan="3" class="text-end"><b>Grand Total</b></td>
                                <td colspan="2"><b class="text-danger">₱<?php echo number_format((float) $res['total_amount'], 2); ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status management -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><b>Update Status</b></div>
                <div class="card-body">
                    <p class="small text-muted">
                        Current status: <span class="badge <?php echo $sb[$res['status']] ?? 'bg-secondary'; ?>"><?php echo ucfirst(html_escape($res['status'])); ?></span>
                    </p>
                    <?php echo form_open('staff/reservation/edit/' . (int) $res['id']); ?>
                        <div class="mb-2">
                            <select name="new_status" class="form-select">
                                <?php foreach (array('pending', 'confirmed', 'ready', 'completed', 'cancelled') as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $res['status'] == $s ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($s); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="update_status" value="1" class="btn btn-danger btn-sm w-100">
                            Update Status
                        </button>
                    <?php echo form_close(); ?>
                    <a href="<?php echo site_url('staff/reservation/edit/' . (int) $res['id']); ?>" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        Full Edit Form
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($res['receipt_image']): ?>
    <div class="modal fade" id="receiptPreviewModal" tabindex="-1" aria-labelledby="receiptPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptPreviewModalLabel">Proof of Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="<?php echo $receipt_src; ?>" class="img-fluid rounded" alt="Receipt" style="max-height: 75vh;">
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
<?php endif; ?>
