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
                <li class="breadcrumb-item"><a href="<?= e(APP_URL) ?>/activity_logs.php">Activity Logs</a></li>
                <li class="breadcrumb-item active" aria-current="page">Log #<?= (int)$log['id'] ?></li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Activity Log Details</h2>
                <p class="text-muted mb-0" style="font-size:13px;">Full audit details for Event #<?= (int)$log['id'] ?>.</p>
            </div>
            <div>
                <a href="<?= e(APP_URL) ?>/activity_logs.php" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Activity Logs
                </a>
            </div>
        </div>

        <?php renderFlash(); ?>

        <div class="row g-4">

            <!-- Left Summary Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                    <div class="card-header p-3 border-bottom" style="background:transparent;border-color:var(--border-color)!important;">
                        <h6 class="mb-0 fw-bold" style="color:var(--text-primary);"><i class="bi bi-person-badge me-2 text-primary"></i>Acting User</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <?php if (!empty($log['user_name'])): ?>
                            <div class="mb-3">
                                <?php if (!empty($log['user_avatar']) && file_exists(BASEPATH . '/' . $log['user_avatar'])): ?>
                                    <img src="<?= e(APP_URL . '/' . $log['user_avatar']) ?>" alt="Avatar" class="rounded-circle border" width="80" height="80" style="object-fit:cover;">
                                <?php else: ?>
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width:80px;height:80px;font-size:32px;background:linear-gradient(135deg, var(--primary), var(--secondary));">
                                        <?= strtoupper(mb_substr($log['user_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h5 class="fw-bold mb-1" style="color:var(--text-primary);"><?= e($log['user_name']) ?></h5>
                            <p class="text-muted mb-3" style="font-size:13px;"><?= e($log['user_email'] ?? '') ?></p>

                            <div class="text-start" style="font-size:13px;">
                                <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:var(--border-color)!important;">
                                    <span class="text-muted">User ID</span>
                                    <span class="fw-semibold" style="color:var(--text-primary);"><?= (int)$log['user_id'] ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Phone</span>
                                    <span class="fw-semibold" style="color:var(--text-primary);"><?= !empty($log['user_phone']) ? e($log['user_phone']) : '—' ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-robot fs-1 d-block mb-2 opacity-50"></i>
                                <span class="fw-semibold">System Generated / Anonymous</span>
                                <p class="small mb-0 mt-1">Action performed by background system or unauthenticated visitor.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Details Card -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                    <div class="card-header p-3 border-bottom" style="background:transparent;border-color:var(--border-color)!important;">
                        <h6 class="mb-0 fw-bold" style="color:var(--text-primary);"><i class="bi bi-info-circle me-2 text-primary"></i>Event Information</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="text-muted d-block small mb-1">Event Log ID</label>
                                <div class="fw-bold fs-5" style="color:var(--text-primary);">#<?= (int)$log['id'] ?></div>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted d-block small mb-1">Timestamp</label>
                                <div class="fw-semibold" style="color:var(--text-primary);">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>
                                    <?= formatDate($log['created_at']) ?>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted d-block small mb-1">Action Performed</label>
                                <div>
                                    <span class="wms-badge badge-active fs-6 px-3 py-1">
                                        <?= e($log['action']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted d-block small mb-1">Module / Resource</label>
                                <div>
                                    <span class="wms-badge badge-system fs-6 px-3 py-1">
                                        <i class="bi bi-folder2 me-1"></i><?= ucfirst(e($log['module'])) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted d-block small mb-1">Client IP Address</label>
                                <div class="font-monospace" style="color:var(--text-primary);">
                                    <i class="bi bi-globe2 me-1 text-info"></i>
                                    <?= e($log['ip_address'] ?? '—') ?>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted d-block small mb-1">Audit Policy</label>
                                <div style="color:var(--text-secondary);font-size:13px;">
                                    <i class="bi bi-shield-check text-success me-1"></i> Read-only immutable audit record
                                </div>
                            </div>
                        </div>

                        <hr style="border-color:var(--border-color);">

                        <!-- Description Box -->
                        <div class="mb-4">
                            <label class="text-muted d-block small mb-2 fw-semibold">Action Description</label>
                            <div class="p-3 rounded" style="background:rgba(255,255,255,0.03);border:1px solid var(--border-color);color:var(--text-primary);font-size:14px;line-height:1.6;">
                                <?= nl2br(e($log['description'] ?? 'No additional description provided.')) ?>
                            </div>
                        </div>

                        <!-- User Agent Box -->
                        <div>
                            <label class="text-muted d-block small mb-2 fw-semibold">Client User Agent</label>
                            <div class="p-2 rounded font-monospace" style="background:rgba(0,0,0,0.2);border:1px solid var(--border-color);font-size:12px;color:var(--text-secondary);word-break:break-all;">
                                <?= e($log['user_agent'] ?? '—') ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </main>
</div>

<?php require_once INCLUDE_PATH . '/footer.php'; ?>
