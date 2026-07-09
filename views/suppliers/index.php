<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Supplier Management</h2>
            <div>
                <a href="suppliers.php?action=export&<?= http_build_query($filters) ?>" class="btn btn-success me-2"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
                <a href="suppliers.php?action=printList&<?= http_build_query($filters) ?>" target="_blank" class="btn btn-secondary me-2"><i class="bi bi-printer"></i> Print</a>
                <?php if(hasPermission('suppliers.create')): ?>
                    <a href="suppliers.php?action=create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Supplier</a>
                <?php endif; ?>
            </div>
        </div>

        <?php renderFlash(); ?>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="suppliers.php" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search code, name, email, phone..." value="<?= e($filters['search']) ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="city" class="form-select">
                            <option value="">All Cities</option>
                            <?php foreach($cities as $c): ?>
                                <option value="<?= e($c) ?>" <?= $filters['city'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="country" class="form-select">
                            <option value="">All Countries</option>
                            <?php foreach($countries as $c): ?>
                                <option value="<?= e($c) ?>" <?= $filters['country'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" name="only_deleted" value="1" id="onlyDeleted" <?= $filters['only_deleted'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="onlyDeleted">Show Deleted</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Supplier Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Company</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>City/Country</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($suppliers)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">No suppliers found.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($suppliers as $s): ?>
                                <tr>
                                    <td><span class="fw-bold"><?= e($s['supplier_code']) ?></span></td>
                                    <td><?= e($s['company_name']) ?></td>
                                    <td><?= e($s['contact_person']) ?: '<span class="text-muted">-</span>' ?></td>
                                    <td><?= e($s['phone']) ?: '<span class="text-muted">-</span>' ?></td>
                                    <td>
                                        <?= e($s['city']) ?: '-' ?>, <?= e($s['country']) ?: '-' ?>
                                    </td>
                                    <td>
                                        <?php if($s['deleted_at']): ?>
                                            <span class="badge bg-danger">Deleted</span>
                                        <?php else: ?>
                                            <span class="badge bg-<?= $s['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst(e($s['status'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="suppliers.php?action=details&id=<?= $s['id'] ?>" class="btn btn-sm btn-info" title="View Details"><i class="bi bi-eye"></i></a>
                                        
                                        <?php if(!$s['deleted_at']): ?>
                                            <?php if(hasPermission('suppliers.edit')): ?>
                                                <a href="suppliers.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <?php endif; ?>
                                            
                                            <?php if(hasPermission('suppliers.delete')): ?>
                                                <form action="suppliers.php?action=delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if(hasPermission('suppliers.restore')): ?>
                                                <form action="suppliers.php?action=restore" method="POST" class="d-inline" onsubmit="return confirm('Restore this supplier?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
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
                    <?= renderPagination($totalRecords, $limit, $page, 'suppliers.php') ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
