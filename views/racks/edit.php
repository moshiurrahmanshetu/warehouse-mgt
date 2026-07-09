<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <h2>Edit Rack</h2>
        <?php renderFlash(); ?>
        <div class="card">
            <div class="card-body">
                <form action="racks.php?action=update" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <div class="mb-3">
                        <label>Code</label>
                        <input type="text" name="rack_code" value="<?= e($item['rack_code']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="rack_name" value="<?= e($item['rack_name']) ?>" class="form-control" required>
                    </div>
        <div class="mb-3">
            <label>Parent warehouse_zones</label>
            <select name="zone_id" class="form-control" required>
                <option value="">Select...</option>
                <?php foreach($parents as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $item['zone_id'] == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $item['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $item['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="racks.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>