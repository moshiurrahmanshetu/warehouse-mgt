<?php
/**
 * Unit Controller - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/UnitModel.php';

class UnitController
{
    private UnitModel $model;

    public function __construct() { $this->model = new UnitModel(); }

    public function index(): void
    {
        requirePermission('units.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $page = max(1, (int)($_GET['page'] ?? 1)); $limit = 15; $offset = ($page - 1) * $limit;
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        $baseUnits = $this->model->getActiveForDropdown();
        $baseUnitMap = array_column($baseUnits, 'unit_name', 'id');
        require_once VIEW_PATH . '/units/index.php';
    }

    public function export(): void
    {
        requirePermission('units.view');
        $filters = ['search' => sanitize($_GET['search'] ?? ''), 'status' => sanitize($_GET['status'] ?? ''), 'only_deleted' => !empty($_GET['only_deleted'])];
        $items = $this->model->getAll($filters, 0, 0, $filters['only_deleted']);
        $headers = ['Code', 'Unit Name', 'Short Name', 'Type', 'Conversion Factor', 'Status'];
        $data = array_map(fn($r) => [$r['unit_code'], $r['unit_name'], $r['short_name'], $r['unit_type'], $r['conversion_factor'], ucfirst($r['status'])], $items);
        exportCsv('units_' . date('Ymd_His') . '.csv', $headers, $data);
    }

    public function create(): void { requirePermission('units.create'); $baseUnits = $this->model->getActiveForDropdown(); require_once VIEW_PATH . '/units/create.php'; }

    public function store(): void
    {
        requirePermission('units.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('units.php');
        verifyCsrf();
        $name = sanitize($_POST['unit_name'] ?? '');
        $shortName = sanitize($_POST['short_name'] ?? '');
        if (empty($name) || empty($shortName)) { flashMessage('error', 'Unit Name and Short Name are required.'); redirect('units.php?action=create'); }
        if ($this->model->nameExists($name)) { flashMessage('error', 'Unit Name already exists.'); redirect('units.php?action=create'); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->create([
                'unit_code'         => generateSequenceCode('unit_code', 'UNT-', 6),
                'unit_name'         => $name,
                'short_name'        => $shortName,
                'unit_type'         => sanitize($_POST['unit_type'] ?? ''),
                'base_unit_id'      => !empty($_POST['base_unit_id']) ? (int)$_POST['base_unit_id'] : null,
                'conversion_factor' => (float)($_POST['conversion_factor'] ?? 1),
                'status'            => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
            ]);
            Database::getInstance()->commit(); flashMessage('success', 'Unit created successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); error_log($e->getMessage()); flashMessage('error', 'Failed to create unit.'); }
        redirect('units.php');
    }

    public function edit(): void { requirePermission('units.edit'); $item = $this->findOrAbort((int)($_GET['id'] ?? 0)); $baseUnits = $this->model->getActiveForDropdown(); require_once VIEW_PATH . '/units/edit.php'; }

    public function update(): void
    {
        requirePermission('units.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('units.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['unit_name'] ?? '');
        $shortName = sanitize($_POST['short_name'] ?? '');
        if (empty($name) || empty($shortName)) { flashMessage('error', 'Unit Name and Short Name are required.'); redirect("units.php?action=edit&id=$id"); }
        if ($this->model->nameExists($name, $id)) { flashMessage('error', 'Unit Name already exists.'); redirect("units.php?action=edit&id=$id"); }
        try {
            Database::getInstance()->beginTransaction();
            $this->model->update($id, ['unit_name' => $name, 'short_name' => $shortName, 'unit_type' => sanitize($_POST['unit_type'] ?? ''), 'base_unit_id' => !empty($_POST['base_unit_id']) ? (int)$_POST['base_unit_id'] : null, 'conversion_factor' => (float)($_POST['conversion_factor'] ?? 1), 'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active']);
            Database::getInstance()->commit(); flashMessage('success', 'Unit updated successfully.');
        } catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update unit.'); }
        redirect('units.php');
    }

    public function delete(): void
    {
        requirePermission('units.delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('units.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softDelete($id); Database::getInstance()->commit(); flashMessage('success', 'Unit deleted.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to delete unit.'); }
        redirect('units.php');
    }

    public function restore(): void
    {
        requirePermission('units.restore');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('units.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->softRestore($id); Database::getInstance()->commit(); flashMessage('success', 'Unit restored.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to restore unit.'); }
        redirect('units.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('units.edit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('units.php');
        verifyCsrf(); $id = (int)($_POST['id'] ?? 0);
        try { Database::getInstance()->beginTransaction(); $this->model->toggleStatusLog($id); Database::getInstance()->commit(); flashMessage('success', 'Status updated.'); }
        catch (Exception $e) { Database::getInstance()->rollBack(); flashMessage('error', 'Failed to update status.'); }
        redirect('units.php');
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Unit not found.'); redirect('units.php'); exit; }
        return $item;
    }
}
