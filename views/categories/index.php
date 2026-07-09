<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-diagram-3 me-2"></i>Categories</h2>
            <div>
                <a href="categories.php?action=export&<?= http_build_query($filters) ?>" class="btn btn-success me-2"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
                <?php if(hasPermission('categories.create')): ?>
                    <a href="categories.php?action=create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Category</a>
                <?php endif; ?>
            </div>
        </div>

        <?php renderFlash(); ?>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="categories.php" method="GET" class="row g-3">
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
                        <a href="categories.php" class="btn btn-outline-secondary">Reset</a>
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
                                <th>Name</th>
                                <th>Parent</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($items)): ?>
                                <tr><td colspan="6" class="text-center py-4"><div class="text-muted">No categories found.</div></td></tr>
                            <?php else: ?>
                                <?php foreach ($items as $row): ?>
                                <tr>
                                    <td><span class="fw-bold text-primary"><?= e($row['category_code']) ?></span></td>
                                    <td><?= e($row['category_name']) ?></td>
                                    <td><?= $row['parent_id'] ? e($parentMap[$row['parent_id']] ?? '—') : '<span class="text-muted">Root</span>' ?></td>
                                    <td><?= (int)$row['sort_order'] ?></td>
                                    <td>
                                        <?php if($row['deleted_at']): ?>
                                            <span class="badge bg-danger">Deleted</span>
                                        <?php else: ?>
                                            <form action="categories.php?action=toggleStatus" method="POST" class="d-inline">
                                                <?= csrfField() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'secondary' ?> border-0" style="cursor:pointer;" title="Click to toggle"><?= ucfirst(e($row['status'])) ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if(!$row['deleted_at']): ?>
                                            <?php if(hasPermission('categories.edit')): ?>
                                                <a href="categories.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if(hasPermission('categories.delete')): ?>
                                                <form action="categories.php?action=delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                                    <?= csrfField() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if(hasPermission('categories.restore')): ?>
                                                <form action="categories.php?action=restore" method="POST" class="d-inline" onsubmit="return confirm('Restore this category?');">
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
                    <?= renderPagination($totalRecords, $limit, $page, 'categories.php') ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
