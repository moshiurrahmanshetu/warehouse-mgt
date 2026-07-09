<?php
/**
 * ProductAttribute Controller - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/ProductAttributeModel.php';

class ProductAttributeController
{
    private ProductAttributeModel $model;

    public function __construct() { $this->model = new ProductAttributeModel(); }

    public function index(): void
    {
        requirePermission('attributes.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $page = max(1, (int)($_GET['page'] ?? 1)); $limit = 15; $offset = ($page - 1) * $limit;
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        require_once VIEW_PATH . '/attributes/index.php';
    }

    public function export(): void
    {
        requirePermission('attributes.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $items = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        $headers = ['Code', 'Attribute Name', 'Status'];
        $data = array_map(fn($r) => [$r['attribute_code'], $r['attribute_name'], ucfirst($r['status'])], $items);
        exportCsv('attributes_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    public function create(): void { requirePermission('attributes.create'); require_once VIEW_PATH . '/attributes/create.php'; }

    public function store(): void
    {
        requirePermission('attributes.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attributes.php');
        verifyCsrf();
        $name = sanitize($_POST['attribute_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Attribute Name is required.'); redirect('attributes.php?action=create'); }
        if ($this->model->nameExists($name)) { flashMessage('error', 'Attribute Name already exists.'); redirect('attributes.php?action=create'); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->create(['attribute_code' => generateSequenceCode('attribute_code', 'ATT-', 6), 'attribute_name' => $name, 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Attribute created successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); error_log($e->getMessage()); flashMessage('error', 'Failed to create attribute.'); }
        redirect('attributes.php');
    }

    public function edit(): void { requirePermission('attributes.edit'); $item = $this->findOrAbort((int)($_GET['id'] ?? 0)); require_once VIEW_PATH . '/attributes/edit.php'; }

    public function update(): void
    {
        requirePermission('attributes.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attributes.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['attribute_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Attribute Name is required.'); redirect("attributes.php?action=edit&id=$id"); }
        if ($this->model->nameExists($name, $id)) { flashMessage('error', 'Attribute Name already exists.'); redirect("attributes.php?action=edit&id=$id"); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, ['attribute_name' => $name, 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Attribute updated successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update attribute.'); }
        redirect('attributes.php');
    }

    public function delete(): void
    {
        requirePermission('attributes.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attributes.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        if ($this->model->hasValues($id)) { flashMessage('error', 'Cannot delete: attribute has existing values. Delete values first.'); redirect('attributes.php'); return; }
        try { Database::getInstance()->beginTransaction(); $this->model->softDelete($id); Database::getInstance()->commit(); flashMessage('success', 'Attribute deleted.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to delete.'); }
        redirect('attributes.php');
    }

    public function restore(): void
    {
        requirePermission('attributes.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attributes.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softRestore($id); Database::getInstance()->commit(); flashMessage('success', 'Attribute restored.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to restore.'); }
        redirect('attributes.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('attributes.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attributes.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->toggleStatusLog($id); Database::getInstance()->commit(); flashMessage('success', 'Status updated.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update status.'); }
        redirect('attributes.php');
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Attribute not found.'); redirect('attributes.php'); exit; }
        return $item;
    }
}
