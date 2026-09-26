<div class="container-lg py-5">

    <h3 class="fw-bold mb-1">Staff Dashboard</h3>
    <p class="text-muted">Overview of the giftshop</p>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Products</h6>
                    <h2 class="fw-bold"><?php echo (int) $total['all']; ?></h2>
                    <small class="text-muted"><?php echo (int) $total['active']; ?> active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Low Stock</h6>
                    <h2 class="fw-bold text-warning"><?php echo (int) $total['low']; ?></h2>
                    <small class="text-muted"><?php echo (int) $total['out']; ?> out of stock</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Pending Reservations</h6>
                    <h2 class="fw-bold text-danger"><?php echo (int) $pending; ?></h2>
                    <small class="text-muted">awaiting receipt</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Completed</h6>
                    <h2 class="fw-bold text-success"><?php echo (int) $completed; ?></h2>
                    <small class="text-muted">fulfilled</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage links -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a href="<?php echo site_url('staff/inventory'); ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-box-seam fs-1 text-danger"></i>
                        <div><h5 class="mb-0">Manage Inventory</h5><small class="text-muted">Add, edit and organize products</small></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?php echo site_url('staff/reservations'); ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-clipboard-check fs-1 text-danger"></i>
                        <div><h5 class="mb-0">Reservations</h5><small class="text-muted">Manage reservation statuses</small></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?php echo site_url('staff/reports'); ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-bar-chart fs-1 text-danger"></i>
                        <div><h5 class="mb-0">Reports</h5><small class="text-muted">Sales and stock analytics</small></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Recent Reservations</h5>
            <a href="<?php echo site_url('staff/reservations'); ?>" class="btn btn-sm btn-outline-danger">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr><th>Code</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    <?php if (empty($recent)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No reservations yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($recent as $row): ?>
                        <tr>
                            <td><b><?php echo html_escape($row['reservation_code']); ?></b></td>
                            <td><?php echo html_escape($row['full_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>₱<?php echo number_format((float) $row['total_amount'], 2); ?></td>
                            <td><span class="badge bg-warning text-dark"><?php echo ucfirst(html_escape($row['status'])); ?></span></td>
                            <td class="text-end">
                                <a href="<?php echo site_url('staff/reservation/' . (int) $row['id']); ?>" class="btn btn-sm btn-outline-danger">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>