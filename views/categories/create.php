<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="categories.php">Categories</a></li>
                <li class="breadcrumb-item active">Add Category</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Add New Category</h2>
            <a href="categories.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <?php renderFlash(); ?>

        <form action="categories.php?action=store" method="POST">
            <?= csrfField() ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-diagram-3"></i> Category Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="category_name" class="form-control" required placeholder="e.g. Electronics">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Category</label>
                            <select name="parent_id" class="form-select">
                                <option value="">— None (Root Category) —</option>
                                <?php foreach ($parents as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= e($p['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Optional description..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="categories.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Save Category</button>
            </div>
        </form>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
