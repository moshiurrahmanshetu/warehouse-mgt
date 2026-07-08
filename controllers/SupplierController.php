<?php
/**
 * Supplier Controller
 * Warehouse Management System
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/SupplierModel.php';

class SupplierController
{
    private SupplierModel $model;

    public function __construct()
    {
        $this->model = new SupplierModel();
    }

    /**
     * List suppliers with search, filtering, and pagination.
     */
    public function index(): void
    {
        requirePermission('suppliers.view');

        $filters = [
            'search'       => sanitize($_GET['search'] ?? ''),
            'status'       => sanitize($_GET['status'] ?? ''),
            'city'         => sanitize($_GET['city'] ?? ''),
            'country'      => sanitize($_GET['country'] ?? ''),
            'only_deleted' => !empty($_GET['only_deleted']),
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $suppliers = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);

        $cities = $this->model->getDistinctField('city');
        $countries = $this->model->getDistinctField('country');

        require_once VIEW_PATH . '/suppliers/index.php';
    }

    /**
     * Export the filtered supplier list to CSV.
     */
    public function export(): void
    {
        requirePermission('suppliers.view');

        $filters = [
            'search'       => sanitize($_GET['search'] ?? ''),
            'status'       => sanitize($_GET['status'] ?? ''),
            'city'         => sanitize($_GET['city'] ?? ''),
            'country'      => sanitize($_GET['country'] ?? ''),
            'only_deleted' => !empty($_GET['only_deleted']),
        ];

        // Fetch all matching without limit
        $suppliers = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);

        $headers = [
            'Supplier Code', 'Company Name', 'Contact Person', 'Email', 'Phone', 'Mobile',
            'City', 'Country', 'Opening Balance', 'Balance Type', 'Status'
        ];

        $data = [];
        foreach ($suppliers as $s) {
            $data[] = [
                $s['supplier_code'], $s['company_name'], $s['contact_person'], $s['email'], 
                $s['phone'], $s['mobile'], $s['city'], $s['country'], 
                $s['opening_balance'], $s['balance_type'], ucfirst($s['status'])
            ];
        }

        exportCsv('suppliers_export_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    /**
     * Print view of suppliers.
     */
    public function printList(): void
    {
        requirePermission('suppliers.view');
        $filters = [
            'search'       => sanitize($_GET['search'] ?? ''),
            'status'       => sanitize($_GET['status'] ?? ''),
            'city'         => sanitize($_GET['city'] ?? ''),
            'country'      => sanitize($_GET['country'] ?? ''),
            'only_deleted' => !empty($_GET['only_deleted']),
        ];
        $suppliers = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        require_once VIEW_PATH . '/suppliers/print.php';
    }

    /**
     * Show create form.
     */
    public function create(): void
    {
        requirePermission('suppliers.create');
        require_once VIEW_PATH . '/suppliers/create.php';
    }

    /**
     * Process create form.
     */
    public function store(): void
    {
        requirePermission('suppliers.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('suppliers.php');
        verifyCsrf();

        $company_name = sanitize($_POST['company_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        
        if (empty($company_name)) {
            flashMessage('error', 'Company Name is required.');
            redirect('suppliers.php?action=create');
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashMessage('error', 'Invalid email format.');
            redirect('suppliers.php?action=create');
        }
        if ($this->model->companyNameExists($company_name)) {
            flashMessage('error', 'Company Name already exists.');
            redirect('suppliers.php?action=create');
        }

        $data = $this->extractFormData();
        
        try {
            Database::getInstance()->beginTransaction();
            $data['supplier_code'] = generateSequenceCode('supplier_code', 'SUP-', 6);
            $this->model->create($data);
            Database::getInstance()->commit();
            flashMessage('success', 'Supplier created successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            error_log($e->getMessage());
            flashMessage('error', 'Failed to generate supplier code or save data.');
        }

        redirect('suppliers.php');
    }

    /**
     * Show edit form.
     */
    public function edit(): void
    {
        requirePermission('suppliers.edit');
        $id = (int)($_GET['id'] ?? 0);
        $supplier = $this->model->findById($id);
        
        if (!$supplier) {
            flashMessage('error', 'Supplier not found.');
            redirect('suppliers.php');
        }
        
        require_once VIEW_PATH . '/suppliers/edit.php';
    }

    /**
     * Process update form.
     */
    public function update(): void
    {
        requirePermission('suppliers.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('suppliers.php');
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $company_name = sanitize($_POST['company_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        
        if (empty($company_name)) {
            flashMessage('error', 'Company Name is required.');
            redirect("suppliers.php?action=edit&id=$id");
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashMessage('error', 'Invalid email format.');
            redirect("suppliers.php?action=edit&id=$id");
        }
        if ($this->model->companyNameExists($company_name, $id)) {
            flashMessage('error', 'Company Name already exists.');
            redirect("suppliers.php?action=edit&id=$id");
        }

        $data = $this->extractFormData();
        
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, $data);
            Database::getInstance()->commit();
            flashMessage('success', 'Supplier updated successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to update supplier.');
        }

        redirect('suppliers.php');
    }

    /**
     * Show details view.
     */
    public function details(): void
    {
        requirePermission('suppliers.view');
        $id = (int)($_GET['id'] ?? 0);
        $supplier = $this->model->findById($id, true); // True to view details even if deleted
        
        if (!$supplier) {
            flashMessage('error', 'Supplier not found.');
            redirect('suppliers.php');
        }
        
        require_once VIEW_PATH . '/suppliers/details.php';
    }

    /**
     * Soft delete supplier.
     */
    public function delete(): void
    {
        requirePermission('suppliers.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('suppliers.php');
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->delete($id);
            Database::getInstance()->commit();
            flashMessage('success', 'Supplier deleted successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to delete supplier.');
        }
        
        redirect('suppliers.php');
    }

    /**
     * Restore a deleted supplier.
     */
    public function restore(): void
    {
        requirePermission('suppliers.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('suppliers.php');
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->restore($id);
            Database::getInstance()->commit();
            flashMessage('success', 'Supplier restored successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to restore supplier.');
        }
        
        // Redirect back to deleted list
        redirect('suppliers.php?only_deleted=1');
    }

    /**
     * Helper to extract common form data.
     */
    private function extractFormData(): array
    {
        return [
            'company_name'    => sanitize($_POST['company_name'] ?? ''),
            'contact_person'  => sanitize($_POST['contact_person'] ?? ''),
            'email'           => sanitize($_POST['email'] ?? ''),
            'phone'           => sanitize($_POST['phone'] ?? ''),
            'mobile'          => sanitize($_POST['mobile'] ?? ''),
            'website'         => sanitize($_POST['website'] ?? ''),
            'tax_number'      => sanitize($_POST['tax_number'] ?? ''),
            'trade_license'   => sanitize($_POST['trade_license'] ?? ''),
            'country'         => sanitize($_POST['country'] ?? ''),
            'state'           => sanitize($_POST['state'] ?? ''),
            'city'            => sanitize($_POST['city'] ?? ''),
            'zip_code'        => sanitize($_POST['zip_code'] ?? ''),
            'address'         => sanitize($_POST['address'] ?? ''),
            'opening_balance' => (float)($_POST['opening_balance'] ?? 0),
            'balance_type'    => ($_POST['balance_type'] ?? 'Credit') === 'Debit' ? 'Debit' : 'Credit',
            'credit_limit'    => (float)($_POST['credit_limit'] ?? 0),
            'payment_terms'   => sanitize($_POST['payment_terms'] ?? ''),
            'notes'           => sanitize($_POST['notes'] ?? ''),
            'status'          => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        ];
    }
}
