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
                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="bi bi-person-circle me-2 text-primary"></i>My Profile</h2>
                <p class="text-muted mb-0" style="font-size:13px;">Manage your personal account details and security settings.</p>
            </div>
            <div>
                <a href="<?= e(APP_URL) ?>/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php renderFlash(); ?>

        <div class="row g-4">

            <!-- Left Column: User Summary Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                    <div class="card-body text-center p-4">
                        
                        <!-- Avatar Display -->
                        <div class="position-relative d-inline-block mb-3">
                            <?php if (!empty($user['avatar']) && file_exists(BASEPATH . '/' . $user['avatar'])): ?>
                                <img src="<?= e(APP_URL . '/' . $user['avatar']) ?>" alt="<?= e($user['name']) ?>"
                                     class="rounded-circle border border-2 border-primary shadow-sm"
                                     width="110" height="110" style="object-fit:cover;">
                            <?php else: ?>
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                     style="width:110px;height:110px;font-size:42px;background:linear-gradient(135deg, var(--primary), var(--secondary));">
                                    <?= strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h4 class="fw-bold mb-1" style="color:var(--text-primary);"><?= e($user['name']) ?></h4>
                        <p class="text-muted mb-2" style="font-size:13px;"><?= e($user['email']) ?></p>

                        <!-- Role & Status Badges -->
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <?php
                            $roles = $user['roles'] ?? [];
                            if (empty($roles)) $roles = ['User'];
                            foreach ($roles as $role): ?>
                                <span class="wms-badge badge-admin"><i class="bi bi-shield-lock me-1"></i><?= ucfirst(e($role)) ?></span>
                            <?php endforeach; ?>

                            <?php $active = ($user['is_active'] ?? ($user['status'] === 'active')); ?>
                            <span class="wms-badge <?= $active ? 'badge-active' : 'badge-inactive' ?>">
                                <i class="bi bi-<?= $active ? 'check-circle' : 'dash-circle' ?> me-1"></i><?= $active ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>

                        <hr style="border-color:var(--border-color);">

                        <!-- Account Metadata List -->
                        <div class="text-start" style="font-size:13px;">
                            <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:var(--border-color)!important;">
                                <span class="text-muted"><i class="bi bi-hash me-1"></i>User ID</span>
                                <span class="fw-semibold" style="color:var(--text-primary);"><?= (int)$user['id'] ?></span>
                            </div>

                            <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:var(--border-color)!important;">
                                <span class="text-muted"><i class="bi bi-person-badge me-1"></i>Username / Identity</span>
                                <span class="fw-semibold" style="color:var(--text-primary);"><?= e($user['email']) ?></span>
                            </div>

                            <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:var(--border-color)!important;">
                                <span class="text-muted"><i class="bi bi-telephone me-1"></i>Phone</span>
                                <span class="fw-semibold" style="color:var(--text-primary);"><?= !empty($user['phone']) ? e($user['phone']) : '—' ?></span>
                            </div>

                            <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:var(--border-color)!important;">
                                <span class="text-muted"><i class="bi bi-box-arrow-in-right me-1"></i>Last Login</span>
                                <span class="fw-semibold" style="color:var(--text-primary);">
                                    <?php
                                    $lastLogin = $user['last_login'] ?? $user['last_login_at'] ?? null;
                                    echo $lastLogin ? formatDate($lastLogin) : '—';
                                    ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:var(--border-color)!important;">
                                <span class="text-muted"><i class="bi bi-activity me-1"></i>Last Activity</span>
                                <span class="fw-semibold" style="color:var(--text-primary);">
                                    <?= !empty($user['last_activity']) ? formatDate($user['last_activity']) : 'Just now' ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted"><i class="bi bi-calendar-check me-1"></i>Joined Date</span>
                                <span class="fw-semibold" style="color:var(--text-primary);">
                                    <?= !empty($user['created_at']) ? formatDate($user['created_at'], 'd M Y') : '—' ?>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right Column: Tabs for Edit Profile & Change Password -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                    
                    <div class="card-header border-bottom p-0" style="background:transparent;border-color:var(--border-color)!important;">
                        <ul class="nav nav-tabs px-3 pt-2" id="profileTabs" role="tablist" style="border-bottom:none;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3 fw-semibold" id="edit-profile-tab" data-bs-toggle="tab" data-bs-target="#edit-profile" type="button" role="tab" aria-controls="edit-profile" aria-selected="true" style="color:var(--text-primary);">
                                    <i class="bi bi-person-lines-fill me-2"></i>Edit Profile
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3 fw-semibold" id="change-password-tab" data-bs-toggle="tab" data-bs-target="#change-password" type="button" role="tab" aria-controls="change-password" aria-selected="false" style="color:var(--text-secondary);">
                                    <i class="bi bi-key-fill me-2"></i>Change Password
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="profileTabContent">

                            <!-- Tab 1: Edit Profile -->
                            <div class="tab-pane fade show active" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                                <form action="<?= e(APP_URL) ?>/profile.php?action=update" method="POST" enctype="multipart/form-data">
                                    <?= csrfField() ?>

                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Full Name <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:var(--border-color);color:var(--text-muted);"><i class="bi bi-person"></i></span>
                                                <input type="text" name="name" class="form-control" required
                                                       value="<?= e($user['name']) ?>"
                                                       placeholder="Enter your full name">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Email Address <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:var(--border-color);color:var(--text-muted);"><i class="bi bi-envelope"></i></span>
                                                <input type="email" name="email" class="form-control" required
                                                       value="<?= e($user['email']) ?>"
                                                       placeholder="name@example.com">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Phone / Mobile Number
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:var(--border-color);color:var(--text-muted);"><i class="bi bi-telephone"></i></span>
                                                <input type="text" name="phone" class="form-control"
                                                       value="<?= e($user['phone'] ?? '') ?>"
                                                       placeholder="+1 (555) 000-0000">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Profile Photo / Avatar
                                            </label>
                                            <input type="file" name="avatar" class="form-control"
                                                   accept="image/jpeg,image/png,image/webp">
                                            <div class="form-text" style="font-size:11.5px;color:var(--text-muted);">
                                                Accepted formats: JPG, PNG, WebP (Max 2MB).
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Readonly System Assigned Information Notice -->
                                    <div class="p-3 mb-4 rounded" style="background:rgba(79,70,229,0.08);border:1px solid rgba(79,70,229,0.2);">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-info-circle text-primary me-2 fs-5"></i>
                                            <span style="font-size:12.5px;color:var(--text-secondary);">
                                                Roles, account status, and system identifiers are managed by the System Administrator and cannot be modified here.
                                            </span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-check-circle me-1"></i> Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab 2: Change Password -->
                            <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                                <form action="<?= e(APP_URL) ?>/profile.php?action=changePassword" method="POST">
                                    <?= csrfField() ?>

                                    <div class="row g-3 mb-4">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Current Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:var(--border-color);color:var(--text-muted);"><i class="bi bi-lock"></i></span>
                                                <input type="password" name="current_password" id="current_password" class="form-control" required autocomplete="current-password" placeholder="Enter current password">
                                                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="current_password" style="border-color:var(--border-color);"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                New Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:var(--border-color);color:var(--text-muted);"><i class="bi bi-key"></i></span>
                                                <input type="password" name="new_password" id="new_password" class="form-control" required minlength="6" autocomplete="new-password" placeholder="Minimum 6 characters">
                                                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="new_password" style="border-color:var(--border-color);"><i class="bi bi-eye"></i></button>
                                            </div>
                                            <div class="form-text" style="font-size:11.5px;color:var(--text-muted);">
                                                Must be at least 6 characters.
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Confirm New Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:var(--border-color);color:var(--text-muted);"><i class="bi bi-check2-square"></i></span>
                                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="6" autocomplete="new-password" placeholder="Re-type new password">
                                                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="confirm_password" style="border-color:var(--border-color);"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-warning px-4 text-dark fw-semibold">
                                            <i class="bi bi-shield-lock me-1"></i> Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </main>
</div>

<script>
// Handle hash routing to switch tab automatically (e.g. #password)
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#password') {
        const passTab = document.getElementById('change-password-tab');
        if (passTab) {
            const tab = new bootstrap.Tab(passTab);
            tab.show();
        }
    }

    // Password show/hide toggle
    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });
});
</script>

<?php require_once INCLUDE_PATH . '/footer.php'; ?>
