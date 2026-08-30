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
    <!-- Brand & Toggle Header -->
    <div class="sidebar-header">
        <a href="<?= e(APP_URL) ?>/dashboard.php" class="sidebar-brand">
            <div class="brand-icon">📦</div>
            <div class="brand-text">
                <span class="brand-title">WMS Pro</span>
                <span class="brand-subtitle">Warehouse System</span>
            </div>
        </a>
        <button type="button" class="sidebar-toggle-btn d-none d-lg-flex" id="sidebarCollapseBtn" aria-label="Toggle Sidebar" title="Collapse / Expand Sidebar">
            <i class="bi bi-chevron-left toggle-icon"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav" role="navigation" aria-label="Main Navigation">
        <div class="nav-section-title">Main</div>

        <a href="<?= e(APP_URL) ?>/dashboard.php"
           class="sidebar-nav-item <?= activeClass('dashboard') ?>"
           id="nav-dashboard"
           data-tooltip="Dashboard">
            <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
            <span class="nav-text">Dashboard</span>
        </a>

        <div class="nav-section-title">Administration</div>

        <a href="<?= e(APP_URL) ?>/users.php"
           class="sidebar-nav-item <?= activeClass('users') ?>"
           id="nav-users"
           data-tooltip="Users">
            <span class="nav-icon"><i class="bi bi-people"></i></span>
            <span class="nav-text">Users</span>
        </a>

        <a href="<?= e(APP_URL) ?>/roles.php"
           class="sidebar-nav-item <?= activeClass('roles') ?>"
           id="nav-roles"
           data-tooltip="Roles &amp; Permissions">
            <span class="nav-icon"><i class="bi bi-shield-lock"></i></span>
            <span class="nav-text">Roles &amp; Permissions</span>
        </a>

        <?php if(hasPermission('warehouses.view') || hasPermission('zones.view') || hasPermission('racks.view') || hasPermission('shelves.view') || hasPermission('bins.view')): ?>
        <div class="nav-section-title">Warehouse Management</div>
        <?php endif; ?>

        <?php if(hasPermission('warehouses.view')): ?>
        <a href="<?= e(APP_URL) ?>/warehouses.php"
           class="sidebar-nav-item <?= activeClass('warehouses') ?>"
           id="nav-warehouses"
           data-tooltip="Warehouses">
            <span class="nav-icon"><i class="bi bi-building"></i></span>
            <span class="nav-text">Warehouses</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('zones.view')): ?>
        <a href="<?= e(APP_URL) ?>/zones.php"
           class="sidebar-nav-item <?= activeClass('zones') ?>"
           id="nav-zones"
           data-tooltip="Zones">
            <span class="nav-icon"><i class="bi bi-geo-alt"></i></span>
            <span class="nav-text">Zones</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('racks.view')): ?>
        <a href="<?= e(APP_URL) ?>/racks.php"
           class="sidebar-nav-item <?= activeClass('racks') ?>"
           id="nav-racks"
           data-tooltip="Racks">
            <span class="nav-icon"><i class="bi bi-columns"></i></span>
            <span class="nav-text">Racks</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('shelves.view')): ?>
        <a href="<?= e(APP_URL) ?>/shelves.php"
           class="sidebar-nav-item <?= activeClass('shelves') ?>"
           id="nav-shelves"
           data-tooltip="Shelves">
            <span class="nav-icon"><i class="bi bi-layout-three-columns"></i></span>
            <span class="nav-text">Shelves</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('bins.view')): ?>
        <a href="<?= e(APP_URL) ?>/bins.php"
           class="sidebar-nav-item <?= activeClass('bins') ?>"
           id="nav-bins"
           data-tooltip="Bins">
            <span class="nav-icon"><i class="bi bi-box"></i></span>
            <span class="nav-text">Bins</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('suppliers.view') || hasPermission('customers.view')): ?>
        <div class="nav-section-title">Master Data</div>
        <?php endif; ?>

        <?php if(hasPermission('suppliers.view')): ?>
        <a href="<?= e(APP_URL) ?>/suppliers.php"
           class="sidebar-nav-item <?= activeClass('suppliers') ?>"
           id="nav-suppliers"
           data-tooltip="Suppliers">
            <span class="nav-icon"><i class="bi bi-truck"></i></span>
            <span class="nav-text">Suppliers</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('customers.view')): ?>
        <a href="<?= e(APP_URL) ?>/customers.php"
           class="sidebar-nav-item <?= activeClass('customers') ?>"
           id="nav-customers"
           data-tooltip="Customers">
            <span class="nav-icon"><i class="bi bi-people"></i></span>
            <span class="nav-text">Customers</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('categories.view') || hasPermission('brands.view') || hasPermission('units.view') || hasPermission('tax_rates.view') || hasPermission('currencies.view') || hasPermission('attributes.view') || hasPermission('product_tags.view')): ?>
        <div class="nav-section-title">Product Master Data</div>
        <?php endif; ?>

        <?php if(hasPermission('categories.view')): ?>
        <a href="<?= e(APP_URL) ?>/categories.php"
           class="sidebar-nav-item <?= activeClass('categories') ?>"
           data-tooltip="Categories">
            <span class="nav-icon"><i class="bi bi-diagram-3"></i></span>
            <span class="nav-text">Categories</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('brands.view')): ?>
        <a href="<?= e(APP_URL) ?>/brands.php"
           class="sidebar-nav-item <?= activeClass('brands') ?>"
           data-tooltip="Brands">
            <span class="nav-icon"><i class="bi bi-award"></i></span>
            <span class="nav-text">Brands</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('units.view')): ?>
        <a href="<?= e(APP_URL) ?>/units.php"
           class="sidebar-nav-item <?= activeClass('units') ?>"
           data-tooltip="Units of Measure">
            <span class="nav-icon"><i class="bi bi-rulers"></i></span>
            <span class="nav-text">Units of Measure</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('tax_rates.view')): ?>
        <a href="<?= e(APP_URL) ?>/tax_rates.php"
           class="sidebar-nav-item <?= activeClass('tax_rates') ?>"
           data-tooltip="Tax Rates">
            <span class="nav-icon"><i class="bi bi-percent"></i></span>
            <span class="nav-text">Tax Rates</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('currencies.view')): ?>
        <a href="<?= e(APP_URL) ?>/currencies.php"
           class="sidebar-nav-item <?= activeClass('currencies') ?>"
           data-tooltip="Currencies">
            <span class="nav-icon"><i class="bi bi-currency-exchange"></i></span>
            <span class="nav-text">Currencies</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('attributes.view')): ?>
        <a href="<?= e(APP_URL) ?>/attributes.php"
           class="sidebar-nav-item <?= activeClass('attributes') ?>"
           data-tooltip="Attributes">
            <span class="nav-icon"><i class="bi bi-sliders"></i></span>
            <span class="nav-text">Attributes</span>
        </a>
        <?php endif; ?>

        <?php if(hasPermission('product_tags.view')): ?>
        <a href="<?= e(APP_URL) ?>/product_tags.php"
           class="sidebar-nav-item <?= activeClass('product_tags') ?>"
           data-tooltip="Product Tags">
            <span class="nav-icon"><i class="bi bi-tags"></i></span>
            <span class="nav-text">Product Tags</span>
        </a>
        <?php endif; ?>

        <div class="nav-section-title">System</div>

        <?php if (hasPermission('logs.view') || hasPermission('activity_logs.view')): ?>
        <a href="<?= e(APP_URL) ?>/activity_logs.php"
           class="sidebar-nav-item <?= activeClass('activity') ?>"
           id="nav-activity"
           data-tooltip="Activity Logs">
            <span class="nav-icon"><i class="bi bi-activity"></i></span>
            <span class="nav-text">Activity Logs</span>
        </a>
        <?php endif; ?>

        <?php if (hasPermission('settings.view') || hasPermission('settings.manage')): ?>
        <a href="<?= e(APP_URL) ?>/settings.php"
           class="sidebar-nav-item <?= activeClass('settings') ?>"
           id="nav-settings"
           data-tooltip="Settings">
            <span class="nav-icon"><i class="bi bi-gear"></i></span>
            <span class="nav-text">Settings</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer / User -->
    <div class="sidebar-footer">
        <a href="<?= e(APP_URL) ?>/profile.php" class="sidebar-user" title="View Profile" data-tooltip="<?= e($user['name'] ?? 'My Profile') ?>">
            <?php if (!empty($user['avatar']) && file_exists(BASEPATH . '/' . $user['avatar'])): ?>
                <img src="<?= e(APP_URL . '/' . $user['avatar']) ?>" alt="Avatar" class="user-avatar" style="object-fit:cover;">
            <?php else: ?>
                <div class="user-avatar"><?= e($initials ?: '?') ?></div>
            <?php endif; ?>
            <div class="user-info">
                <div class="user-name"><?= e($user['name'] ?? 'User') ?></div>
                <div class="user-role"><?= e(implode(', ', array_map('ucfirst', $_SESSION['roles'] ?? ['user']))) ?></div>
            </div>
        </a>
    </div>
</aside>

<!-- Mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
