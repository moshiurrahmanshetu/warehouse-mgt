<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard &mdash; <?= e(APP_NAME) ?></title>
    <meta name="description" content="Warehouse Management System Dashboard — real-time overview of your warehouse.">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- App Styles -->
    <link rel="stylesheet" href="<?= e(ASSET_PATH) ?>/css/style.css">

    <script>
        window.WMS_SESSION_TIMEOUT = <?= (int) SESSION_TIMEOUT ?>;
        window.WMS_LOGOUT_URL      = '<?= e(APP_URL) ?>/logout.php';
    </script>
</head>
<body class="authenticated">
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
            <div class="page-header d-flex flex-wrap align-items-start justify-content-between gap-3 animate-in">
                <div>
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Welcome back, <?= e($data['user_name'] ?? currentUser()['name'] ?? 'Administrator') ?>. Here's what's happening.</p>
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

            <!-- Stat Cards -->
            <div class="row g-3 mb-4">

                <div class="col-sm-6 col-xl-3 animate-in">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= (int)($data['total_users'] ?? 0) ?></div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i><?= (int)($data['active_users'] ?? 0) ?> active</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3 animate-in">
                    <div class="stat-card stat-secondary">
                        <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= (int)($data['total_roles'] ?? 0) ?></div>
                            <div class="stat-label">System Roles</div>
                            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>All active</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3 animate-in">
                    <div class="stat-card stat-success">
                        <div class="stat-icon"><i class="bi bi-key-fill"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= (int)($data['total_permissions'] ?? 0) ?></div>
                            <div class="stat-label">Permissions</div>
                            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>Configured</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3 animate-in">
                    <div class="stat-card stat-warning">
                        <div class="stat-icon"><i class="bi bi-activity"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= count($data['recent_logs'] ?? []) ?></div>
                            <div class="stat-label">Recent Activities</div>
                            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>Last 10 events</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Activity Log Table -->
            <div class="row g-3">
                <div class="col-12 animate-in">
                    <div class="wms-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h2 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0">Recent Activity</h2>
                                <p style="font-size:12px;color:var(--text-muted);margin:0">Latest system events logged</p>
                            </div>
                            <span class="wms-badge badge-system"><i class="bi bi-clock me-1"></i>Live</span>
                        </div>

                        <?php if (!empty($data['recent_logs'])): ?>
                        <div class="table-responsive">
                            <table class="wms-table" id="activityTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Module</th>
                                        <th>IP Address</th>
                                        <th>Date &amp; Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($data['recent_logs'] as $i => $log): ?>
                                    <tr>
                                        <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                                        <td>
                                            <div style="font-weight:600;color:var(--text-primary)"><?= e($log['user_name'] ?? 'System') ?></div>
                                        </td>
                                        <td><span class="wms-badge badge-active"><?= e($log['action']) ?></span></td>
                                        <td><span class="wms-badge badge-admin"><?= e($log['module']) ?></span></td>
                                        <td style="font-family:monospace;font-size:12px"><?= e($log['ip_address'] ?? '—') ?></td>
                                        <td><?= formatDate($log['created_at']) ?></td>
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
            </div>

        </main>

        <!-- Footer -->
        <?php require_once INCLUDE_PATH . '/footer.php'; ?>
    </div>

</div>

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
