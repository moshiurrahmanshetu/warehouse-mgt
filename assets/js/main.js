/**
 * Warehouse Management System — Main JS
 */

(function () {
    'use strict';

    // ── Sidebar Toggle (mobile) ────────────────────────────────────────────
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebar        = document.getElementById('wmsSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar?.classList.add('open');
        sidebarOverlay?.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar?.classList.remove('open');
        sidebarOverlay?.classList.remove('open');
        document.body.style.overflow = '';
    }

    sidebarToggle?.addEventListener('click', openSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);

    // ── Password Visibility Toggle ─────────────────────────────────────────
    document.querySelectorAll('.input-toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.dataset.target;
            const input    = document.getElementById(targetId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });
    });

    // ── Auto-dismiss Alerts ────────────────────────────────────────────────
    setTimeout(function () {
        document.querySelectorAll('.alert.alert-dismissible').forEach(function (alert) {
            const bsAlert = bootstrap?.Alert?.getOrCreateInstance(alert);
            bsAlert?.close();
        });
    }, 5000);

    // ── Session Timeout Warning ────────────────────────────────────────────
    const SESSION_TIMEOUT_MS = (typeof WMS_SESSION_TIMEOUT !== 'undefined')
        ? WMS_SESSION_TIMEOUT * 1000
        : 3600000;

    const WARNING_BEFORE_MS = 120000; // warn 2 minutes before expiry

    let warnTimer;
    let logoutTimer;

    function resetTimers() {
        clearTimeout(warnTimer);
        clearTimeout(logoutTimer);

        warnTimer = setTimeout(function () {
            const modal = document.getElementById('sessionWarningModal');
            if (modal && typeof bootstrap !== 'undefined') {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        }, SESSION_TIMEOUT_MS - WARNING_BEFORE_MS);

        logoutTimer = setTimeout(function () {
            window.location.href = window.WMS_LOGOUT_URL || '/warehouse-mgt/logout.php';
        }, SESSION_TIMEOUT_MS);
    }

    if (document.body.classList.contains('authenticated')) {
        resetTimers();
        ['click', 'keydown', 'scroll', 'mousemove'].forEach(function (evt) {
            document.addEventListener(evt, resetTimers, { passive: true });
        });
    }

    // ── Active Nav Link Highlighting ───────────────────────────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-nav-item').forEach(function (link) {
        if (link.getAttribute('href') && currentPath.endsWith(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });

    // ── Animate-in Elements ────────────────────────────────────────────────
    document.querySelectorAll('.animate-in').forEach(function (el, i) {
        el.style.animationDelay = (i * 60) + 'ms';
    });

    // ── Confirm Delete / Destructive Actions ───────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.dataset.confirm || 'Are you sure you want to perform this action?';
            if (!window.confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // ── Tooltip Initialization ─────────────────────────────────────────────
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el, { trigger: 'hover' });
        });
    }

})();
