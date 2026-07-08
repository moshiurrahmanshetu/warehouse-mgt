<?php
/**
 * Sidebar Include
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
<aside class="wms-sidebar" id="wmsSidebar">
    <!-- Brand -->
    <a href="<?= e(APP_URL) ?>/dashboard.php" class="sidebar-brand">
        <div class="brand-icon">📦</div>
        <div class="brand-text">
            <span class="brand-title">WMS Pro</span>
            <span class="brand-subtitle">Warehouse System</span>
        </div>
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav" role="navigation" aria-label="Main Navigation">
        <div class="nav-section-title">Main</div>

        <a href="<?= e(APP_URL) ?>/dashboard.php"
           class="sidebar-nav-item <?= activeClass('dashboard') ?>"
           id="nav-dashboard">
            <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
            Dashboard
        </a>

        <div class="nav-section-title">Administration</div>

        <a href="#"
           class="sidebar-nav-item <?= activeClass('users') ?>"
           id="nav-users">
            <span class="nav-icon"><i class="bi bi-people"></i></span>
            Users
            <span class="nav-badge">Soon</span>
        </a>

        <a href="#"
           class="sidebar-nav-item <?= activeClass('roles') ?>"
           id="nav-roles">
            <span class="nav-icon"><i class="bi bi-shield-lock"></i></span>
            Roles &amp; Permissions
            <span class="nav-badge">Soon</span>
        </a>

        <?php if(hasPermission('warehouses.view') || hasPermission('zones.view') || hasPermission('racks.view') || hasPermission('shelves.view') || hasPermission('bins.view')): ?>
        <div class="nav-section-title">Warehouse Management</div>
        <?php endif; ?>

        <?php if(hasPermission('warehouses.view')): ?>
        <a href="<?= e(APP_URL) ?>/warehouses.php"
           class="sidebar-nav-item <?= activeClass('warehouses') ?>"
           id="nav-warehouses">
            <span class="nav-icon"><i class="bi bi-building"></i></span>
            Warehouses
        </a>
        <?php endif; ?>

        <?php if(hasPermission('zones.view')): ?>
        <a href="<?= e(APP_URL) ?>/zones.php"
           class="sidebar-nav-item <?= activeClass('zones') ?>"
           id="nav-zones">
            <span class="nav-icon"><i class="bi bi-geo-alt"></i></span>
            Zones
        </a>
        <?php endif; ?>

        <?php if(hasPermission('racks.view')): ?>
        <a href="<?= e(APP_URL) ?>/racks.php"
           class="sidebar-nav-item <?= activeClass('racks') ?>"
           id="nav-racks">
            <span class="nav-icon"><i class="bi bi-columns"></i></span>
            Racks
        </a>
        <?php endif; ?>

        <?php if(hasPermission('shelves.view')): ?>
        <a href="<?= e(APP_URL) ?>/shelves.php"
           class="sidebar-nav-item <?= activeClass('shelves') ?>"
           id="nav-shelves">
            <span class="nav-icon"><i class="bi bi-layout-three-columns"></i></span>
            Shelves
        </a>
        <?php endif; ?>

        <?php if(hasPermission('bins.view')): ?>
        <a href="<?= e(APP_URL) ?>/bins.php"
           class="sidebar-nav-item <?= activeClass('bins') ?>"
           id="nav-bins">
            <span class="nav-icon"><i class="bi bi-box"></i></span>
            Bins
        </a>
        <?php endif; ?>

        <div class="nav-section-title">System</div>

        <a href="#"
           class="sidebar-nav-item <?= activeClass('activity') ?>"
           id="nav-activity">
            <span class="nav-icon"><i class="bi bi-activity"></i></span>
            Activity Logs
            <span class="nav-badge">Soon</span>
        </a>

        <a href="#"
           class="sidebar-nav-item <?= activeClass('settings') ?>"
           id="nav-settings">
            <span class="nav-icon"><i class="bi bi-gear"></i></span>
            Settings
            <span class="nav-badge">Soon</span>
        </a>
    </nav>

    <!-- Sidebar Footer / User -->
    <div class="sidebar-footer">
        <a href="#" class="sidebar-user">
            <div class="user-avatar"><?= e($initials ?: '?') ?></div>
            <div class="user-info">
                <div class="user-name"><?= e($user['name'] ?? 'User') ?></div>
                <div class="user-role"><?= e(implode(', ', array_map('ucfirst', $_SESSION['roles'] ?? ['user']))) ?></div>
            </div>
        </a>
    </div>
</aside>

<!-- Mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
