<?php
/**
 * Top Navbar Include
 * Rendered on every authenticated page.
 */
if (!defined('BASEPATH')) exit('No direct script access');

$user = currentUser();
$initials = '';
if ($user) {
    $parts = explode(' ', trim($user['name']));
    $initials = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) $initials .= strtoupper(substr(end($parts), 0, 1));
}
?>
<header class="wms-navbar" id="wmsNavbar">
    <!-- Mobile sidebar toggle -->
    <button class="navbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Search -->
    <div class="navbar-search d-none d-md-block">
        <div class="search-input-wrap">
            <i class="bi bi-search"></i>
            <input type="search"
                   class="search-input"
                   id="globalSearch"
                   placeholder="Search..."
                   aria-label="Global search">
        </div>
    </div>

    <!-- Actions -->
    <div class="navbar-actions">
        <!-- Notifications placeholder -->
        <button class="navbar-btn" id="btnNotifications"
                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Notifications"
                aria-label="Notifications">
            <i class="bi bi-bell"></i>
            <span class="badge-dot"></span>
        </button>

        <!-- Full-screen toggle -->
        <button class="navbar-btn" id="btnFullscreen"
                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Toggle Fullscreen"
                aria-label="Toggle fullscreen">
            <i class="bi bi-fullscreen"></i>
        </button>

        <!-- Profile dropdown -->
        <div class="dropdown profile-dropdown" id="profileDropdown">
            <a href="#" class="dropdown-toggle"
               data-bs-toggle="dropdown" aria-expanded="false"
               id="profileMenuToggle">
                <div class="profile-avatar-sm"><?= e($initials ?: '?') ?></div>
                <span class="profile-name d-none d-sm-inline"><?= e($user['name'] ?? 'User') ?></span>
                <i class="bi bi-chevron-down ms-1" style="font-size:11px;color:var(--text-muted)"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <div style="padding:10px 14px;border-bottom:1px solid var(--border-color);margin-bottom:6px">
                        <div style="font-size:13px;font-weight:600;color:var(--text-primary)"><?= e($user['name'] ?? '') ?></div>
                        <div style="font-size:11px;color:var(--text-muted)"><?= e($user['email'] ?? '') ?></div>
                    </div>
                </li>
                <li><a class="dropdown-item" href="#" id="profileLink"><i class="bi bi-person"></i> My Profile</a></li>
                <li><a class="dropdown-item" href="#" id="settingsLink"><i class="bi bi-gear"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger"
                       href="<?= e(APP_URL) ?>/logout.php"
                       id="logoutLink"
                       data-confirm="Are you sure you want to log out?">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
// Fullscreen toggle
document.getElementById('btnFullscreen')?.addEventListener('click', function () {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
        this.innerHTML = '<i class="bi bi-fullscreen-exit"></i>';
    } else {
        document.exitFullscreen?.();
        this.innerHTML = '<i class="bi bi-fullscreen"></i>';
    }
});
</script>
