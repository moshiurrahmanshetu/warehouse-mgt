<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="customers.php">Customers</a></li>
                <li class="breadcrumb-item active">Customer Details</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Customer: <span class="text-primary"><?= e($customer['customer_code']) ?></span></h2>
            <div class="d-flex gap-2">
                <a href="customers.php" class="btn btn-secondary btn-sm">Back to List</a>
                <?php if (!$customer['deleted_at'] && hasPermission('customers.edit')): ?>
                    <a href="customers.php?action=edit&id=<?= $customer['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($customer['deleted_at']): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                This customer was <strong>deleted</strong> on <?= e($customer['deleted_at']) ?>.
                <?php if (hasPermission('customers.restore')): ?>
                    <form action="customers.php?action=restore" method="POST" class="d-inline ms-3">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Restore this customer?')">Restore</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <!-- Identity -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold"><i class="bi bi-person-badge"></i> Identity</div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><th width="45%">Customer Code</th><td><span class="badge bg-primary"><?= e($customer['customer_code']) ?></span></td></tr>
                            <tr><th>Type</th><td><span class="badge bg-<?= $customer['customer_type'] === 'Business' ? 'info' : 'secondary' ?> text-white"><?= e($customer['customer_type']) ?></span></td></tr>
                            <tr><th>Customer Name</th><td class="fw-semibold"><?= e($customer['customer_name']) ?></td></tr>
                            <tr><th>Company Name</th><td><?= e($customer['company_name'] ?: '-') ?></td></tr>
                            <tr><th>Tax Number</th><td><?= e($customer['tax_number'] ?: '-') ?></td></tr>
                            <tr><th>National ID</th><td><?= e($customer['national_id'] ?: '-') ?></td></tr>
                            <tr><th>Trade License</th><td><?= e($customer['trade_license'] ?: '-') ?></td></tr>
                            <tr><th>Status</th>
                                <td>
                                    <span class="badge bg-<?= $customer['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst(e($customer['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold"><i class="bi bi-telephone"></i> Contact</div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><th width="45%">Email</th><td><?= $customer['email'] ? '<a href="mailto:'.e($customer['email']).'">'.e($customer['email']).'</a>' : '-' ?></td></tr>
                            <tr><th>Phone</th><td><?= e($customer['phone'] ?: '-') ?></td></tr>
                            <tr><th>Mobile</th><td><?= e($customer['mobile'] ?: '-') ?></td></tr>
                            <tr><th>Website</th><td><?= $customer['website'] ? '<a href="'.e($customer['website']).'" target="_blank">'.e($customer['website']).'</a>' : '-' ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold"><i class="bi bi-geo-alt"></i> Address</div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><th width="45%">Country</th><td><?= e($customer['country'] ?: '-') ?></td></tr>
                            <tr><th>State</th><td><?= e($customer['state'] ?: '-') ?></td></tr>
                            <tr><th>City</th><td><?= e($customer['city'] ?: '-') ?></td></tr>
                            <tr><th>Zip Code</th><td><?= e($customer['zip_code'] ?: '-') ?></td></tr>
                            <tr><th>Billing Address</th><td><?= nl2br(e($customer['address'] ?: '-')) ?></td></tr>
                            <tr><th>Shipping Address</th><td><?= nl2br(e($customer['shipping_address'] ?: '-')) ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Financial -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold"><i class="bi bi-currency-dollar"></i> Financial Details</div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><th width="45%">Opening Balance</th><td><?= number_format($customer['opening_balance'], 2) ?></td></tr>
                            <tr><th>Current Balance</th>
                                <td class="<?= $customer['balance_type'] === 'Credit' ? 'text-success' : 'text-danger' ?> fw-semibold">
                                    <?= number_format($customer['current_balance'], 2) ?> (<?= e($customer['balance_type']) ?>)
                                </td>
                            </tr>
                            <tr><th>Credit Limit</th><td><?= number_format($customer['credit_limit'], 2) ?></td></tr>
                            <tr><th>Payment Terms</th><td><?= e($customer['payment_terms'] ?: '-') ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes & Meta -->
            <?php if ($customer['notes']): ?>
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light fw-semibold"><i class="bi bi-sticky"></i> Notes</div>
                    <div class="card-body text-muted"><?= nl2br(e($customer['notes'])) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Meta -->
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body py-2 text-muted small">
                        Created: <?= e($customer['created_at']) ?> &nbsp;&bull;&nbsp;
                        Last Updated: <?= e($customer['updated_at']) ?>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
