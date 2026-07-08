<?php require_once VIEW_PATH . '/includes/header.php'; require_once VIEW_PATH . '/includes/navbar.php'; ?>
<div class="d-flex">
    <?php require_once VIEW_PATH . '/includes/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="customers.php">Customers</a></li>
                <li class="breadcrumb-item active">Add Customer</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Add New Customer</h2>
            <a href="customers.php" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <?php renderFlash(); ?>

        <form action="customers.php?action=store" method="POST">
            <?= csrfField() ?>

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
                                <option value="Individual">Individual</option>
                                <option value="Business">Business</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-4" id="companyNameField">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control" placeholder="https://...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tax Number / VAT</label>
                            <input type="text" name="tax_number" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">National ID</label>
                            <input type="text" name="national_id" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Trade License</label>
                            <input type="text" name="trade_license" class="form-control">
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
                            <input type="text" name="country" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State / Province</label>
                            <input type="text" name="state" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Zip Code</label>
                            <input type="text" name="zip_code" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Billing Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" class="form-control" rows="2"></textarea>
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
                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current Balance</label>
                            <input type="number" step="0.01" name="current_balance" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Balance Type</label>
                            <select name="balance_type" class="form-select">
                                <option value="Debit">Debit (Customer Owes)</option>
                                <option value="Credit">Credit (We Owe)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Credit Limit</label>
                            <input type="number" step="0.01" name="credit_limit" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Terms</label>
                            <input type="text" name="payment_terms" class="form-control" placeholder="e.g. Net 30">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-semibold"><i class="bi bi-sticky"></i> Notes</div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="text-end">
                <a href="customers.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Save Customer</button>
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
