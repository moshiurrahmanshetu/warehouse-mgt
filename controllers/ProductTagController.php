<?php
/**
 * ProductTag Controller - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/ProductTagModel.php';

class ProductTagController
{
    private ProductTagModel $model;

    public function __construct() { $this->model = new ProductTagModel(); }

    public function index(): void
    {
        requirePermission('product_tags.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $page = max(1, (int)($_GET['page'] ?? 1)); $limit = 15; $offset = ($page - 1) * $limit;
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        require_once VIEW_PATH . '/product_tags/index.php';
    }

    public function export(): void
    {
        requirePermission('product_tags.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $items = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        $headers = ['Code', 'Tag Name', 'Status'];
        $data = array_map(fn($r) => [$r['tag_code'], $r['tag_name'], ucfirst($r['status'])], $items);
        exportCsv('product_tags_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    public function create(): void { requirePermission('product_tags.create'); require_once VIEW_PATH . '/product_tags/create.php'; }

    public function store(): void
    {
        requirePermission('product_tags.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('product_tags.php');
        verifyCsrf();
        $name = sanitize($_POST['tag_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Tag Name is required.'); redirect('product_tags.php?action=create'); }
        if ($this->model->nameExists($name)) { flashMessage('error', 'Tag Name already exists.'); redirect('product_tags.php?action=create'); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->create(['tag_code' => generateSequenceCode('tag_code', 'TAG-', 6), 'tag_name' => $name, 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Tag created successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); error_log($e->getMessage()); flashMessage('error', 'Failed to create tag.'); }
        redirect('product_tags.php');
    }

    public function edit(): void { requirePermission('product_tags.edit'); $item = $this->findOrAbort((int)($_GET['id'] ?? 0)); require_once VIEW_PATH . '/product_tags/edit.php'; }

    public function update(): void
    {
        requirePermission('product_tags.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('product_tags.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['tag_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Tag Name is required.'); redirect("product_tags.php?action=edit&id=$id"); }
        if ($this->model->nameExists($name, $id)) { flashMessage('error', 'Tag Name already exists.'); redirect("product_tags.php?action=edit&id=$id"); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, ['tag_name' => $name, 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Tag updated successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update tag.'); }
        redirect('product_tags.php');
    }

    public function delete(): void
    {
        requirePermission('product_tags.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('product_tags.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softDelete($id); Database::getInstance()->commit(); flashMessage('success', 'Tag deleted.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to delete.'); }
        redirect('product_tags.php');
    }

    public function restore(): void
    {
        requirePermission('product_tags.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('product_tags.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softRestore($id); Database::getInstance()->commit(); flashMessage('success', 'Tag restored.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to restore.'); }
        redirect('product_tags.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('product_tags.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('product_tags.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->toggleStatusLog($id); Database::getInstance()->commit(); flashMessage('success', 'Status updated.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update status.'); }
        redirect('product_tags.php');
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Tag not found.'); redirect('product_tags.php'); exit; }
        return $item;
    }
}
