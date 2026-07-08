<?php require_once VIEW_PATH . '/includes/header.php'; require_once VIEW_PATH . '/includes/navbar.php'; ?>
<div class="d-flex">
    <?php require_once VIEW_PATH . '/includes/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <h2>Create Warehouse</h2>
        <?php renderFlash(); ?>
        <div class="card">
            <div class="card-body">
                <form action="warehouses.php?action=store" method="POST">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label>Code</label>
                        <input type="text" name="warehouse_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="warehouse_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="warehouses.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once VIEW_PATH . '/includes/footer.php'; ?>