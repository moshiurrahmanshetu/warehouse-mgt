<?php
/**
 * TaxRate Controller - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/TaxRateModel.php';

class TaxRateController
{
    private TaxRateModel $model;

    public function __construct() { $this->model = new TaxRateModel(); }

    public function index(): void
    {
        requirePermission('tax_rates.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $page = max(1, (int)($_GET['page'] ?? 1)); $limit = 15; $offset = ($page - 1) * $limit;
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        require_once VIEW_PATH . '/tax_rates/index.php';
    }

    public function export(): void
    {
        requirePermission('tax_rates.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $items = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        $headers = ['Tax Name', 'Percentage (%)', 'Type', 'Status'];
        $data = array_map(fn($r) => [$r['tax_name'], $r['tax_percentage'], $r['tax_type'], ucfirst($r['status'])], $items);
        exportCsv('tax_rates_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    public function create(): void { requirePermission('tax_rates.create'); require_once VIEW_PATH . '/tax_rates/create.php'; }

    public function store(): void
    {
        requirePermission('tax_rates.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tax_rates.php');
        verifyCsrf();
        $name = sanitize($_POST['tax_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Tax Name is required.'); redirect('tax_rates.php?action=create'); }
        if ($this->model->nameExists($name)) { flashMessage('error', 'Tax Name already exists.'); redirect('tax_rates.php?action=create'); }
        $pct = (float)($_POST['tax_percentage'] ?? 0);
        if ($pct < 0 || $pct > 100) { flashMessage('error', 'Tax percentage must be between 0 and 100.'); redirect('tax_rates.php?action=create'); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->create(['tax_name' => $name, 'tax_percentage' => $pct, 'tax_type' => ($_POST['tax_type'] ?? 'Exclusive') === 'Inclusive' ? 'Inclusive' : 'Exclusive', 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Tax Rate created successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); error_log($e->getMessage()); flashMessage('error', 'Failed to create tax rate.'); }
        redirect('tax_rates.php');
    }

    public function edit(): void { requirePermission('tax_rates.edit'); $item = $this->findOrAbort((int)($_GET['id'] ?? 0)); require_once VIEW_PATH . '/tax_rates/edit.php'; }

    public function update(): void
    {
        requirePermission('tax_rates.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tax_rates.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['tax_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Tax Name is required.'); redirect("tax_rates.php?action=edit&id=$id"); }
        if ($this->model->nameExists($name, $id)) { flashMessage('error', 'Tax Name already exists.'); redirect("tax_rates.php?action=edit&id=$id"); }
        $pct = (float)($_POST['tax_percentage'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, ['tax_name' => $name, 'tax_percentage' => $pct, 'tax_type' => ($_POST['tax_type'] ?? 'Exclusive') === 'Inclusive' ? 'Inclusive' : 'Exclusive', 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Tax Rate updated successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update tax rate.'); }
        redirect('tax_rates.php');
    }

    public function delete(): void
    {
        requirePermission('tax_rates.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tax_rates.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softDelete($id); Database::getInstance()->commit(); flashMessage('success', 'Tax Rate deleted.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to delete.'); }
        redirect('tax_rates.php');
    }

    public function restore(): void
    {
        requirePermission('tax_rates.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tax_rates.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softRestore($id); Database::getInstance()->commit(); flashMessage('success', 'Tax Rate restored.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to restore.'); }
        redirect('tax_rates.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('tax_rates.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tax_rates.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->toggleStatusLog($id); Database::getInstance()->commit(); flashMessage('success', 'Status updated.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update status.'); }
        redirect('tax_rates.php');
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Tax Rate not found.'); redirect('tax_rates.php'); exit; }
        return $item;
    }
}
