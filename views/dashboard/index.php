<?php require_once INCLUDE_PATH . '/header.php'; ?>
<div class="wms-wrapper">

    <!-- Sidebar -->
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>

    <!-- Main -->
    <div class="wms-main" id="wmsMain">

        <!-- Navbar -->
        <?php require_once INCLUDE_PATH . '/navbar.php'; ?>

        <!-- Page Content -->
        <main class="wms-content">

            <!-- Page Header + Breadcrumb -->
            <div class="page-header d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Welcome back, <?= e($data['user_name'] ?? currentUser()['name'] ?? 'Administrator') ?>. Here is your real-time operational overview.</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= e(APP_URL) ?>/dashboard.php"><i class="bi bi-house me-1"></i>Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>

            <!-- Flash messages -->
            <?php renderFlash(); ?>

            <!-- Primary Stat Cards (Top Row) -->
            <div class="row g-3 mb-4">

                <!-- Users Card -->
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= (int)($data['total_users'] ?? 0) ?></div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-trend up">
                                <i class="bi bi-check-circle-fill me-1"></i><?= (int)($data['active_users'] ?? 0) ?> active
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warehouses Card -->
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card stat-secondary">
                        <div class="stat-icon"><i class="bi bi-building"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= (int)($data['total_warehouses'] ?? 0) ?></div>
                            <div class="stat-label">Total Warehouses</div>
                            <div class="stat-trend up">
                                <i class="bi bi-geo-alt-fill me-1"></i><?= (int)($data['active_warehouses'] ?? 0) ?> active
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suppliers & Customers Card -->
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card stat-success">
                        <div class="stat-icon"><i class="bi bi-truck"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= (int)($data['total_suppliers'] ?? 0) ?></div>
                            <div class="stat-label">Suppliers</div>
                            <div class="stat-trend up">
                                <i class="bi bi-person-lines-fill me-1"></i><?= (int)($data['total_customers'] ?? 0) ?> customers
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Roles & Security Card -->
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card stat-warning">
                        <div class="stat-icon"><i class="bi bi-shield-lock-fill"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= (int)($data['total_roles'] ?? 0) ?></div>
                            <div class="stat-label">Security Roles</div>
                            <div class="stat-trend up">
                                <i class="bi bi-key-fill me-1"></i><?= (int)($data['total_permissions'] ?? 0) ?> permissions
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Secondary Infrastructure & Master Data Counters Strip -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                        <div class="card-body p-3">
                            <div class="row g-3 text-center">
                                
                                <div class="col-6 col-md-3 col-lg-2 border-end" style="border-color:var(--border-color)!important;">
                                    <small class="text-muted d-block" style="font-size:11.5px;">Zones</small>
                                    <span class="fs-5 fw-bold" style="color:var(--text-primary);"><?= (int)($data['total_zones'] ?? 0) ?></span>
                                </div>

                                <div class="col-6 col-md-3 col-lg-2 border-end" style="border-color:var(--border-color)!important;">
                                    <small class="text-muted d-block" style="font-size:11.5px;">Racks</small>
                                    <span class="fs-5 fw-bold" style="color:var(--text-primary);"><?= (int)($data['total_racks'] ?? 0) ?></span>
                                </div>

                                <div class="col-6 col-md-3 col-lg-2 border-end" style="border-color:var(--border-color)!important;">
                                    <small class="text-muted d-block" style="font-size:11.5px;">Shelves</small>
                                    <span class="fs-5 fw-bold" style="color:var(--text-primary);"><?= (int)($data['total_shelves'] ?? 0) ?></span>
                                </div>

                                <div class="col-6 col-md-3 col-lg-2 border-end" style="border-color:var(--border-color)!important;">
                                    <small class="text-muted d-block" style="font-size:11.5px;">Bins</small>
                                    <span class="fs-5 fw-bold" style="color:var(--text-primary);"><?= (int)($data['total_bins'] ?? 0) ?></span>
                                </div>

                                <div class="col-6 col-md-3 col-lg-2 border-end" style="border-color:var(--border-color)!important;">
                                    <small class="text-muted d-block" style="font-size:11.5px;">Categories</small>
                                    <span class="fs-5 fw-bold" style="color:var(--text-primary);"><?= (int)($data['total_categories'] ?? 0) ?></span>
                                </div>

                                <div class="col-6 col-md-3 col-lg-2">
                                    <small class="text-muted d-block" style="font-size:11.5px;">Brands &amp; Units</small>
                                    <span class="fs-5 fw-bold" style="color:var(--text-primary);"><?= (int)($data['total_brands'] ?? 0) ?> / <?= (int)($data['total_units'] ?? 0) ?></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area: Recent Activity & Quick Summaries -->
            <div class="row g-4">

                <!-- Left Column: Recent Activity Log (10 entries) -->
                <div class="col-lg-8">
                    <div class="wms-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h2 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0">
                                    <i class="bi bi-activity text-primary me-2"></i>Recent Activity
                                </h2>
                                <p style="font-size:12px;color:var(--text-muted);margin:0">Live system audit events</p>
                            </div>
                            <div>
                                <a href="<?= e(APP_URL) ?>/activity_logs.php" class="btn btn-outline-primary btn-sm px-3" style="font-size:12px;">
                                    View All Activity <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($data['recent_logs'])): ?>
                        <div class="table-responsive">
                            <table class="wms-table" id="activityTable">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Module</th>
                                        <th>Description</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($data['recent_logs'] as $log): ?>
                                    <?php
                                    $actionLower = strtolower($log['action']);
                                    $badgeClass = 'badge-system';
                                    if (str_contains($actionLower, 'create') || str_contains($actionLower, 'login') || str_contains($actionLower, 'activate')) {
                                        $badgeClass = 'badge-active';
                                    } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'logout') || str_contains($actionLower, 'deactivate')) {
                                        $badgeClass = 'badge-banned';
                                    } elseif (str_contains($actionLower, 'update') || str_contains($actionLower, 'setting') || str_contains($actionLower, 'password')) {
                                        $badgeClass = 'badge-admin';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;color:var(--text-primary);font-size:13px;">
                                                <?= e($log['user_name'] ?? 'System') ?>
                                            </div>
                                        </td>
                                        <td><span class="wms-badge <?= $badgeClass ?>"><?= e($log['action']) ?></span></td>
                                        <td>
                                            <span class="badge" style="background:rgba(255,255,255,0.06);color:var(--text-secondary);font-size:11px;">
                                                <?= ucfirst(e($log['module'])) ?>
                                            </span>
                                        </td>
                                        <td style="color:var(--text-secondary);font-size:12.5px;">
                                            <?= e(truncate($log['description'] ?? '—', 45)) ?>
                                        </td>
                                        <td style="font-size:12px;color:var(--text-muted);white-space:nowrap;">
                                            <?= formatDate($log['created_at'], 'd M, H:i') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                            <div style="text-align:center;padding:40px;color:var(--text-muted)">
                                <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:12px;opacity:.4"></i>
                                No activity recorded yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Business Summary Quick Cards -->
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-3">

                        <!-- Recent Warehouses -->
                        <div class="wms-card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-bold" style="color:var(--text-primary);font-size:13.5px;">
                                    <i class="bi bi-building me-1 text-secondary"></i> Recently Added Warehouses
                                </h6>
                                <a href="<?= e(APP_URL) ?>/warehouses.php" class="text-muted small text-decoration-none">Manage</a>
                            </div>
                            <?php if (!empty($data['recent_warehouses'])): ?>
                                <ul class="list-unstyled mb-0" style="font-size:12.5px;">
                                    <?php foreach ($data['recent_warehouses'] as $wh): ?>
                                        <li class="py-2 border-bottom d-flex justify-content-between align-items-center" style="border-color:var(--border-color)!important;">
                                            <div>
                                                <div class="fw-semibold" style="color:var(--text-primary);"><?= e($wh['warehouse_name']) ?></div>
                                                <small class="text-muted"><?= e($wh['warehouse_code']) ?></small>
                                            </div>
                                            <span class="wms-badge <?= $wh['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                                <?= ucfirst(e($wh['status'])) ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small mb-0 py-2">No warehouses added yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Recent Suppliers -->
                        <div class="wms-card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-bold" style="color:var(--text-primary);font-size:13.5px;">
                                    <i class="bi bi-truck me-1 text-success"></i> Recent Suppliers
                                </h6>
                                <a href="<?= e(APP_URL) ?>/suppliers.php" class="text-muted small text-decoration-none">Manage</a>
                            </div>
                            <?php if (!empty($data['recent_suppliers'])): ?>
                                <ul class="list-unstyled mb-0" style="font-size:12.5px;">
                                    <?php foreach ($data['recent_suppliers'] as $sup): ?>
                                        <li class="py-2 border-bottom d-flex justify-content-between align-items-center" style="border-color:var(--border-color)!important;">
                                            <div>
                                                <div class="fw-semibold" style="color:var(--text-primary);"><?= e($sup['name']) ?></div>
                                                <small class="text-muted"><?= e($sup['supplier_code'] ?? $sup['phone'] ?? '') ?></small>
                                            </div>
                                            <span class="wms-badge <?= ($sup['status'] ?? 'active') === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                                <?= ucfirst(e($sup['status'] ?? 'active')) ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small mb-0 py-2">No suppliers registered yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Recent Customers -->
                        <div class="wms-card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-bold" style="color:var(--text-primary);font-size:13.5px;">
                                    <i class="bi bi-people me-1 text-warning"></i> Recent Customers
                                </h6>
                                <a href="<?= e(APP_URL) ?>/customers.php" class="text-muted small text-decoration-none">Manage</a>
                            </div>
                            <?php if (!empty($data['recent_customers'])): ?>
                                <ul class="list-unstyled mb-0" style="font-size:12.5px;">
                                    <?php foreach ($data['recent_customers'] as $cust): ?>
                                        <li class="py-2 border-bottom d-flex justify-content-between align-items-center" style="border-color:var(--border-color)!important;">
                                            <div>
                                                <div class="fw-semibold" style="color:var(--text-primary);"><?= e($cust['name']) ?></div>
                                                <small class="text-muted"><?= e($cust['customer_code'] ?? $cust['email'] ?? '') ?></small>
                                            </div>
                                            <span class="wms-badge <?= ($cust['status'] ?? 'active') === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                                <?= ucfirst(e($cust['status'] ?? 'active')) ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small mb-0 py-2">No customers registered yet.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            </div>

        </main>

        <?php require_once INCLUDE_PATH . '/footer.php'; ?>
    </div>
</div>
