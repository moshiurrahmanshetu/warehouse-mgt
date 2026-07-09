<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="suppliers.php">Suppliers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Supplier Details</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Supplier Details: <?= e($supplier['supplier_code']) ?></h2>
            <div>
                <a href="suppliers.php" class="btn btn-secondary">Back to List</a>
                <?php if(!$supplier['deleted_at'] && hasPermission('suppliers.edit')): ?>
                    <a href="suppliers.php?action=edit&id=<?= $supplier['id'] ?>" class="btn btn-warning">Edit</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if($supplier['deleted_at']): ?>
            <div class="alert alert-danger">
                This supplier was deleted on <?= formatDate($supplier['deleted_at']) ?>.
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light fw-bold">Company Information</div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr><th style="width: 40%">Company Name:</th><td><?= e($supplier['company_name']) ?></td></tr>
                            <tr><th>Contact Person:</th><td><?= e($supplier['contact_person'] ?? '-') ?></td></tr>
                            <tr><th>Email:</th><td><?= e($supplier['email'] ?? '-') ?></td></tr>
                            <tr><th>Phone:</th><td><?= e($supplier['phone'] ?? '-') ?></td></tr>
                            <tr><th>Mobile:</th><td><?= e($supplier['mobile'] ?? '-') ?></td></tr>
                            <tr><th>Website:</th><td><?= e($supplier['website'] ?? '-') ?></td></tr>
                            <tr><th>Tax Number:</th><td><?= e($supplier['tax_number'] ?? '-') ?></td></tr>
                            <tr><th>Trade License:</th><td><?= e($supplier['trade_license'] ?? '-') ?></td></tr>
                            <tr><th>Status:</th>
                                <td>
                                    <span class="badge bg-<?= $supplier['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst(e($supplier['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light fw-bold">Address & Location</div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr><th style="width: 40%">Address:</th><td><?= nl2br(e($supplier['address'] ?? '-')) ?></td></tr>
                            <tr><th>City:</th><td><?= e($supplier['city'] ?? '-') ?></td></tr>
                            <tr><th>State:</th><td><?= e($supplier['state'] ?? '-') ?></td></tr>
                            <tr><th>Zip Code:</th><td><?= e($supplier['zip_code'] ?? '-') ?></td></tr>
                            <tr><th>Country:</th><td><?= e($supplier['country'] ?? '-') ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-light fw-bold">Financial Details & Notes</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr><th style="width: 40%">Opening Balance:</th><td><?= number_format($supplier['opening_balance'], 2) ?> <?= e($supplier['balance_type']) ?></td></tr>
                                    <tr><th>Credit Limit:</th><td><?= number_format($supplier['credit_limit'], 2) ?></td></tr>
                                    <tr><th>Payment Terms:</th><td><?= e($supplier['payment_terms'] ?? '-') ?></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <strong>Notes:</strong>
                                <p class="mt-2 text-muted"><?= nl2br(e($supplier['notes'] ?? 'No notes available.')) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
