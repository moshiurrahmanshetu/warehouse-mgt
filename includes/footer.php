<?php
/**
 * Footer Include
 */
if (!defined('BASEPATH')) exit('No direct script access');
?>
<footer class="wms-footer">
    <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</span>
    <span>Version <?= e(APP_VERSION) ?> &mdash; <?= e(APP_ENV === 'development' ? '🔧 Development' : '🚀 Production') ?></span>
</footer>
    </div> <!-- close wms-main or wms-wrapper -->
</div> <!-- close outer wrapper (if any) -->

<!-- Session Warning Modal -->
<div class="modal fade" id="sessionWarningModal" tabindex="-1" aria-labelledby="sessionWarningLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius)">
            <div class="modal-header" style="border-color:var(--border-color)">
                <h5 class="modal-title" id="sessionWarningLabel" style="color:var(--text-primary)">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Session Expiring Soon
                </h5>
            </div>
            <div class="modal-body" style="color:var(--text-secondary)">
                Your session will expire in 2 minutes due to inactivity. Click <strong>Stay Logged In</strong> to continue your session.
            </div>
            <div class="modal-footer" style="border-color:var(--border-color)">
                <a href="<?= e(APP_URL) ?>/logout.php" class="btn btn-outline-secondary btn-sm" id="modalLogoutBtn">Log Out</a>
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal"
                        style="background:var(--primary);color:#fff;border:none"
                        id="stayLoggedInBtn">
                    Stay Logged In
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- App JS -->
<script src="<?= e(ASSET_PATH) ?>/js/main.js"></script>
</body>
</html>
