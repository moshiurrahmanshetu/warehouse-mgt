<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="users.php">Users</a></li>
                <li class="breadcrumb-item active">Add User</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-person-plus me-2"></i>Add New User</h2>
            <a href="users.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <?php renderFlash(); ?>

        <form action="users.php?action=store" method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>

            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-person-lines-fill me-1"></i> User Details
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                   placeholder="e.g. John Smith">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required
                                   placeholder="e.g. john@example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required
                                   autocomplete="new-password">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   placeholder="e.g. +1 555 000 000">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Avatar</label>
                            <input type="file" name="avatar" class="form-control"
                                   accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG, WebP — max 2 MB.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Assign Roles</label>
                            <select name="roles[]" class="form-select" multiple size="4">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= (int)$role['id'] ?>">
                                        <?= e($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Hold Ctrl / Cmd to select multiple.</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                       id="isActive" checked>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="users.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Save User
                </button>
            </div>
        </form>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>