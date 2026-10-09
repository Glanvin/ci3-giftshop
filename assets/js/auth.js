(function () {
    'use strict';

    function flipTo(url) {
        var overlay = document.getElementById('flipOverlay');
        if (!overlay) {
            window.location.href = url;
            return false;
        }
        overlay.classList.add('show');
        setTimeout(function () {
            window.location.href = url;
        }, 950);
        return false;
    }

    window.flipTo = flipTo;
})();
