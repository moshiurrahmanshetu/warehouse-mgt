<?php require_once VIEW_PATH . '/includes/header.php'; require_once VIEW_PATH . '/includes/navbar.php'; ?>
<div class="d-flex">
    <?php require_once VIEW_PATH . '/includes/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="customers.php">Customers</a></li>
                <li class="breadcrumb-item active">Edit Customer</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Customer: <span class="text-primary"><?= e($customer['customer_code']) ?></span></h2>
            <a href="customers.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <?php renderFlash(); ?>

        <form action="customers.php?action=update" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= $customer['id'] ?>">

            <!-- Basic Information -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-person-vcard"></i> Basic Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                            <select name="customer_type" class="form-select" id="customerType" onchange="toggleCompanyField()">
                                <option value="Individual" <?= $customer['customer_type'] === 'Individual' ? 'selected' : '' ?>>Individual</option>
                                <option value="Business"   <?= $customer['customer_type'] === 'Business'   ? 'selected' : '' ?>>Business</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="<?= e($customer['customer_name']) ?>" required>
                        </div>
                        <div class="col-md-4" id="companyNameField">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?= e($customer['company_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($customer['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($customer['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" class="form-control" value="<?= e($customer['mobile'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control" value="<?= e($customer['website'] ?? '') ?>" placeholder="https://...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tax Number / VAT</label>
                            <input type="text" name="tax_number" class="form-control" value="<?= e($customer['tax_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">National ID</label>
                            <input type="text" name="national_id" class="form-control" value="<?= e($customer['national_id'] ?? '') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Trade License</label>
                            <input type="text" name="trade_license" class="form-control" value="<?= e($customer['trade_license'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location & Address -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-geo-alt"></i> Location &amp; Address
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="<?= e($customer['country'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State / Province</label>
                            <input type="text" name="state" class="form-control" value="<?= e($customer['state'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="<?= e($customer['city'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Zip Code</label>
                            <input type="text" name="zip_code" class="form-control" value="<?= e($customer['zip_code'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Billing Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= e($customer['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" class="form-control" rows="2"><?= e($customer['shipping_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Details -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-currency-dollar"></i> Financial Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Opening Balance</label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="<?= e($customer['opening_balance']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current Balance</label>
                            <input type="number" step="0.01" name="current_balance" class="form-control" value="<?= e($customer['current_balance']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Balance Type</label>
                            <select name="balance_type" class="form-select">
                                <option value="Debit"  <?= $customer['balance_type'] === 'Debit'  ? 'selected' : '' ?>>Debit (Customer Owes)</option>
                                <option value="Credit" <?= $customer['balance_type'] === 'Credit' ? 'selected' : '' ?>>Credit (We Owe)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Credit Limit</label>
                            <input type="number" step="0.01" name="credit_limit" class="form-control" value="<?= e($customer['credit_limit']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Terms</label>
                            <input type="text" name="payment_terms" class="form-control" value="<?= e($customer['payment_terms'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"   <?= $customer['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $customer['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-sticky"></i> Notes</div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="3"><?= e($customer['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="text-end">
                <a href="customers.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Update Customer</button>
            </div>
        </form>

    </main>
</div>

<script>
function toggleCompanyField() {
    const type = document.getElementById('customerType').value;
    document.getElementById('companyNameField').style.display = type === 'Business' ? 'block' : 'none';
}
toggleCompanyField();
</script>
<?php require_once VIEW_PATH . '/includes/footer.php'; ?>
