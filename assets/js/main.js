(function () {
    'use strict';

    var CART_KEY = 'universityGiftshopCart';

    // The cart badge is rendered server-side (#cart-count is authored in the
    // header templates). updateHeaderCount is kept as a no-op so the old
    // localStorage cart logic never resets the server-rendered count.
    function updateHeaderCount() {
        return;
    }

    function toggleDropdown(e) {
        var btn = e && e.target ? e.target.closest('.profile-btn') : null;
        if (btn) e.stopPropagation();
        var dropdown = document.getElementById('profileDropdown');
        if (dropdown) dropdown.classList.toggle('show');
    }

    function showLogoutModal() {
        var overlay = document.getElementById('logoutOverlay');
        if (overlay) overlay.classList.add('show');
    }

    function closeLogoutModal() {
        var overlay = document.getElementById('logoutOverlay');
        if (overlay) overlay.classList.remove('show');
    }

    function dismissToast() {
        var t = document.getElementById('cartToast');
        if (t) {
            t.classList.add('toast-hide');
            setTimeout(function () { if (t.parentNode) t.parentNode.removeChild(t); }, 300);
        }
    }

    function changeQty(change) {
        var wrap  = document.querySelector('.qty-wrap[data-max]');
        if (!wrap) return;
        var maxStock = parseInt(wrap.getAttribute('data-max'), 10) || 1;
        var display  = document.getElementById('qty-display');
        var input    = document.getElementById('qty-value');
        if (!display || !input) return;
        var current = parseInt(display.textContent, 10) + change;
        if (current < 1) current = 1;
        if (current > maxStock) current = maxStock;
        display.textContent = current;
        input.value = current;
    }

    document.addEventListener('click', function (e) {
        var dd = document.querySelector('.profile-dropdown');
        if (dd && !dd.contains(e.target)) {
            var dropdown = document.getElementById('profileDropdown');
            if (dropdown) dropdown.classList.remove('show');
        }
    });

    document.addEventListener('DOMContentLoaded', updateHeaderCount);

    window.toggleDropdown    = toggleDropdown;
    window.showLogoutModal   = showLogoutModal;
    window.closeLogoutModal  = closeLogoutModal;
    window.updateHeaderCount = updateHeaderCount;
    window.dismissToast      = dismissToast;
    window.changeQty         = changeQty;
    window.CART_KEY          = CART_KEY;
})();