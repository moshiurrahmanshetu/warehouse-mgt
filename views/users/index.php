<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-people me-2"></i>Users</h2>
            <?php if (hasPermission('users.manage')): ?>
                <a href="users.php?action=create" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Add User
                </a>
            <?php endif; ?>
        </div>

        <?php renderFlash(); ?>

        <!-- Filter Bar -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <form action="users.php" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search name, username, email..."
                               value="<?= e($filters['search']) ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active"   <?= $filters['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-center gap-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="only_deleted" value="1"
                                   id="onlyDeleted" <?= $filters['only_deleted'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="onlyDeleted">Show Deleted</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="users.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:48px">Avatar</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No users found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $row): ?>
                                <tr>
                                    <!-- Avatar -->
                                    <td>
                                        <?php if (!empty($row['avatar'])): ?>
                                            <img src="<?= e($row['avatar']) ?>" alt="avatar"
                                                 class="rounded-circle" width="36" height="36"
                                                 style="object-fit:cover;">
                                        <?php else: ?>
                                            <span class="d-inline-flex align-items-center justify-content-center
                                                         rounded-circle bg-secondary text-white fw-bold"
                                                  style="width:36px;height:36px;font-size:14px;">
                                                <?= strtoupper(mb_substr($row['name'] ?? '?', 0, 1)) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Full Name -->
                                    <td>
                                        <span class="fw-semibold"><?= e($row['name']) ?></span>
                                        <?php if (!empty($row['username'])): ?>
                                            <br><small class="text-muted">@<?= e($row['username']) ?></small>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Email -->
                                    <td><?= e($row['email']) ?></td>

                                    <!-- Phone -->
                                    <td><?= e($row['phone'] ?? '—') ?></td>

                                    <!-- Status -->
                                    <td>
                                        <?php if (!empty($row['deleted_at'])): ?>
                                            <span class="badge bg-danger">Deleted</span>
                                        <?php elseif (hasPermission('users.manage')): ?>
                                            <form action="users.php?action=toggleStatus" method="POST" class="d-inline">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                <button type="submit"
                                                        class="badge border-0 bg-<?= ($row['is_active'] ?? $row['status'] === 'active') ? 'success' : 'secondary' ?>"
                                                        style="cursor:pointer;" title="Click to toggle">
                                                    <?= ($row['is_active'] ?? $row['status'] === 'active') ? 'Active' : 'Inactive' ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-<?= ($row['is_active'] ?? $row['status'] === 'active') ? 'success' : 'secondary' ?>">
                                                <?= ($row['is_active'] ?? $row['status'] === 'active') ? 'Active' : 'Inactive' ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Last Login -->
                                    <td>
                                        <?php
                                            $lastLogin = $row['last_login'] ?? $row['last_login_at'] ?? null;
                                            echo $lastLogin ? e(date('d M Y, H:i', strtotime($lastLogin))) : '<span class="text-muted">—</span>';
                                        ?>
                                    </td>

                                    <!-- Created -->
                                    <td><?= !empty($row['created_at']) ? e(date('d M Y', strtotime($row['created_at']))) : '—' ?></td>

                                    <!-- Actions -->
                                    <td class="text-end">
                                        <?php if (empty($row['deleted_at'])): ?>
                                            <?php if (hasPermission('users.manage')): ?>
                                                <a href="users.php?action=edit&id=<?= (int)$row['id'] ?>"
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="users.php?action=delete" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Delete this user?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if (hasPermission('users.manage')): ?>
                                                <form action="users.php?action=restore" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Restore this user?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($totalRecords > 0): ?>
                <div class="card-footer bg-white border-top-0 pt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Total: <?= number_format($totalRecords) ?> user(s)</small>
                        <?= renderPagination($totalRecords, $limit, $page, 'users.php') ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
