<?php
/**
 * Brand Controller - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BrandModel.php';

class BrandController
{
    private BrandModel $model;

    public function __construct() { $this->model = new BrandModel(); }

    public function index(): void
    {
        requirePermission('brands.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $page = max(1, (int)($_GET['page'] ?? 1)); $limit = 15; $offset = ($page - 1) * $limit;
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        require_once VIEW_PATH . '/brands/index.php';
    }

    public function export(): void
    {
        requirePermission('brands.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $items = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        $headers = ['Code', 'Brand Name', 'Website', 'Status'];
        $data = array_map(fn($r) => [$r['brand_code'], $r['brand_name'], $r['website'], ucfirst($r['status'])], $items);
        exportCsv('brands_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    public function create(): void { requirePermission('brands.create'); require_once VIEW_PATH . '/brands/create.php'; }

    public function store(): void
    {
        requirePermission('brands.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('brands.php');
        verifyCsrf();
        $name = sanitize($_POST['brand_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Brand Name is required.'); redirect('brands.php?action=create'); }
        if ($this->model->nameExists($name)) { flashMessage('error', 'Brand Name already exists.'); redirect('brands.php?action=create'); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->create([
                'brand_code'  => generateSequenceCode('brand_code', 'BRD-', 6),
                'brand_name'  => $name,
                'description' => sanitize($_POST['description'] ?? ''),
                'website'     => sanitize($_POST['website'] ?? ''),
                'status'      => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
            ]);
            Database::getInstance()->commit(); flashMessage('success', 'Brand created successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); error_log($e->getMessage()); flashMessage('error', 'Failed to create brand.'); }
        redirect('brands.php');
    }

    public function edit(): void { requirePermission('brands.edit'); $item = $this->findOrAbort((int)($_GET['id'] ?? 0)); require_once VIEW_PATH . '/brands/edit.php'; }

    public function update(): void
    {
        requirePermission('brands.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('brands.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['brand_name'] ?? '');
        if (empty($name)) { flashMessage('error', 'Brand Name is required.'); redirect("brands.php?action=edit&id=$id"); }
        if ($this->model->nameExists($name, $id)) { flashMessage('error', 'Brand Name already exists.'); redirect("brands.php?action=edit&id=$id"); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, ['brand_name' => $name, 'description' => sanitize($_POST['description'] ?? ''), 'website' => sanitize($_POST['website'] ?? ''), 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Brand updated successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update brand.'); }
        redirect('brands.php');
    }

    public function delete(): void
    {
        requirePermission('brands.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('brands.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softDelete($id); Database::getInstance()->commit(); flashMessage('success', 'Brand deleted.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to delete brand.'); }
        redirect('brands.php');
    }

    public function restore(): void
    {
        requirePermission('brands.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('brands.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softRestore($id); Database::getInstance()->commit(); flashMessage('success', 'Brand restored.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to restore brand.'); }
        redirect('brands.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('brands.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('brands.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->toggleStatusLog($id); Database::getInstance()->commit(); flashMessage('success', 'Status updated.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update status.'); }
        redirect('brands.php');
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Brand not found.'); redirect('brands.php'); exit; }
        return $item;
    }
}
