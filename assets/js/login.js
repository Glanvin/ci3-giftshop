(function () {
    'use strict';

    function goToSignup() {
        var overlay = document.getElementById('flipOverlay');
        if (!overlay) return;
        overlay.classList.add('show');
        setTimeout(function () {
            window.location.href = overlay.getAttribute('data-redirect') || 'auth/signup';
        }, 950);
    }

    window.goToSignup = goToSignup;
})();