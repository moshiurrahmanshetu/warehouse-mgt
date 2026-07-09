<?php
/**
 * Category Controller - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/CategoryModel.php';

class CategoryController
{
    private CategoryModel $model;

    public function __construct() { $this->model = new CategoryModel(); }

    public function index(): void
    {
        requirePermission('categories.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $page = max(1, (int)($_GET['page'] ?? 1)); $limit = 15; $offset = ($page - 1) * $limit;
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        $parentMap = array_column($this->model->getActiveForDropdown(), 'category_name', 'id');
        require_once VIEW_PATH . '/categories/index.php';
    }

    public function export(): void
    {
        requirePermission('categories.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $items = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        $headers = ['Code', 'Name', 'Description', 'Sort Order', 'Status'];
        $data = array_map(fn($r) => [$r['category_code'], $r['category_name'], $r['description'], $r['sort_order'], ucfirst($r['status'])], $items);
        exportCsv('categories_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    public function create(): void { requirePermission('categories.create'); $parents = $this->model->getActiveForDropdown(); require_once VIEW_PATH . '/categories/create.php'; }

    public function store(): void
    {
        requirePermission('categories.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('categories.php');
        verifyCsrf();
        $name = sanitize($_POST['category_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Category Name is required.'); redirect('categories.php?action=create'); }
        if ($this->model->nameExists($name)) { flashMessage('error', 'Category Name already exists.'); redirect('categories.php?action=create'); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->create([
                'category_code' => generateSequenceCode('category_code', 'CAT-', 6),
                'category_name' => $name,
                'parent_id'     => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'description'   => sanitize($_POST['description'] ?? ''),
                'sort_order'    => (int)($_POST['sort_order'] ?? 0),
                'status'        => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
            ]);
            Database::getInstance()->commit();
            flashMessage('success', 'Category created successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); error_log($e->getMessage()); flashMessage('error', 'Failed to create category.'); }
        redirect('categories.php');
    }

    public function edit(): void
    {
        requirePermission('categories.edit');
        $item = $this->findOrAbort((int)($_GET['id'] ?? 0));
        $parents = $this->model->getActiveForDropdown();
        require_once VIEW_PATH . '/categories/edit.php';
    }

    public function update(): void
    {
        requirePermission('categories.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('categories.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['category_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Category Name is required.'); redirect("categories.php?action=edit&id=$id"); }
        if ($this->model->nameExists($name, $id)) { flashMessage('error', 'Category Name already exists.'); redirect("categories.php?action=edit&id=$id"); }
        // Prevent circular parent
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        if ($parentId && $parentId === $id) { flashMessage('error', 'A category cannot be its own parent.'); redirect("categories.php?action=edit&id=$id"); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, [
                'category_name' => $name,
                'parent_id'     => $parentId,
                'description'   => sanitize($_POST['description'] ?? ''),
                'sort_order'    => (int)($_POST['sort_order'] ?? 0),
                'status'        => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
            ]);
            Database::getInstance()->commit();
            flashMessage('success', 'Category updated successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update category.'); }
        redirect('categories.php');
    }

    public function delete(): void
    {
        requirePermission('categories.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('categories.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($this->model->hasChildren($id)) { flashMessage('error', 'Cannot delete: category has child categories.'); redirect('categories.php'); return; }
        try { Database::getInstance()->beginTransaction(); $this->model->softDelete($id); Database::getInstance()->commit(); flashMessage('success', 'Category deleted.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to delete.'); }
        redirect('categories.php');
    }

    public function restore(): void
    {
        requirePermission('categories.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('categories.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softRestore($id); Database::getInstance()->commit(); flashMessage('success', 'Category restored.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to restore.'); }
        redirect('categories.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('categories.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('categories.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->toggleStatusLog($id); Database::getInstance()->commit(); flashMessage('success', 'Status updated.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update status.'); }
        redirect('categories.php');
    }

    private function findOrAbort(int $id, bool $includeDeleted = false): array
    {
        $item = $this->model->findById($id, $includeDeleted);
        if (!$item) { flashMessage('error', 'Category not found.'); redirect('categories.php'); exit; }
        return $item;
    }
}
