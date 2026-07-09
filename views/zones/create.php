<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <h2>Create Zone</h2>
        <?php renderFlash(); ?>
        <div class="card">
            <div class="card-body">
                <form action="zones.php?action=store" method="POST">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label>Code</label>
                        <input type="text" name="zone_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="zone_name" class="form-control" required>
                    </div>
        <div class="mb-3">
            <label>Parent warehouses</label>
            <select name="warehouse_id" class="form-control" required>
                <option value="">Select...</option>
                <?php foreach($parents as $p): ?>
                <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="zones.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>