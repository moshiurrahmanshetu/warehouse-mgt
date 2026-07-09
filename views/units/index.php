<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-rulers me-2"></i>Units of Measure</h2>
            <div>
                <a href="units.php?action=export&<?= http_build_query($filters) ?>" class="btn btn-success me-2"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
                <?php if(hasPermission('units.create')): ?>
                    <a href="units.php?action=create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Unit</a>
                <?php endif; ?>
            </div>
        </div>

        <?php renderFlash(); ?>

        <div class="card mb-3">
            <div class="card-body">
                <form action="units.php" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search code, name, short name..." value="<?= e($filters['search']) ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" name="only_deleted" value="1" id="onlyDeleted" <?= $filters['only_deleted'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="onlyDeleted">Show Deleted</label>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="units.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Unit Name</th>
                                <th>Short Name</th>
                                <th>Type</th>
                                <th>Base Unit</th>
                                <th>Conv. Factor</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($items)): ?>
                                <tr><td colspan="8" class="text-center py-4"><div class="text-muted">No units found.</div></td></tr>
                            <?php else: ?>
                                <?php foreach ($items as $row): ?>
                                <tr>
                                    <td><span class="fw-bold text-primary"><?= e($row['unit_code']) ?></span></td>
                                    <td><?= e($row['unit_name']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= e($row['short_name']) ?></span></td>
                                    <td><?= e($row['unit_type']) ?: '<span class="text-muted">—</span>' ?></td>
                                    <td><?= $row['base_unit_id'] ? e($baseUnitMap[$row['base_unit_id']] ?? '—') : '<span class="text-muted">Base</span>' ?></td>
                                    <td><?= number_format((float)$row['conversion_factor'], 4) ?></td>
                                    <td>
                                        <?php if($row['deleted_at']): ?>
                                            <span class="badge bg-danger">Deleted</span>
                                        <?php else: ?>
                                            <form action="units.php?action=toggleStatus" method="POST" class="d-inline">
                                                <?= csrfField() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'secondary' ?> border-0" style="cursor:pointer;" title="Click to toggle"><?= ucfirst(e($row['status'])) ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if(!$row['deleted_at']): ?>
                                            <?php if(hasPermission('units.edit')): ?>
                                                <a href="units.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if(hasPermission('units.delete')): ?>
                                                <form action="units.php?action=delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this unit?');">
                                                    <?= csrfField() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if(hasPermission('units.restore')): ?>
                                                <form action="units.php?action=restore" method="POST" class="d-inline" onsubmit="return confirm('Restore this unit?');">
                                                    <?= csrfField() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Restore"><i class="bi bi-arrow-counterclockwise"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($totalRecords > $limit): ?>
                <div class="card-footer bg-white border-top-0 pt-3">
                    <?= renderPagination($totalRecords, $limit, $page, 'units.php') ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
