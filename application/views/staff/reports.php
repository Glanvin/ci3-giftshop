<div class="container-lg py-5">

    <h3 class="fw-bold mb-1">Reports</h3>
    <p class="text-muted">Sales and inventory analytics</p>

    <!-- Key metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Revenue (Completed)</h6>
                    <h3 class="fw-bold text-danger">₱<?php echo number_format((float) $total_revenue, 2); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Pending Revenue</h6>
                    <h3 class="fw-bold text-warning">₱<?php echo number_format((float) $pending_revenue, 2); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Reservations</h6>
                    <h3 class="fw-bold"><?php echo (int) $total_res; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Monthly activity -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><b>Monthly Activity (Last 6 Months)</b></div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>Month</th><th>Reservations</th><th>Revenue</th></tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < count($months); $i++): ?>
                                <tr>
                                    <td><?php echo html_escape($months[$i]); ?></td>
                                    <td><?php echo (int) $monthly_counts[$i]; ?></td>
                                    <td class="text-danger">₱<?php echo number_format((float) $monthly_revenue[$i], 2); ?></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top selling products -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><b>Top Selling Products (Completed)</b></div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-end">Units Sold</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($top_products)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No completed sales yet.</td></tr>
                            <?php endif; ?>
                            <?php $rank = 1; ?>
                            <?php foreach ($top_products as $t): ?>
                                <tr>
                                    <td><?php echo $rank++; ?></td>
                                    <td><?php echo html_escape($t['name']); ?></td>
                                    <td class="text-end"><?php echo (int) $t['total_quantity']; ?></td>
                                    <td class="text-end">₱<?php echo number_format((float) $t['revenue'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Stock summary -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white"><b>Inventory Stock Summary</b></div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <h2 class="fw-bold text-success"><?php echo (int) $stock_ok; ?></h2>
                    <span class="text-muted">Products In Stock (OK)</span>
                </div>
                <div class="col-md-4">
                    <h2 class="fw-bold text-warning"><?php echo (int) $stock_low; ?></h2>
                    <span class="text-muted">Low Stock</span>
                </div>
                <div class="col-md-4">
                    <h2 class="fw-bold text-danger"><?php echo (int) $stock_out; ?></h2>
                    <span class="text-muted">Out of Stock</span>
                </div>
            </div>
        </div>
    </div>

</div>