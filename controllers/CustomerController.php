<?php
/**
 * Customer Controller
 * Warehouse Management System - Phase 04
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/CustomerModel.php';

class CustomerController
{
    private CustomerModel $model;

    public function __construct()
    {
        $this->model = new CustomerModel();
    }

    // ─────────────────────────────────────────────────────────
    // Index: List with search, filter, pagination
    // ─────────────────────────────────────────────────────────
    public function index(): void
    {
        requirePermission('customers.view');

        $filters = $this->getFilters();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 10;
        $offset  = ($page - 1) * $limit;

        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $customers    = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);

        $cities    = $this->model->getDistinctField('city');
        $countries = $this->model->getDistinctField('country');

        require_once VIEW_PATH . '/customers/index.php';
    }

    // ─────────────────────────────────────────────────────────
    // Export CSV
    // ─────────────────────────────────────────────────────────
    public function export(): void
    {
        requirePermission('customers.view');

        $filters   = $this->getFilters();
        $customers = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);

        $headers = [
            'Customer Code', 'Type', 'Customer Name', 'Company Name', 'Email', 'Phone',
            'Mobile', 'City', 'Country', 'Credit Limit', 'Opening Balance',
            'Current Balance', 'Balance Type', 'Status'
        ];

        $data = [];
        foreach ($customers as $c) {
            $data[] = [
                $c['customer_code'],
                $c['customer_type'],
                $c['customer_name'],
                $c['company_name'],
                $c['email'],
                $c['phone'],
                $c['mobile'],
                $c['city'],
                $c['country'],
                $c['credit_limit'],
                $c['opening_balance'],
                $c['current_balance'],
                $c['balance_type'],
                ucfirst($c['status']),
            ];
        }

        exportCsv('customers_export_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    // ─────────────────────────────────────────────────────────
    // Print list
    // ─────────────────────────────────────────────────────────
    public function printList(): void
    {
        requirePermission('customers.view');
        $filters   = $this->getFilters();
        $customers = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        require_once VIEW_PATH . '/customers/print.php';
    }

    // ─────────────────────────────────────────────────────────
    // Create form
    // ─────────────────────────────────────────────────────────
    public function create(): void
    {
        requirePermission('customers.create');
        require_once VIEW_PATH . '/customers/create.php';
    }

    // ─────────────────────────────────────────────────────────
    // Store (process create)
    // ─────────────────────────────────────────────────────────
    public function store(): void
    {
        requirePermission('customers.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('customers.php');
        verifyCsrf();

        $data = $this->extractFormData();
        $errors = $this->validateData($data);

        if (!empty($errors)) {
            flashMessage('error', implode('<br>', $errors));
            redirect('customers.php?action=create');
        }

        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            $data['customer_code'] = generateSequenceCode('customer_code', 'CUS-', 6);
            $this->model->create($data);
            $db->commit();
            flashMessage('success', 'Customer created successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            error_log('Customer store error: ' . $e->getMessage());
            flashMessage('error', 'Failed to create customer. Please try again.');
        }

        redirect('customers.php');
    }

    // ─────────────────────────────────────────────────────────
    // Edit form
    // ─────────────────────────────────────────────────────────
    public function edit(): void
    {
        requirePermission('customers.edit');
        $customer = $this->findOrAbort((int)($_GET['id'] ?? 0));
        require_once VIEW_PATH . '/customers/edit.php';
    }

    // ─────────────────────────────────────────────────────────
    // Update (process edit)
    // ─────────────────────────────────────────────────────────
    public function update(): void
    {
        requirePermission('customers.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('customers.php');
        verifyCsrf();

        $id   = (int)($_POST['id'] ?? 0);
        $data = $this->extractFormData();
        $errors = $this->validateData($data, $id);

        if (!empty($errors)) {
            flashMessage('error', implode('<br>', $errors));
            redirect("customers.php?action=edit&id=$id");
        }

        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            $this->model->update($id, $data);
            $db->commit();
            flashMessage('success', 'Customer updated successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            error_log('Customer update error: ' . $e->getMessage());
            flashMessage('error', 'Failed to update customer. Please try again.');
        }

        redirect('customers.php');
    }

    // ─────────────────────────────────────────────────────────
    // Details view
    // ─────────────────────────────────────────────────────────
    public function details(): void
    {
        requirePermission('customers.view');
        $customer = $this->findOrAbort((int)($_GET['id'] ?? 0), true);
        require_once VIEW_PATH . '/customers/details.php';
    }

    // ─────────────────────────────────────────────────────────
    // Soft delete
    // ─────────────────────────────────────────────────────────
    public function delete(): void
    {
        requirePermission('customers.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('customers.php');
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            $this->model->delete($id);
            $db->commit();
            flashMessage('success', 'Customer deleted successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to delete customer.');
        }

        redirect('customers.php');
    }

    // ─────────────────────────────────────────────────────────
    // Restore
    // ─────────────────────────────────────────────────────────
    public function restore(): void
    {
        requirePermission('customers.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('customers.php');
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            $this->model->restore($id);
            $db->commit();
            flashMessage('success', 'Customer restored successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to restore customer.');
        }

        redirect('customers.php?only_deleted=1');
    }

    // ─────────────────────────────────────────────────────────
    // Toggle status
    // ─────────────────────────────────────────────────────────
    public function toggleStatus(): void
    {
        requirePermission('customers.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('customers.php');
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            $this->model->toggleStatus($id);
            $db->commit();
            flashMessage('success', 'Customer status updated.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to update status.');
        }

        redirect('customers.php');
    }

    // ─────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────

    private function getFilters(): array
    {
        return [
            'search'        => sanitize($_GET['search'] ?? ''),
            'status'        => sanitize($_GET['status'] ?? ''),
            'customer_type' => sanitize($_GET['customer_type'] ?? ''),
            'city'          => sanitize($_GET['city'] ?? ''),
            'country'       => sanitize($_GET['country'] ?? ''),
            'only_deleted'  => !empty($_GET['only_deleted']),
        ];
    }

    private function extractFormData(): array
    {
        return [
            'customer_type'    => in_array($_POST['customer_type'] ?? '', ['Individual', 'Business']) ? $_POST['customer_type'] : 'Individual',
            'company_name'     => sanitize($_POST['company_name'] ?? ''),
            'customer_name'    => sanitize($_POST['customer_name'] ?? ''),
            'email'            => sanitize($_POST['email'] ?? ''),
            'phone'            => sanitize($_POST['phone'] ?? ''),
            'mobile'           => sanitize($_POST['mobile'] ?? ''),
            'website'          => sanitize($_POST['website'] ?? ''),
            'tax_number'       => sanitize($_POST['tax_number'] ?? ''),
            'national_id'      => sanitize($_POST['national_id'] ?? ''),
            'trade_license'    => sanitize($_POST['trade_license'] ?? ''),
            'country'          => sanitize($_POST['country'] ?? ''),
            'state'            => sanitize($_POST['state'] ?? ''),
            'city'             => sanitize($_POST['city'] ?? ''),
            'zip_code'         => sanitize($_POST['zip_code'] ?? ''),
            'address'          => sanitize($_POST['address'] ?? ''),
            'shipping_address' => sanitize($_POST['shipping_address'] ?? ''),
            'credit_limit'     => (float)($_POST['credit_limit'] ?? 0),
            'opening_balance'  => (float)($_POST['opening_balance'] ?? 0),
            'current_balance'  => (float)($_POST['current_balance'] ?? 0),
            'balance_type'     => ($_POST['balance_type'] ?? 'Debit') === 'Credit' ? 'Credit' : 'Debit',
            'payment_terms'    => sanitize($_POST['payment_terms'] ?? ''),
            'notes'            => sanitize($_POST['notes'] ?? ''),
            'status'           => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        ];
    }

    private function validateData(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if (empty($data['customer_name'])) {
            $errors[] = 'Customer Name is required.';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        }

        if (!empty($data['customer_name']) && !empty($data['mobile'])) {
            if ($this->model->duplicateNameMobileExists($data['customer_name'], $data['mobile'], $excludeId)) {
                $errors[] = 'A customer with the same Name and Mobile already exists.';
            }
        }

        if (!empty($data['customer_name']) && !empty($data['email'])) {
            if ($this->model->duplicateNameEmailExists($data['customer_name'], $data['email'], $excludeId)) {
                $errors[] = 'A customer with the same Name and Email already exists.';
            }
        }

        return $errors;
    }

    private function findOrAbort(int $id, bool $includeDeleted = false): array
    {
        $customer = $this->model->findById($id, $includeDeleted);
        if (!$customer) {
            flashMessage('error', 'Customer not found.');
            redirect('customers.php');
        }
        return $customer;
    }
}
