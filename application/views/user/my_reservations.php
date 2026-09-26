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
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>