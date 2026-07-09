<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="tax_rates.php">Tax Rates</a></li>
            <li class="breadcrumb-item active">Edit Tax Rate</li>
        </ol></nav>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Tax Rate</h2>
            <a href="tax_rates.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <?php renderFlash(); ?>
        <form action="tax_rates.php?action=update" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-percent"></i> Tax Rate Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tax Name <span class="text-danger">*</span></label>
                            <input type="text" name="tax_name" class="form-control" value="<?= e($item['tax_name']) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Percentage (%)</label>
                            <input type="number" step="0.0001" name="tax_percentage" class="form-control" value="<?= e($item['tax_percentage']) ?>" min="0" max="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tax Type</label>
                            <select name="tax_type" class="form-select">
                                <option value="Exclusive" <?= $item['tax_type'] === 'Exclusive' ? 'selected' : '' ?>>Exclusive (added on top)</option>
                                <option value="Inclusive" <?= $item['tax_type'] === 'Inclusive' ? 'selected' : '' ?>>Inclusive (included in price)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= $item['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $item['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <a href="tax_rates.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Update Tax Rate</button>
            </div>
        </form>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
