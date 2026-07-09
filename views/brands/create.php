<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="brands.php">Brands</a></li>
            <li class="breadcrumb-item active">Add Brand</li>
        </ol></nav>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Add New Brand</h2>
            <a href="brands.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <?php renderFlash(); ?>
        <form action="brands.php?action=store" method="POST">
            <?= csrfField() ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-award"></i> Brand Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" name="brand_name" class="form-control" required placeholder="e.g. Apple">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control" placeholder="https://...">
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
                            <textarea name="description" class="form-control" rows="3" placeholder="Optional..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <a href="brands.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Save Brand</button>
            </div>
        </form>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
