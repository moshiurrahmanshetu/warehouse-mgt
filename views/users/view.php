<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="users.php">Users</a></li>
                <li class="breadcrumb-item active">View User</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-person-badge me-2"></i>User Profile</h2>
            <div>
                <?php if (hasPermission('users.manage')): ?>
                    <a href="users.php?action=edit&id=<?= (int)$user['id'] ?>" class="btn btn-warning btn-sm me-2">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                <?php endif; ?>
                <a href="users.php" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>

        <?php renderFlash(); ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-4">
                    <!-- Avatar -->
                    <div class="col-md-2 text-center">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= e($user['avatar']) ?>" alt="Avatar"
                                 class="rounded-circle border" width="100" height="100"
                                 style="object-fit:cover;">
                        <?php else: ?>
                            <span class="d-inline-flex align-items-center justify-content-center
                                         rounded-circle bg-secondary text-white fw-bold"
                                  style="width:100px;height:100px;font-size:36px;">
                                <?= strtoupper(mb_substr($user['name'] ?? '?', 0, 1)) ?>
                            </span>
                        <?php endif; ?>
                        <div class="mt-2">
                            <?php $active = ($user['is_active'] ?? ($user['status'] === 'active')); ?>
                            <span class="badge bg-<?= $active ? 'success' : 'secondary' ?>">
                                <?= $active ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="col-md-10">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Full Name</dt>
                            <dd class="col-sm-9"><?= e($user['name']) ?></dd>

                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9"><?= e($user['email']) ?></dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9"><?= e($user['phone'] ?? '—') ?></dd>

                            <dt class="col-sm-3">Last Login</dt>
                            <dd class="col-sm-9">
                                <?php
                                    $lastLogin = $user['last_login'] ?? $user['last_login_at'] ?? null;
                                    echo $lastLogin ? e(date('d M Y, H:i', strtotime($lastLogin))) : '—';
                                ?>
                            </dd>

                            <dt class="col-sm-3">Last Activity</dt>
                            <dd class="col-sm-9">
                                <?= !empty($user['last_activity']) ? e(date('d M Y, H:i', strtotime($user['last_activity']))) : '—' ?>
                            </dd>

                            <dt class="col-sm-3">Created</dt>
                            <dd class="col-sm-9">
                                <?= !empty($user['created_at']) ? e(date('d M Y', strtotime($user['created_at']))) : '—' ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
