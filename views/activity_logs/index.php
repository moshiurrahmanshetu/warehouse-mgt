<?php
require_once INCLUDE_PATH . '/header.php';
require_once INCLUDE_PATH . '/navbar.php';
?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= e(APP_URL) ?>/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="bi bi-activity me-2 text-primary"></i>Activity Logs</h2>
                <p class="text-muted mb-0" style="font-size:13px;">Audit trail and chronological event log of user actions across the system.</p>
            </div>
            <div>
                <span class="wms-badge badge-system px-3 py-2" style="font-size:12px;">
                    <i class="bi bi-database me-1"></i> Total Records: <strong><?= number_format($totalRecords) ?></strong>
                </span>
            </div>
        </div>

        <?php renderFlash(); ?>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
            <div class="card-body p-3">
                <form action="<?= e(APP_URL) ?>/activity_logs.php" method="GET" class="row g-2 align-items-end">
                    
                    <!-- Search Input -->
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">Search Keyword</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:var(--border-color);color:var(--text-muted);"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Action, module, description, IP..." value="<?= e($filters['search']) ?>">
                        </div>
                    </div>

                    <!-- User Filter -->
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">User</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            <?php foreach ($usersList as $u): ?>
                                <option value="<?= (int)$u['id'] ?>" <?= ((string)$filters['user_id'] === (string)$u['id']) ? 'selected' : '' ?>>
                                    <?= e($u['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Module Filter -->
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">Module</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="">All Modules</option>
                            <?php foreach ($modules as $m): ?>
                                <option value="<?= e($m) ?>" <?= ($filters['module'] === $m) ? 'selected' : '' ?>>
                                    <?= ucfirst(e($m)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Action Filter -->
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">Action</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">All Actions</option>
                            <?php foreach ($actions as $act): ?>
                                <option value="<?= e($act) ?>" <?= ($filters['action'] === $act) ? 'selected' : '' ?>>
                                    <?= e($act) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-3">
                        <div class="row g-1">
                            <div class="col-6">
                                <label class="form-label" style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($filters['date_from']) ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($filters['date_to']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 text-end mt-2">
                        <button type="submit" class="btn btn-primary btn-sm px-3 me-1">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        <a href="<?= e(APP_URL) ?>/activity_logs.php" class="btn btn-outline-secondary btn-sm px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <!-- Logs Table Card -->
        <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="wms-table mb-0" id="activityLogsTable">
                        <thead>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th style="width:160px;">Date &amp; Time</th>
                                <th style="width:200px;">User</th>
                                <th style="width:140px;">Action</th>
                                <th style="width:130px;">Module</th>
                                <th>Description</th>
                                <th style="width:120px;">IP Address</th>
                                <th style="width:80px;" class="text-end">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5" style="color:var(--text-muted);">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                        No activity log entries found matching your criteria.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $i => $row): ?>
                                    <?php
                                    $actionLower = strtolower($row['action']);
                                    $badgeClass = 'badge-system';
                                    if (str_contains($actionLower, 'create') || str_contains($actionLower, 'login') || str_contains($actionLower, 'activate') || str_contains($actionLower, 'restore')) {
                                        $badgeClass = 'badge-active';
                                    } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'logout') || str_contains($actionLower, 'deactivate')) {
                                        $badgeClass = 'badge-banned';
                                    } elseif (str_contains($actionLower, 'update') || str_contains($actionLower, 'edit') || str_contains($actionLower, 'password') || str_contains($actionLower, 'setting')) {
                                        $badgeClass = 'badge-admin';
                                    }
                                    ?>
                                    <tr>
                                        <td style="color:var(--text-muted);font-size:12px;"><?= (int)$offset + $i + 1 ?></td>
                                        <td style="font-size:12.5px;white-space:nowrap;">
                                            <i class="bi bi-clock me-1 text-muted"></i>
                                            <?= formatDate($row['created_at']) ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['user_name'])): ?>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($row['user_avatar']) && file_exists(BASEPATH . '/' . $row['user_avatar'])): ?>
                                                        <img src="<?= e(APP_URL . '/' . $row['user_avatar']) ?>" alt="Avatar" class="rounded-circle" width="26" height="26" style="object-fit:cover;">
                                                    <?php else: ?>
                                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:26px;height:26px;font-size:11px;background:linear-gradient(135deg, var(--primary), var(--secondary));">
                                                            <?= strtoupper(mb_substr($row['user_name'], 0, 1)) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div style="font-size:13px;font-weight:600;color:var(--text-primary);line-height:1.2;"><?= e($row['user_name']) ?></div>
                                                        <small style="font-size:11px;color:var(--text-muted);"><?= e($row['user_email'] ?? '') ?></small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic" style="font-size:12.5px;">System / Anonymous</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="wms-badge <?= $badgeClass ?>">
                                                <?= e($row['action']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background:rgba(255,255,255,0.07);color:var(--text-secondary);font-size:11.5px;font-weight:500;">
                                                <i class="bi bi-folder2 me-1"></i><?= ucfirst(e($row['module'])) ?>
                                            </span>
                                        </td>
                                        <td style="color:var(--text-secondary);font-size:13px;">
                                            <?= e(truncate($row['description'] ?? '—', 85)) ?>
                                        </td>
                                        <td style="font-family:monospace;font-size:12px;color:var(--text-muted);">
                                            <?= e($row['ip_address'] ?? '—') ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= e(APP_URL) ?>/activity_logs.php?action=details&id=<?= (int)$row['id'] ?>" class="btn btn-outline-secondary btn-sm" title="View Full Details" style="padding:2px 8px;font-size:12px;">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Footer -->
            <?php if ($totalRecords > $limit): ?>
                <div class="card-footer bg-transparent border-top p-3" style="border-color:var(--border-color)!important;">
                    <?= renderPagination($totalRecords, $limit, $page, 'activity_logs.php') ?>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php require_once INCLUDE_PATH . '/footer.php'; ?>
