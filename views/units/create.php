<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="units.php">Units</a></li>
            <li class="breadcrumb-item active">Add Unit</li>
        </ol></nav>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Add New Unit</h2>
            <a href="units.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <?php renderFlash(); ?>
        <form action="units.php?action=store" method="POST">
            <?= csrfField() ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-rulers"></i> Unit Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" name="unit_name" class="form-control" required placeholder="e.g. Kilogram">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Short Name <span class="text-danger">*</span></label>
                            <input type="text" name="short_name" class="form-control" required placeholder="e.g. kg">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Unit Type</label>
                            <input type="text" name="unit_type" class="form-control" placeholder="e.g. Weight, Volume, Length">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Base Unit</label>
                            <select name="base_unit_id" class="form-select">
                                <option value="">— None (This is a base unit) —</option>
                                <?php foreach ($baseUnits as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= e($u['unit_name']) ?> (<?= e($u['short_name']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Select a base unit if this unit is derived from another.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Conversion Factor</label>
                            <input type="number" step="0.00000001" name="conversion_factor" class="form-control" value="1.00000000" min="0">
                            <div class="form-text">e.g. 1000 if 1 base unit = 1000 of this unit.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <a href="units.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Save Unit</button>
            </div>
        </form>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
