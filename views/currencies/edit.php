<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="currencies.php">Currencies</a></li>
            <li class="breadcrumb-item active">Edit Currency</li>
        </ol></nav>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Currency</h2>
            <a href="currencies.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <?php renderFlash(); ?>
        <form action="currencies.php?action=update" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-currency-exchange"></i> Currency Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Currency Code <span class="text-danger">*</span></label>
                            <input type="text" name="currency_code" class="form-control text-uppercase" value="<?= e($item['currency_code']) ?>" required maxlength="10">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Currency Name <span class="text-danger">*</span></label>
                            <input type="text" name="currency_name" class="form-control" value="<?= e($item['currency_name']) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Symbol <span class="text-danger">*</span></label>
                            <input type="text" name="currency_symbol" class="form-control" value="<?= e($item['currency_symbol']) ?>" required maxlength="10">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Exchange Rate</label>
                            <input type="number" step="0.000001" name="exchange_rate" class="form-control" value="<?= e($item['exchange_rate']) ?>" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= $item['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $item['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault" <?= $item['is_default'] ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="isDefault">
                                    <i class="bi bi-star-fill text-warning"></i> Set as Default Currency
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <a href="currencies.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Update Currency</button>
            </div>
        </form>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
