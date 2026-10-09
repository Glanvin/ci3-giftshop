<div class="container-lg py-5" style="max-width: 900px;">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo site_url('staff/reservations'); ?>">Reservations</a></li>
            <li class="breadcrumb-item active">Edit <?php echo html_escape($res['reservation_code']); ?></li>
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <b>Reservation <?php echo html_escape($res['reservation_code']); ?></b>
            <span class="badge <?php echo $sb[$res['status']] ?? 'bg-secondary'; ?>"><?php echo ucfirst(html_escape($res['status'])); ?></span>
        </div>
        <div class="card-body">
            <div class="row small text-muted mb-3">
                <div class="col-md-4">Customer: <b><?php echo html_escape($res['full_name']); ?></b></div>
                <div class="col-md-4">Email: <b><?php echo html_escape($res['email']); ?></b></div>
                <div class="col-md-4">Created: <b><?php echo date('M d, Y', strtotime($res['created_at'])); ?></b></div>
            </div>
            <div class="row small text-muted mb-3">
                <div class="col-md-4">Total: <b class="text-danger">₱<?php echo number_format((float) $res['total_amount'], 2); ?></b></div>
                <div class="col-md-4">Reserve until: <b><?php echo $res['expiry_date'] ? date('M d, Y', strtotime($res['expiry_date'])) : '—'; ?></b></div>
                <div class="col-md-4">
                    Receipt:
                    <?php if ($res['receipt_image']): ?>
                        <b class="text-success">Uploaded</b>
                    <?php else: ?>
                        <b class="text-warning">Missing</b>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><b>Change Status</b></div>
        <div class="card-body">
            <?php echo form_open('staff/reservation/edit/' . (int) $res['id']); ?>
                <div class="mb-3">
                    <label class="form-label">New Status</label>
                    <select name="new_status" class="form-select">
                        <?php foreach (array('pending', 'confirmed', 'ready', 'completed', 'cancelled') as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $res['status'] == $s ? 'selected' : ''; ?>>
                                <?php echo ucfirst($s); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="update_status" value="1" class="btn btn-danger px-4">Save Status</button>
                <a href="<?php echo site_url('staff/reservation/' . (int) $res['id']); ?>" class="btn btn-outline-secondary">Back</a>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
