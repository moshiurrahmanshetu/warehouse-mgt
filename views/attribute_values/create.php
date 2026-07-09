<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="attribute_values.php">Attribute Values</a></li>
            <li class="breadcrumb-item active">Add Value</li>
        </ol></nav>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Add New Attribute Value</h2>
            <a href="attribute_values.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <?php renderFlash(); ?>
        <form action="attribute_values.php?action=store" method="POST">
            <?= csrfField() ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-list-ul"></i> Value Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Attribute <span class="text-danger">*</span></label>
                            <select name="attribute_id" class="form-select" required>
                                <option value="">— Select Attribute —</option>
                                <?php foreach ($attributes as $attr): ?>
                                    <option value="<?= $attr['id'] ?>"><?= e($attr['attribute_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="text" name="value" class="form-control" required placeholder="e.g. Red, XL, Cotton">
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
                    </div>
                </div>
            </div>
            <div class="text-end">
                <a href="attribute_values.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Save Value</button>
            </div>
        </form>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
