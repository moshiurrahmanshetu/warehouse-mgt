<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="roles.php">Roles</a></li>
                <li class="breadcrumb-item active">View Role</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-shield-lock me-2"></i>Role Details</h2>
            <div>
                <?php if (hasPermission('roles.manage')): ?>
                    <a href="roles.php?action=edit&id=<?= (int)$role['id'] ?>" class="btn btn-warning btn-sm me-2">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                <?php endif; ?>
                <a href="roles.php" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>

        <?php renderFlash(); ?>

        <div class="card shadow-sm">
            <div class="card-header bg-light fw-semibold">
                <i class="bi bi-shield me-1"></i> Role Information
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Role Name</dt>
                    <dd class="col-sm-9">
                        <?= e($role['name']) ?>
                        <?php if (!empty($role['is_system'])): ?>
                            <span class="badge bg-dark ms-2">System</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-3">Slug</dt>
                    <dd class="col-sm-9"><code><?= e($role['slug']) ?></code></dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9"><?= e($role['description'] ?? '—') ?></dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-<?= $role['is_active'] ? 'success' : 'secondary' ?>">
                            <?= $role['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </dd>

                    <dt class="col-sm-3">Created</dt>
                    <dd class="col-sm-9">
                        <?= !empty($role['created_at']) ? e(date('d M Y', strtotime($role['created_at']))) : '—' ?>
                    </dd>
                </dl>
            </div>
        </div>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
