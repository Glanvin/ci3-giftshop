<?php echo render_notifications(); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.notification-area .alert[data-auto-dismiss]').forEach(function (alert) {
        var delay = parseInt(alert.getAttribute('data-auto-dismiss'), 10) || 5000;
        window.setTimeout(function () {
            if (!document.body.contains(alert)) return;
            if (window.bootstrap && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
                return;
            }
            alert.classList.remove('show');
            window.setTimeout(function () {
                if (alert.parentNode) alert.parentNode.removeChild(alert);
            }, 200);
        }, delay);
    });
});
</script>
