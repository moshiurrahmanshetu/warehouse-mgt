<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="roles.php">Roles</a></li>
                <li class="breadcrumb-item active">Add Role</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-shield-plus me-2"></i>Add New Role</h2>
            <a href="roles.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <?php renderFlash(); ?>

        <form action="roles.php?action=store" method="POST">
            <?= csrfField() ?>

            <!-- Basic Details -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-shield me-1"></i> Role Details
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-5">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                   placeholder="e.g. Warehouse Manager">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" required
                                   placeholder="e.g. warehouse-manager">
                            <div class="form-text">Lowercase letters, numbers and hyphens only.</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                       id="isActive" checked>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control"
                                   placeholder="Brief description of this role's purpose">
                        </div>

                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-key me-1"></i> Permissions
                </div>
                <div class="card-body">
                    <?php if (empty($groupedPermissions)): ?>
                        <p class="text-muted mb-0">No permissions have been defined yet.</p>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($groupedPermissions as $module => $perms): ?>
                                <div class="col-md-3">
                                    <div class="card h-100 border-secondary-subtle">
                                        <div class="card-header bg-light py-1 fw-semibold text-capitalize small">
                                            <?= e(str_replace('_', ' ', $module)) ?>
                                        </div>
                                        <div class="card-body py-2">
                                            <?php foreach ($perms as $p): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="permissions[]"
                                                           value="<?= (int)$p['id'] ?>"
                                                           id="perm_<?= (int)$p['id'] ?>">
                                                    <label class="form-check-label small"
                                                           for="perm_<?= (int)$p['id'] ?>"
                                                           title="<?= e($p['description'] ?? '') ?>">
                                                        <?= e($p['name']) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-end">
                <a href="roles.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Save Role
                </button>
            </div>
        </form>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>