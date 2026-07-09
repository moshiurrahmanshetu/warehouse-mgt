<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="categories.php">Categories</a></li>
                <li class="breadcrumb-item active">Edit Category</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Category</h2>
            <a href="categories.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <?php renderFlash(); ?>

        <form action="categories.php?action=update" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-diagram-3"></i> Category Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Category Code</label>
                            <input type="text" class="form-control" value="<?= e($item['category_code']) ?>" disabled>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="category_name" class="form-control" value="<?= e($item['category_name']) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Parent Category</label>
                            <select name="parent_id" class="form-select">
                                <option value="">— None (Root Category) —</option>
                                <?php foreach ($parents as $p): ?>
                                    <?php if ($p['id'] !== (int)$item['id']): ?>
                                    <option value="<?= $p['id'] ?>" <?= $item['parent_id'] == $p['id'] ? 'selected' : '' ?>><?= e($p['category_name']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= (int)$item['sort_order'] ?>" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= $item['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $item['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= e($item['description']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="categories.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Update Category</button>
            </div>
        </form>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
