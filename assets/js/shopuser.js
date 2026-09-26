(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var checkAll = document.getElementById('checkAll');
        if (checkAll) {
            var items = document.querySelectorAll('.item-check');
            checkAll.addEventListener('change', function () {
                items.forEach(function (cb) { cb.checked = checkAll.checked; });
            });
        }
    });
})();