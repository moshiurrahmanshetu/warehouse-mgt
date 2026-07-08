<?php require_once VIEW_PATH . '/includes/header.php'; require_once VIEW_PATH . '/includes/navbar.php'; ?>
<div class="d-flex">
    <?php require_once VIEW_PATH . '/includes/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <h2>Edit Warehouse</h2>
        <?php renderFlash(); ?>
        <div class="card">
            <div class="card-body">
                <form action="warehouses.php?action=update" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <div class="mb-3">
                        <label>Code</label>
                        <input type="text" name="warehouse_code" value="<?= e($item['warehouse_code']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="warehouse_name" value="<?= e($item['warehouse_name']) ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $item['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $item['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="warehouses.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once VIEW_PATH . '/includes/footer.php'; ?>