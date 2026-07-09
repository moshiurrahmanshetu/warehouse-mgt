<?php
// Repair script to fix models and restore Users/Roles views

// 1. Repair Models
$models = glob(__DIR__ . '/models/*.php');
$repairedModels = [];
foreach ($models as $modelPath) {
    if (basename($modelPath) === 'BaseModel.php') continue;
    $content = file_get_contents($modelPath);
    if (strpos($content, 'extends BaseModel') !== false && strpos($content, 'BaseModel.php') === false) {
        // Find a safe place to put the require
        if (strpos($content, "defined('BASEPATH')") !== false) {
            $content = preg_replace('/(defined\([^\)]+\).*?;)/', "$1\nrequire_once __DIR__ . '/BaseModel.php';", $content);
        } else {
            $content = preg_replace('/<\?php\s*/', "<?php\nrequire_once __DIR__ . '/BaseModel.php';\n", $content);
        }
        file_put_contents($modelPath, $content);
        $repairedModels[] = basename($modelPath);
    }
}

// 2. Repair Users Views
$userIndex = __DIR__ . '/views/users/index.php';
if (file_exists($userIndex)) {
    $content = file_get_contents($userIndex);
    // Overwrite the table header
    $content = preg_replace('/<th>Users Name<\/th>\s*<th>Percentage \(%\)<\/th>\s*<th>Type<\/th>/s', 
        "<th>Name</th><th>Email</th><th>Phone</th>", $content);
    // Overwrite the table rows
    $content = preg_replace('/<td><span class="fw-semibold"><\?= e\(\$row\[\'users_name\'\]\) \?><\/span><\/td>\s*<td><span class="fw-bold text-primary"><\?= number_format\(\(float\)\$row\[\'users_percentage\'\], 2\) \?>%<\/span><\/td>\s*<td>\s*<span class="badge bg-<\?= \$row\[\'users_type\'\] === \'Inclusive\' \? \'info\' : \'warning\' \?> text-dark"><\?= e\(\$row\[\'users_type\'\]\) \?><\/span>\s*<\/td>/s',
        '<td><span class="fw-semibold"><?= e($row[\'name\']) ?></span></td>
         <td><?= e($row[\'email\']) ?></td>
         <td><?= e($row[\'phone\']) ?></td>', $content);
         
    // Fix search field
    $content = preg_replace('/Search users name\.\.\./', 'Search name, email...', $content);
    
    file_put_contents($userIndex, $content);
}

$userCreate = __DIR__ . '/views/users/create.php';
if (file_exists($userCreate)) {
    $content = <<<HTML
<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-person-plus me-2"></i>Create User</h2>
            <a href="users.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Users</a>
        </div>
        <?php renderFlash(); ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="users.php?action=store" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Avatar</label>
                            <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Roles</label>
                            <select name="roles[]" class="form-select" multiple>
                                <?php foreach (\$roles as \$r): ?>
                                    <option value="<?= \$r['id'] ?>"><?= e(\$r['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
HTML;
    file_put_contents($userCreate, $content);
}

$userEdit = __DIR__ . '/views/users/edit.php';
if (file_exists($userEdit)) {
    $content = <<<HTML
<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-person-check me-2"></i>Edit User</h2>
            <a href="users.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Users</a>
        </div>
        <?php renderFlash(); ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="users.php?action=update" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= \$user['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?= e(\$user['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= e(\$user['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= e(\$user['phone']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Avatar</label>
                            <?php if(\$user['avatar']): ?>
                                <img src="<?= e(\$user['avatar']) ?>" height="40" class="d-block mb-2">
                            <?php endif; ?>
                            <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Roles</label>
                            <select name="roles[]" class="form-select" multiple>
                                <?php foreach (\$roles as \$r): ?>
                                    <option value="<?= \$r['id'] ?>" <?= in_array(\$r['id'], \$userRoles) ? 'selected' : '' ?>><?= e(\$r['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" <?= \$user['is_active'] ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= !\$user['is_active'] ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
HTML;
    file_put_contents($userEdit, $content);
}

// 3. Repair Roles Views
$roleIndex = __DIR__ . '/views/roles/index.php';
if (file_exists($roleIndex)) {
    $content = file_get_contents($roleIndex);
    // Overwrite the table header
    $content = preg_replace('/<th>Roles Name<\/th>\s*<th>Percentage \(%\)<\/th>\s*<th>Type<\/th>/s', 
        "<th>Role Name</th><th>Slug</th><th>Description</th>", $content);
    // Overwrite the table rows
    $content = preg_replace('/<td><span class="fw-semibold"><\?= e\(\$row\[\'roles_name\'\]\) \?><\/span><\/td>\s*<td><span class="fw-bold text-primary"><\?= number_format\(\(float\)\$row\[\'roles_percentage\'\], 2\) \?>%<\/span><\/td>\s*<td>\s*<span class="badge bg-<\?= \$row\[\'roles_type\'\] === \'Inclusive\' \? \'info\' : \'warning\' \?> text-dark"><\?= e\(\$row\[\'roles_type\'\]\) \?><\/span>\s*<\/td>/s',
        '<td><span class="fw-semibold"><?= e($row[\'name\']) ?></span> <?php if($row[\'is_system\']): ?><span class="badge bg-dark ms-2">System</span><?php endif; ?></td>
         <td><span class="text-muted"><?= e($row[\'slug\']) ?></span></td>
         <td><?= e($row[\'description\']) ?></td>', $content);
         
    // Fix search field
    $content = preg_replace('/Search roles name\.\.\./', 'Search role...', $content);
    
    file_put_contents($roleIndex, $content);
}

$roleCreate = __DIR__ . '/views/roles/create.php';
if (file_exists($roleCreate)) {
    $content = <<<HTML
<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-shield-plus me-2"></i>Create Role</h2>
            <a href="roles.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Roles</a>
        </div>
        <?php renderFlash(); ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="roles.php?action=store" method="POST">
                    <?= csrfField() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <h5>Permissions</h5>
                            <div class="row">
                                <?php foreach (\$groupedPermissions as \$module => \$perms): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100">
                                            <div class="card-header bg-light fw-bold text-capitalize"><?= e(\$module) ?></div>
                                            <div class="card-body">
                                                <?php foreach (\$perms as \$p): ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= \$p['id'] ?>" id="perm_<?= \$p['id'] ?>">
                                                        <label class="form-check-label" for="perm_<?= \$p['id'] ?>" title="<?= e(\$p['description']) ?>">
                                                            <?= e(\$p['name']) ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
HTML;
    file_put_contents($roleCreate, $content);
}

$roleEdit = __DIR__ . '/views/roles/edit.php';
if (file_exists($roleEdit)) {
    $content = <<<HTML
<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-shield-check me-2"></i>Edit Role</h2>
            <a href="roles.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Roles</a>
        </div>
        <?php renderFlash(); ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="roles.php?action=update" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= \$role['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?= e(\$role['name']) ?>" <?= \$role['is_system'] ? 'readonly' : 'required' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" value="<?= e(\$role['slug']) ?>" <?= \$role['is_system'] ? 'readonly' : 'required' ?>>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" value="<?= e(\$role['description']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select" <?= \$role['is_system'] ? 'disabled' : '' ?>>
                                <option value="1" <?= \$role['is_active'] ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= !\$role['is_active'] ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <h5>Permissions</h5>
                            <div class="row">
                                <?php foreach (\$groupedPermissions as \$module => \$perms): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100">
                                            <div class="card-header bg-light fw-bold text-capitalize"><?= e(\$module) ?></div>
                                            <div class="card-body">
                                                <?php foreach (\$perms as \$p): ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= \$p['id'] ?>" id="perm_<?= \$p['id'] ?>" <?= in_array(\$p['id'], \$rolePermissions) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="perm_<?= \$p['id'] ?>" title="<?= e(\$p['description']) ?>">
                                                            <?= e(\$p['name']) ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
HTML;
    file_put_contents($roleEdit, $content);
}

echo "Repairs complete.\n";
echo "Repaired Models: " . implode(', ', $repairedModels) . "\n";
