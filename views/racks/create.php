<?php require_once VIEW_PATH . '/includes/header.php'; require_once VIEW_PATH . '/includes/navbar.php'; ?>
<div class="d-flex">
    <?php require_once VIEW_PATH . '/includes/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <h2>Create Rack</h2>
        <?php renderFlash(); ?>
        <div class="card">
            <div class="card-body">
                <form action="racks.php?action=store" method="POST">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label>Code</label>
                        <input type="text" name="rack_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="rack_name" class="form-control" required>
                    </div>
        <div class="mb-3">
            <label>Parent warehouse_zones</label>
            <select name="zone_id" class="form-control" required>
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
                    <a href="racks.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once VIEW_PATH . '/includes/footer.php'; ?>