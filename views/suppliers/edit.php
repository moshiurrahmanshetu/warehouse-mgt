<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="suppliers.php">Suppliers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Supplier</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Supplier: <?= e($supplier['supplier_code']) ?></h2>
            <a href="suppliers.php" class="btn btn-secondary">Back to List</a>
        </div>

        <?php renderFlash(); ?>

        <div class="card">
            <div class="card-body">
                <form action="suppliers.php?action=update" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $supplier['id'] ?>">
                    
                    <h5 class="mb-3 text-primary border-bottom pb-2">Basic Information</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" value="<?= e($supplier['company_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" value="<?= e($supplier['contact_person'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($supplier['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($supplier['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" class="form-control" value="<?= e($supplier['mobile'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control" value="<?= e($supplier['website'] ?? '') ?>" placeholder="https://...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tax Number / VAT</label>
                            <input type="text" name="tax_number" class="form-control" value="<?= e($supplier['tax_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Trade License</label>
                            <input type="text" name="trade_license" class="form-control" value="<?= e($supplier['trade_license'] ?? '') ?>">
                        </div>
                    </div>

                    <h5 class="mb-3 text-primary border-bottom pb-2">Location & Address</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="<?= e($supplier['country'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State / Province</label>
                            <input type="text" name="state" class="form-control" value="<?= e($supplier['state'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="<?= e($supplier['city'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Zip Code</label>
                            <input type="text" name="zip_code" class="form-control" value="<?= e($supplier['zip_code'] ?? '') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Full Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= e($supplier['address'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <h5 class="mb-3 text-primary border-bottom pb-2">Financial Details</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Opening Balance</label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="<?= e($supplier['opening_balance']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Balance Type</label>
                            <select name="balance_type" class="form-select">
                                <option value="Credit" <?= $supplier['balance_type'] === 'Credit' ? 'selected' : '' ?>>Credit (We Owe)</option>
                                <option value="Debit" <?= $supplier['balance_type'] === 'Debit' ? 'selected' : '' ?>>Debit (They Owe)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Credit Limit</label>
                            <input type="number" step="0.01" name="credit_limit" class="form-control" value="<?= e($supplier['credit_limit']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Terms</label>
                            <input type="text" name="payment_terms" class="form-control" value="<?= e($supplier['payment_terms'] ?? '') ?>">
                        </div>
                    </div>

                    <h5 class="mb-3 text-primary border-bottom pb-2">Other Settings</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-9">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"><?= e($supplier['notes'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= $supplier['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $supplier['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">Update Supplier</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</div>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>
