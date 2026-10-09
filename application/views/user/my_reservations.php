<div class="container-lg py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="shop-title mb-0">My Reservations</h2>
        <a href="<?php echo site_url('shop/browse'); ?>" class="btn btn-danger btn-sm">
            <i class="bi bi-bag"></i> New Reservation
        </a>
    </div>

    <?php if ($this->session->flashdata('reservation_success')): ?>
        <div class="alert alert-success">
            Your reservation was submitted successfully!
            Your Reservation Code is <b><?php echo html_escape($this->session->flashdata('reservation_code')); ?></b>.
        </div>
    <?php endif; ?>
    <?php if ( ! empty($receipt_success)): ?>
        <div class="alert alert-success" role="status"><?php echo html_escape($receipt_success); ?></div>
    <?php endif; ?>
    <?php if ( ! empty($receipt_error) && empty($receipt_open_id)): ?>
        <div class="alert alert-danger" role="alert"><?php echo html_escape($receipt_error); ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Created</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Reserve Until</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-5">You have no reservations yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($reservations as $row): ?>
                        <?php
                            $expiry = strtotime($row['expiry_date']);
                            $is_expired = $expiry && $expiry < time() && in_array($row['status'], array('pending', 'confirmed', 'ready'), TRUE);
                            $status_badge = array(
                                'pending' => 'bg-warning text-dark',
                                'confirmed' => 'bg-info text-dark',
                                'ready' => 'bg-primary text-white',
                                'completed' => 'bg-success text-white',
                                'cancelled' => 'bg-secondary text-white'
                            );
                        ?>
                        <tr>
                            <td><b><?php echo html_escape($row['reservation_code']); ?></b></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td><?php echo (int) $row['item_count']; ?></td>
                            <td>₱<?php echo number_format((float) $row['total_amount'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo $status_badge[$row['status']] ?? 'bg-secondary'; ?>">
                                    <?php echo ucfirst(html_escape($row['status'])); ?>
                                </span>
                                <?php if ($is_expired): ?>
                                    <span class="badge bg-danger">Expired</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['expiry_date']): ?>
                                    <?php echo date('M d, Y', $expiry); ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo site_url('user/reservation/' . (int) $row['id']); ?>" class="btn btn-sm btn-outline-danger">View</a>
                                <?php if (in_array($row['status'], array('pending', 'confirmed', 'ready'), TRUE)): ?>
                                    <button type="button" class="btn btn-sm btn-danger ms-1 receipt-upload-trigger"
                                            data-reservation-id="<?php echo (int) $row['id']; ?>"
                                            data-bs-toggle="modal" data-bs-target="#receiptUploadModal">
                                        <i class="bi bi-upload"></i>
                                        <?php echo empty($row['receipt_image']) ? 'Attach Receipt' : 'Replace Receipt'; ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="receiptUploadModal" tabindex="-1" aria-labelledby="receiptUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <?php echo form_open_multipart('user/upload_receipt'); ?>
            <input type="hidden" name="reservation_id" id="receipt-reservation-id" value="">
            <input type="hidden" name="return_to" value="reservations">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptUploadModalLabel">Attach Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="receipt-image" class="form-label">Receipt image (JPG, PNG or GIF, max 5MB)</label>
                    <input type="file" id="receipt-image" name="receipt_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif">
                    <?php if ( ! empty($receipt_error)): ?><small class="text-danger d-block mt-1"><?php echo html_escape($receipt_error); ?></small><?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-upload"></i> Upload Receipt</button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var reservationInput = document.getElementById('receipt-reservation-id');
    document.querySelectorAll('.receipt-upload-trigger').forEach(function (button) {
        button.addEventListener('click', function () {
            if (reservationInput) {
                reservationInput.value = button.getAttribute('data-reservation-id') || '';
            }
        });
    });
    var openForId = <?php echo json_encode(isset($receipt_open_id) ? (int) $receipt_open_id : 0); ?>;
    if (openForId && reservationInput) {
        reservationInput.value = openForId;
        var uploadModal = document.getElementById('receiptUploadModal');
        if (uploadModal && window.bootstrap) bootstrap.Modal.getOrCreateInstance(uploadModal).show();
    }
});
</script>
