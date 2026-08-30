/**
 * Warehouse Management System — Installer JS
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // ─── Test Database Connection via AJAX ────────────────────────────────
    const testDbBtn = document.getElementById('btnTestDb');
    const testDbResult = document.getElementById('testDbResult');

    if (testDbBtn) {
        testDbBtn.addEventListener('click', function () {
            const host = document.getElementById('db_host')?.value || '127.0.0.1';
            const port = document.getElementById('db_port')?.value || '3306';
            const name = document.getElementById('db_name')?.value || '';
            const user = document.getElementById('db_user')?.value || '';
            const pass = document.getElementById('db_pass')?.value || '';

            if (!name) {
                if (testDbResult) {
                    testDbResult.className = 'alert alert-danger mt-3';
                    testDbResult.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Please enter a Database Name first.';
                    testDbResult.classList.remove('d-none');
                }
                return;
            }

            testDbBtn.disabled = true;
            testDbBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testing Connection...';
            if (testDbResult) testDbResult.classList.add('d-none');

            const formData = new FormData();
            formData.append('action', 'test_db');
            formData.append('db_host', host);
            formData.append('db_port', port);
            formData.append('db_name', name);
            formData.append('db_user', user);
            formData.append('db_pass', pass);

            fetch('install.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                testDbBtn.disabled = false;
                testDbBtn.innerHTML = '<i class="bi bi-plugin me-1"></i> Test Connection';

                if (testDbResult) {
                    testDbResult.classList.remove('d-none');
                    if (data.success) {
                        testDbResult.className = 'alert alert-success mt-3';
                        testDbResult.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + (data.message || 'Database connection successful!');
                    } else {
                        testDbResult.className = 'alert alert-danger mt-3';
                        testDbResult.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>' + (data.message || 'Connection failed.');
                    }
                }
            })
            .catch(err => {
                testDbBtn.disabled = false;
                testDbBtn.innerHTML = '<i class="bi bi-plugin me-1"></i> Test Connection';
                if (testDbResult) {
                    testDbResult.classList.remove('d-none');
                    testDbResult.className = 'alert alert-danger mt-3';
                    testDbResult.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Failed to communicate with test service.';
                }
            });
        });
    }

    // ─── Password Visibility Toggle ─────────────────────────────────────────
    document.querySelectorAll('.input-toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.dataset.target;
            const input = document.getElementById(targetId);
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

    // ─── SQL Import Source Toggle ──────────────────────────────────────────
    const sqlSourceDefault = document.getElementById('sqlSourceDefault');
    const sqlSourceUpload = document.getElementById('sqlSourceUpload');
    const customUploadWrapper = document.getElementById('customUploadWrapper');

    if (sqlSourceDefault && sqlSourceUpload && customUploadWrapper) {
        function toggleUploadField() {
            if (sqlSourceUpload.checked) {
                customUploadWrapper.classList.remove('d-none');
            } else {
                customUploadWrapper.classList.add('d-none');
            }
        }
        sqlSourceDefault.addEventListener('change', toggleUploadField);
        sqlSourceUpload.addEventListener('change', toggleUploadField);
    }
});
