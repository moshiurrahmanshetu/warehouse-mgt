<?php
/**
 * Currency Controller - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/CurrencyModel.php';

class CurrencyController
{
    private CurrencyModel $model;

    public function __construct() { $this->model = new CurrencyModel(); }

    public function index(): void
    {
        requirePermission('currencies.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $page = max(1, (int)($_GET['page'] ?? 1)); $limit = 15; $offset = ($page - 1) * $limit;
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        require_once VIEW_PATH . '/currencies/index.php';
    }

    public function export(): void
    {
        requirePermission('currencies.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $items = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        $headers = ['Code', 'Name', 'Symbol', 'Exchange Rate', 'Default', 'Status'];
        $data = array_map(fn($r) => [$r['currency_code'], $r['currency_name'], $r['currency_symbol'], $r['exchange_rate'], $r['is_default'] ? 'Yes' : 'No', ucfirst($r['status'])], $items);
        exportCsv('currencies_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    public function create(): void { requirePermission('currencies.create'); require_once VIEW_PATH . '/currencies/create.php'; }

    public function store(): void
    {
        requirePermission('currencies.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('currencies.php');
        verifyCsrf();
        $code = strtoupper(sanitize($_POST['currency_code'] ?? ''));
        $name = sanitize($_POST['currency_name'] ?? '');
        $symbol = sanitize($_POST['currency_symbol'] ?? '');
        if (empty($code) || empty($name) || empty($symbol)) { flashMessage('error', 'Code, Name and Symbol are required.'); redirect('currencies.php?action=create'); }
        if ($this->model->codeExists($code)) { flashMessage('error', 'Currency Code already exists.'); redirect('currencies.php?action=create'); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->create(['currency_code' => $code, 'currency_name' => $name, 'currency_symbol' => $symbol, 'exchange_rate' => (float)($_POST['exchange_rate'] ?? 1), 'is_default' => !empty($_POST['is_default']) ? 1 : 0, 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Currency created successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); error_log($e->getMessage()); flashMessage('error', 'Failed to create currency.'); }
        redirect('currencies.php');
    }

    public function edit(): void { requirePermission('currencies.edit'); $item = $this->findOrAbort((int)($_GET['id'] ?? 0)); require_once VIEW_PATH . '/currencies/edit.php'; }

    public function update(): void
    {
        requirePermission('currencies.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('currencies.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $code = strtoupper(sanitize($_POST['currency_code'] ?? ''));
        $name = sanitize($_POST['currency_name'] ?? '');
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect("currencies.php?action=edit&id=$id"); }
        if ($this->model->codeExists($code, $id)) { flashMessage('error', 'Currency Code already exists.'); redirect("currencies.php?action=edit&id=$id"); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, ['currency_code' => $code, 'currency_name' => $name, 'currency_symbol' => sanitize($_POST['currency_symbol'] ?? ''), 'exchange_rate' => (float)($_POST['exchange_rate'] ?? 1), 'is_default' => !empty($_POST['is_default']) ? 1 : 0, 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Currency updated successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update currency.'); }
        redirect('currencies.php');
    }

    public function delete(): void
    {
        requirePermission('currencies.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('currencies.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softDelete($id); Database::getInstance()->commit(); flashMessage('success', 'Currency deleted.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to delete.'); }
        redirect('currencies.php');
    }

    public function restore(): void
    {
        requirePermission('currencies.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('currencies.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softRestore($id); Database::getInstance()->commit(); flashMessage('success', 'Currency restored.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to restore.'); }
        redirect('currencies.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('currencies.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('currencies.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->toggleStatusLog($id); Database::getInstance()->commit(); flashMessage('success', 'Status updated.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update status.'); }
        redirect('currencies.php');
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Currency not found.'); redirect('currencies.php'); exit; }
        return $item;
    }
}
