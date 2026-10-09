<div class="container-lg py-5">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo site_url('user'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo site_url('user/reservations'); ?>">My Reservations</a></li>
            <li class="breadcrumb-item active"><?php echo html_escape($res['reservation_code']); ?></li>
        </ol>
    </nav>

    <?php if (isset($receipt_success)): ?>
        <div class="alert alert-success"><?php echo html_escape($receipt_success); ?></div>
    <?php endif; ?>
    <?php if (isset($receipt_error)): ?>
        <div class="alert alert-danger"><?php echo html_escape($receipt_error); ?></div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Reservation Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><b>Reservation <?php echo html_escape($res['reservation_code']); ?></b></span>
                    <?php
                        $status_badge = array(
                            'pending' => 'bg-warning text-dark',
                            'confirmed' => 'bg-info text-dark',
                            'ready' => 'bg-primary text-white',
                            'completed' => 'bg-success text-white',
                            'cancelled' => 'bg-secondary text-white'
                        );
                    ?>
                    <span class="badge <?php echo $status_badge[$res['status']] ?? 'bg-secondary'; ?>">
                        <?php echo ucfirst(html_escape($res['status'])); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row small text-muted mb-3">
                        <div class="col-md-4">Created: <b><?php echo date('M d, Y h:i A', strtotime($res['created_at'])); ?></b></div>
                        <div class="col-md-4">Reserve until: <b><?php echo $res['expiry_date'] ? date('M d, Y', $expiry) : '—'; ?></b></div>
                        <div class="col-md-4">Total: <b class="text-danger">₱<?php echo number_format((float) $res['total_amount'], 2); ?></b></div>
                    </div>

                    <?php if ($res['notes']): ?>
                        <p class="small"><b>Notes:</b> <?php echo nl2br(html_escape($res['notes'])); ?></p>
                    <?php endif; ?>

                    <?php if (isset($is_expired) && $is_expired): ?>
                        <div class="alert alert-warning py-2 small">This reservation has expired. Please contact the giftshop.</div>
                    <?php elseif (in_array($res['status'], array('pending', 'confirmed', 'ready'), TRUE)): ?>
                        <div class="alert alert-info py-2 small">
                            This reservation expires in <b><?php echo (int) $days_left; ?> day<?php echo $days_left == 1 ? '' : 's'; ?></b>
                            <?php if ($res['status'] !== 'ready'): ?>
                                — upload a screenshot of your GCash payment to confirm.
                            <?php else: ?>
                                — your items are ready for pickup!
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($res['receipt_image']): ?>
                        <h6 class="fw-bold mt-3">Proof of Payment</h6>
                        <?php $receipt_src = base_url(html_escape(giftshop_receipt_image_path($res['receipt_image']))); ?>
                        <button type="button" class="btn p-0 border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#receiptPreviewModal" aria-label="View receipt image">
                            <img src="<?php echo $receipt_src; ?>" class="img-fluid rounded border" alt="Receipt" style="max-width: 320px; cursor: zoom-in;">
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><b>Items</b></div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
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
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-light">
                                <td colspan="3" class="text-end"><b>Grand Total</b></td>
                                <td><b class="text-danger">₱<?php echo number_format((float) $res['total_amount'], 2); ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar: Receipt Upload -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><b>Upload Proof of Payment</b></div>
                <div class="card-body">
                    <?php if (in_array($res['status'], array('completed', 'cancelled'), TRUE)): ?>
                        <p class="text-muted small mb-0">Receipts can only be uploaded for <b>pending</b>, <b>confirmed</b>, or <b>ready</b> reservations.</p>
                    <?php else: ?>
                        <?php if ($res['receipt_image']): ?>
                            <p class="text-success small">
                                <i class="bi bi-check-circle"></i> A receipt is already on file. You may replace it below.
                            </p>
                        <?php endif; ?>
                        <?php echo form_open_multipart('user/upload_receipt'); ?>
                            <input type="hidden" name="reservation_id" value="<?php echo (int) $res['id']; ?>">
                            <div class="mb-2">
                                <label class="form-label small text-muted">Receipt image (JPG, PNG or GIF, max 5MB)</label>
                                <input type="file" name="receipt_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif">
                                <?php if ( ! empty($receipt_error)): ?><small class="text-danger d-block mt-1"><?php echo html_escape($receipt_error); ?></small><?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="bi bi-upload"></i> Upload Receipt
                                </button>
                            </div>
                        <?php echo form_close(); ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body text-center small text-muted">
                    <i class="bi bi-patch-question display-6 text-warning"></i>
                    <p class="mb-0 mt-2">Questions about your reservation?<br>Contact the giftshop staff.</p>
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
