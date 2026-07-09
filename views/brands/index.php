<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-award me-2"></i>Brands</h2>
            <div>
                <a href="brands.php?action=export&<?= http_build_query($filters) ?>" class="btn btn-success me-2"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
                <?php if(hasPermission('brands.create')): ?>
                    <a href="brands.php?action=create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Brand</a>
                <?php endif; ?>
            </div>
        </div>

        <?php renderFlash(); ?>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="brands.php" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search code, name..." value="<?= e($filters['search']) ?>">
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
                        <a href="brands.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Brand Name</th>
                                <th>Website</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($items)): ?>
                                <tr><td colspan="5" class="text-center py-4"><div class="text-muted">No brands found.</div></td></tr>
                            <?php else: ?>
                                <?php foreach ($items as $row): ?>
                                <tr>
                                    <td><span class="fw-bold text-primary"><?= e($row['brand_code']) ?></span></td>
                                    <td><?= e($row['brand_name']) ?></td>
                                    <td><?= $row['website'] ? '<a href="' . e($row['website']) . '" target="_blank">' . e($row['website']) . '</a>' : '<span class="text-muted">—</span>' ?></td>
                                    <td>
                                        <?php if($row['deleted_at']): ?>
                                            <span class="badge bg-danger">Deleted</span>
                                        <?php else: ?>
                                            <form action="brands.php?action=toggleStatus" method="POST" class="d-inline">
                                                <?= csrfField() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'secondary' ?> border-0" style="cursor:pointer;" title="Click to toggle"><?= ucfirst(e($row['status'])) ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if(!$row['deleted_at']): ?>
                                            <?php if(hasPermission('brands.edit')): ?>
                                                <a href="brands.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if(hasPermission('brands.delete')): ?>
                                                <form action="brands.php?action=delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this brand?');">
                                                    <?= csrfField() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if(hasPermission('brands.restore')): ?>
                                                <form action="brands.php?action=restore" method="POST" class="d-inline" onsubmit="return confirm('Restore this brand?');">
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
                    <?= renderPagination($totalRecords, $limit, $page, 'brands.php') ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
