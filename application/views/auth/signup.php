<div class="flip-overlay" id="flipOverlay">
    <div class="flip-coin">
        <img src="<?php echo base_url('uploads/products/culogo.jpg'); ?>" alt="CU Logo">
    </div>
</div>

<div class="auth-card">

    <div class="left-panel">
        <div class="logo-circle">
            <img src="<?php echo base_url('uploads/products/culogo.jpg'); ?>" alt="CU Logo">
        </div>
        <h2>Already a Member?</h2>
        <p>Sign in to manage your reservations and shop faster.</p>
        <a href="<?php echo site_url('auth/login'); ?>" class="btn-outline-white" onclick="return flipTo(this.href)">SIGN IN</a>
    </div>

    <div class="right-panel">
        <div class="form-title">Sign Up</div>
        <div class="form-sub">Fill in your details below</div>

        <?php echo form_open('auth/signup'); ?>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="ex. Maria Santos"
                       value="<?php echo set_value('full_name'); ?>">
                <span class="field-error"><?php echo form_error('full_name'); ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       placeholder="your.email@cu.edu.ph"
                       value="<?php echo set_value('email'); ?>">
                <span class="field-error"><?php echo form_error('email'); ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password">
                <span class="field-error"><?php echo form_error('password'); ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Password Confirm">
                <span class="field-error"><?php echo form_error('confirm_password'); ?></span>
            </div>
            <button type="submit" class="btn-main">Sign Up</button>
        <?php echo form_close(); ?>

        <div class="text-center mt-3">
            <a href="<?php echo site_url('/'); ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Back to Homepage</a>
        </div>
    </div>

</div>

<script src="<?php echo base_url('assets/js/auth.js'); ?>"></script>