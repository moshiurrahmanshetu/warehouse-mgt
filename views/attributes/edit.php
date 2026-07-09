<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="attributes.php">Attributes</a></li>
            <li class="breadcrumb-item active">Edit Attribute</li>
        </ol></nav>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Attribute</h2>
            <a href="attributes.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <?php renderFlash(); ?>
        <form action="attributes.php?action=update" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-sliders"></i> Attribute Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Attribute Code</label>
                            <input type="text" class="form-control" value="<?= e($item['attribute_code']) ?>" disabled>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Attribute Name <span class="text-danger">*</span></label>
                            <input type="text" name="attribute_name" class="form-control" value="<?= e($item['attribute_name']) ?>" required>
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
                <a href="attributes.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Update Attribute</button>
            </div>
        </form>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
