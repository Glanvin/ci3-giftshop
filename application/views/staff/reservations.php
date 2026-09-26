<div class="container-lg py-5">

    <h3 class="fw-bold mb-1">Reservations</h3>
    <p class="text-muted">Track and update customer reservations</p>

    <?php if (isset($res_message)): ?>
        <div class="alert alert-info"><?php echo $res_message; ?></div>
    <?php endif; ?>

    <!-- Status filter pills -->
    <div class="d-flex flex-wrap gap-2 my-4">
        <?php
            $statuses = array(
                'all' => array('All', 'primary', 'all'),
                'pending' => array('Pending', 'warning', 'pending'),
                'confirmed' => array('Confirmed', 'info', 'confirmed'),
                'ready' => array('Ready', 'primary', 'ready'),
                'completed' => array('Completed', 'success', 'completed'),
                'cancelled' => array('Cancelled', 'secondary', 'cancelled')
            );
        ?>
        <?php foreach ($statuses as $key => $meta): ?>
            <?php
                $count_key = $meta[2] === 'all'
                    ? array_sum($counts)
                    : ($counts[$meta[2]] ?? 0);
            ?>
            <a href="<?php echo site_url('staff/reservations/' . $key); ?>"
               class="btn btn-sm btn-outline-<?php echo $meta[1]; ?> <?php echo $filter == $key ? 'active fw-bold' : ''; ?>">
                <?php echo $meta[0]; ?> (<?php echo (int) $count_key; ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-5">No reservations in this view.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($reservations as $row): ?>
                        <?php
                            $sb = array(
                                'pending' => 'bg-warning text-dark',
                                'confirmed' => 'bg-info text-dark',
                                'ready' => 'bg-primary text-white',
                                'completed' => 'bg-success text-white',
                                'cancelled' => 'bg-secondary text-white'
                            );
                        ?>
                        <tr>
                            <td><b><?php echo html_escape($row['reservation_code']); ?></b></td>
                            <td><?php echo html_escape($row['full_name']); ?><br>
                                <small class="text-muted"><?php echo html_escape($row['email']); ?></small>
                            </td>
                            <td><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                            <td><?php echo (int) $row['item_count']; ?></td>
                            <td>₱<?php echo number_format((float) $row['total_amount'], 2); ?></td>
                            <td><span class="badge <?php echo $sb[$row['status']] ?? 'bg-secondary'; ?>"><?php echo ucfirst(html_escape($row['status'])); ?></span></td>
                            <td class="text-end">
                                <a href="<?php echo site_url('staff/reservation/' . (int) $row['id']); ?>" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="<?php echo site_url('staff/reservation/edit/' . (int) $row['id']); ?>" class="btn btn-sm btn-outline-danger">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>