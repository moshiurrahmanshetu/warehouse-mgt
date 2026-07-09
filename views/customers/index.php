<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">Customers</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Customers</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="customers.php?action=export&<?= http_build_query(array_filter($filters)) ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                </a>
                <a href="customers.php?action=printList&<?= http_build_query(array_filter($filters)) ?>" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="bi bi-printer"></i> Print
                </a>
                <?php if (hasPermission('customers.create')): ?>
                    <a href="customers.php?action=create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Add Customer
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php renderFlash(); ?>

        <!-- Filter Card -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body py-2">
                <form action="customers.php" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Search code, name, email, phone..." value="<?= e($filters['search']) ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="active"   <?= $filters['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="customer_type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="Individual" <?= $filters['customer_type'] === 'Individual' ? 'selected' : '' ?>>Individual</option>
                            <option value="Business"   <?= $filters['customer_type'] === 'Business'   ? 'selected' : '' ?>>Business</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="city" class="form-select form-select-sm">
                            <option value="">All Cities</option>
                            <?php foreach ($cities as $c): ?>
                                <option value="<?= e($c) ?>" <?= $filters['city'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="country" class="form-select form-select-sm">
                            <option value="">All Countries</option>
                            <?php foreach ($countries as $c): ?>
                                <option value="<?= e($c) ?>" <?= $filters['country'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-center gap-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="only_deleted" value="1" id="onlyDeleted" <?= $filters['only_deleted'] ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="onlyDeleted">Deleted</label>
                        </div>
                    </div>
                    <div class="col-md-auto d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <a href="customers.php" class="btn btn-outline-secondary btn-sm">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Customer Name</th>
                                <th>Type</th>
                                <th>Contact</th>
                                <th>City / Country</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-2 d-block mb-2"></i>
                                        No customers found. <?php if (!$filters['only_deleted'] && hasPermission('customers.create')): ?>
                                            <a href="customers.php?action=create">Add your first customer</a>.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customers as $c): ?>
                                    <tr>
                                        <td><span class="fw-semibold text-primary"><?= e($c['customer_code']) ?></span></td>
                                        <td>
                                            <div class="fw-semibold"><?= e($c['customer_name']) ?></div>
                                            <?php if ($c['company_name']): ?>
                                                <small class="text-muted"><?= e($c['company_name']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $c['customer_type'] === 'Business' ? 'info' : 'secondary' ?> text-white">
                                                <?= e($c['customer_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($c['mobile']): ?><div><i class="bi bi-phone text-muted"></i> <?= e($c['mobile']) ?></div><?php endif; ?>
                                            <?php if ($c['email']): ?><small class="text-muted"><i class="bi bi-envelope"></i> <?= e($c['email']) ?></small><?php endif; ?>
                                        </td>
                                        <td><?= e($c['city'] ?: '-') ?>, <?= e($c['country'] ?: '-') ?></td>
                                        <td class="text-end">
                                            <span class="<?= $c['balance_type'] === 'Credit' ? 'text-success' : 'text-danger' ?>">
                                                <?= number_format($c['current_balance'], 2) ?>
                                            </span>
                                            <small class="text-muted">(<?= e($c['balance_type']) ?>)</small>
                                        </td>
                                        <td>
                                            <?php if ($c['deleted_at']): ?>
                                                <span class="badge bg-danger">Deleted</span>
                                            <?php else: ?>
                                                <span class="badge bg-<?= $c['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst(e($c['status'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="customers.php?action=details&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>

                                            <?php if (!$c['deleted_at']): ?>
                                                <?php if (hasPermission('customers.edit')): ?>
                                                    <a href="customers.php?action=edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                                    <!-- Toggle Status -->
                                                    <form action="customers.php?action=toggleStatus" method="POST" class="d-inline">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-<?= $c['status'] === 'active' ? 'secondary' : 'success' ?>"
                                                                title="<?= $c['status'] === 'active' ? 'Deactivate' : 'Activate' ?>">
                                                            <i class="bi bi-toggle-<?= $c['status'] === 'active' ? 'on' : 'off' ?>"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if (hasPermission('customers.delete')): ?>
                                                    <form action="customers.php?action=delete" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Delete this customer?');">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if (hasPermission('customers.restore')): ?>
                                                    <form action="customers.php?action=restore" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Restore this customer?');">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Restore"><i class="bi bi-arrow-counterclockwise"></i></button>
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
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Showing <?= count($customers) ?> of <?= $totalRecords ?> records</small>
                        <?= renderPagination($totalRecords, $limit, $page, 'customers.php') ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
