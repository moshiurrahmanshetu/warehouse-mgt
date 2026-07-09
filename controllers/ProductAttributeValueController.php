<?php
/**
 * ProductAttributeValue Controller - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/ProductAttributeValueModel.php';
require_once MODEL_PATH . '/ProductAttributeModel.php';

class ProductAttributeValueController
{
    private ProductAttributeValueModel $model;
    private ProductAttributeModel $attributeModel;

    public function __construct() { $this->model = new ProductAttributeValueModel(); $this->attributeModel = new ProductAttributeModel(); }

    public function index(): void
    {
        requirePermission('attribute_values.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'attribute_id' => (int)($_GET['attribute_id'] ?? 0), 'only_deleted' => !empty($_GET['only_deleted'])];
        $page = max(1, (int)($_GET['page'] ?? 1)); $limit = 15; $offset = ($page - 1) * $limit;
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        $attributes = $this->attributeModel->getActiveForDropdown();
        require_once VIEW_PATH . '/attribute_values/index.php';
    }

    public function export(): void
    {
        requirePermission('attribute_values.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'attribute_id' => (int)($_GET['attribute_id'] ?? 0), 'only_deleted' => !empty($_GET['only_deleted'])];
        $items = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        $headers = ['Attribute', 'Value', 'Sort Order', 'Status'];
        $data = array_map(fn($r) => [$r['attribute_name'], $r['value'], $r['sort_order'], ucfirst($r['status'])], $items);
        exportCsv('attribute_values_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    public function create(): void { requirePermission('attribute_values.create'); $attributes = $this->attributeModel->getActiveForDropdown(); require_once VIEW_PATH . '/attribute_values/create.php'; }

    public function store(): void
    {
        requirePermission('attribute_values.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attribute_values.php');
        verifyCsrf();
        $attributeId = (int)($_POST['attribute_id'] ?? 0);
        $value = sanitize($_POST['value'] ?? '');
        if (empty($value) || !$attributeId) { flashMessage('error', 'Attribute and Value are required.'); redirect('attribute_values.php?action=create'); }
        if ($this->model->valueExistsForAttribute($attributeId, $value)) { flashMessage('error', 'This value already exists for this attribute.'); redirect('attribute_values.php?action=create'); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->create(['attribute_id' => $attributeId, 'value' => $value, 'sort_order' => (int)($_POST['sort_order'] ?? 0), 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Attribute Value created successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); error_log($e->getMessage()); flashMessage('error', 'Failed to create attribute value.'); }
        redirect('attribute_values.php');
    }

    public function edit(): void { requirePermission('attribute_values.edit'); $item = $this->findOrAbort((int)($_GET['id'] ?? 0)); $attributes = $this->attributeModel->getActiveForDropdown(); require_once VIEW_PATH . '/attribute_values/edit.php'; }

    public function update(): void
    {
        requirePermission('attribute_values.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attribute_values.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $attributeId = (int)($_POST['attribute_id'] ?? 0);
        $value = sanitize($_POST['value'] ?? '');
        if (empty($value) || !$attributeId) { flashMessage('error', 'Attribute and Value are required.'); redirect("attribute_values.php?action=edit&id=$id"); }
        if ($this->model->valueExistsForAttribute($attributeId, $value, $id)) { flashMessage('error', 'This value already exists for this attribute.'); redirect("attribute_values.php?action=edit&id=$id"); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, ['attribute_id' => $attributeId, 'value' => $value, 'sort_order' => (int)($_POST['sort_order'] ?? 0), 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Attribute Value updated successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update.'); }
        redirect('attribute_values.php');
    }

    public function delete(): void
    {
        requirePermission('attribute_values.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attribute_values.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softDelete($id); Database::getInstance()->commit(); flashMessage('success', 'Attribute Value deleted.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to delete.'); }
        redirect('attribute_values.php');
    }

    public function restore(): void
    {
        requirePermission('attribute_values.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attribute_values.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softRestore($id); Database::getInstance()->commit(); flashMessage('success', 'Attribute Value restored.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to restore.'); }
        redirect('attribute_values.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('attribute_values.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('attribute_values.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->toggleStatusLog($id); Database::getInstance()->commit(); flashMessage('success', 'Status updated.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update status.'); }
        redirect('attribute_values.php');
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Attribute Value not found.'); redirect('attribute_values.php'); exit; }
        return $item;
    }
}
