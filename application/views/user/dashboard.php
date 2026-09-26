<div class="container-lg py-5">

    <!-- Welcome -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">Welcome back, <?php echo html_escape($user_data['full_name']); ?>!</h3>
                <p class="text-muted mb-0"><?php echo html_escape($user_data['email']); ?></p>
            </div>
            <div class="text-end">
                <a href="<?php echo site_url('shop/browse'); ?>" class="btn btn-danger">
                    <i class="bi bi-bag"></i> Shop Products
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Reservations</h6>
                    <h2 class="fw-bold"><?php echo (int) $stats['total']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Active / In Progress</h6>
                    <h2 class="fw-bold text-danger"><?php echo (int) $stats['active']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pending Payments</h6>
                    <h2 class="fw-bold text-warning"><?php echo (int) $stats['pending']; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Reservation History</h5>
            <a href="<?php echo site_url('user/reservations'); ?>" class="btn btn-sm btn-outline-danger">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Reservation Code</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No reservations yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td><b><?php echo html_escape($row['reservation_code']); ?></b></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>₱<?php echo number_format((float) $row['total_amount'], 2); ?></td>
                            <td>
                                <?php
                                    $status_badge = array(
                                        'pending' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-info text-dark',
                                        'ready' => 'bg-primary text-white',
                                        'completed' => 'bg-success text-white',
                                        'cancelled' => 'bg-secondary text-white'
                                    );
                                ?>
                                <span class="badge <?php echo $status_badge[$row['status']] ?? 'bg-secondary'; ?>">
                                    <?php echo ucfirst(html_escape($row['status'])); ?>
                                </span>
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