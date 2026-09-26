(function () {
    'use strict';

    function selectRole(role) {
        var roleInput  = document.getElementById('roleInput');
        var pillStu    = document.getElementById('pill-student');
        var pillStaff  = document.getElementById('pill-staff');
        var studentId  = document.getElementById('studentId');
        var department = document.getElementById('department');
        var staffNote  = document.getElementById('staffNote');
        var wrapStu    = document.getElementById('wrap-student-id');
        var wrapDept   = document.getElementById('wrap-department');
        if (!roleInput || !pillStu || !pillStaff) return;

        roleInput.value = role;

        pillStu.classList.toggle('active', role === 'student');
        pillStaff.classList.toggle('active', role === 'staff');

        var isStaff = role === 'staff';

        if (studentId)  studentId.disabled = isStaff;
        if (department) department.disabled = isStaff;

        if (isStaff) {
            if (studentId)  studentId.value = '';
            if (department) department.value = '';
        }

        if (wrapStu) wrapStu.classList.toggle('locked', isStaff);
        if (wrapDept) wrapDept.classList.toggle('locked', isStaff);

        if (staffNote) staffNote.classList.toggle('show', isStaff);
    }

    function goToLogin() {
        var overlay = document.getElementById('coinOverlay');
        if (!overlay) return;
        var coin = overlay.querySelector('.coin');
        if (coin) {
            coin.style.animation = 'none';
            coin.offsetHeight;
            coin.style.animation = '';
        }
        overlay.classList.add('show');
        setTimeout(function () {
            window.location.href = 'login.php';
        }, 950);
    }

    window.selectRole = selectRole;
    window.goToLogin  = goToLogin;
})();