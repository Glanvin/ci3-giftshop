<div class="flip-overlay" id="flipOverlay" data-redirect="<?php echo site_url('auth/signup'); ?>">
    <div class="flip-coin">
        <img src="<?php echo base_url('product-images/culogo.jpg'); ?>" alt="CU Logo">
    </div>
</div>

<div class="auth-card">

    <div class="left-panel">
        <div class="logo-circle">
            <img src="<?php echo base_url('product-images/culogo.jpg'); ?>" alt="CU Logo">
        </div>
        <h2>New Here?</h2>
        <p>Sign up to reserve your university essentials.</p>
        <button class="btn-outline-white" onclick="goToSignup()">SIGN UP</button>
    </div>

    <div class="right-panel">
        <div class="form-title">Sign In</div>
        <div class="form-sub">Enter your credentials to continue</div>

        <?php if (isset($register_ok) && $register_ok): ?>
            <div class="error-box" style="background:#f1f8f1; color:#2e7d32; border-color:#2e7d32;">
                <?php echo html_escape($register_ok); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($login_error)): ?>
            <div class="error-box"><?php echo html_escape($login_error); ?></div>
        <?php endif; ?>

        <?php echo form_open('auth/login'); ?>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="text" name="email" class="form-control"
                       placeholder="your.email@cu.edu.ph"
                       value="<?php echo set_value('email'); ?>">
                <span class="field-error"><?php echo form_error('email'); ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password">
                <span class="field-error"><?php echo form_error('password'); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rem">
                    <label class="form-check-label small" for="rem">Remember me</label>
                </div>
                <a href="#" class="text-danger small text-decoration-none">Forgot Password?</a>
            </div>
            <button type="submit" class="btn-main">Sign In</button>
        <?php echo form_close(); ?>

        <div class="demo-box">
            <h6>Demo Credentials</h6>
            <b>Student:</b> botsai@g.cu.edu.ph / botsai123<br>
            <b>Admin:</b> admin@g.cu.edu.ph / admin123
        </div>

        <div class="text-center mt-3">
            <a href="<?php echo site_url('/'); ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Back to Homepage</a>
        </div>
    </div>

</div>

<script src="<?php echo base_url('assets/js/login.js'); ?>"></script>