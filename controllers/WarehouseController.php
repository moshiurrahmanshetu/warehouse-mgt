<?php
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/WarehouseModel.php';
class WarehouseController {
    private WarehouseModel $model;
    public function __construct() { $this->model = new WarehouseModel(); }
    public function index(): void {
        $items = $this->model->getAll();
        require_once VIEW_PATH . '/warehouses/index.php';
    }
    public function create(): void {
        requirePermission('warehouses.manage');
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/warehouses/create.php';
    }
    public function store(): void {
        requirePermission('warehouses.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('warehouses.php');
        verifyCsrf();
        $code = sanitize($_POST['warehouse_code'] ?? '');
        $name = sanitize($_POST['warehouse_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect('warehouses.php?action=create'); }
        if ($this->model->codeExists($code)) { flashMessage('error', 'Code already exists.'); redirect('warehouses.php?action=create'); }
        $data = ['warehouse_code' => $code, 'warehouse_name' => $name, 'status' => $status];

        $this->model->create($data);
        flashMessage('success', 'Warehouse created successfully.');
        redirect('warehouses.php');
    }
    public function edit(): void {
        requirePermission('warehouses.manage');
        $id = (int)($_GET['id'] ?? 0);
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Not found.'); redirect('warehouses.php'); }
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/warehouses/edit.php';
    }
    public function update(): void {
        requirePermission('warehouses.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('warehouses.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $code = sanitize($_POST['warehouse_code'] ?? '');
        $name = sanitize($_POST['warehouse_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect("warehouses.php?action=edit&id=$id"); }
        if ($this->model->codeExists($code, $id)) { flashMessage('error', 'Code already exists.'); redirect("warehouses.php?action=edit&id=$id"); }
        $data = ['warehouse_code' => $code, 'warehouse_name' => $name, 'status' => $status];

        $this->model->update($id, $data);
        flashMessage('success', 'Warehouse updated successfully.');
        redirect('warehouses.php');
    }
    public function delete(): void {
        requirePermission('warehouses.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('warehouses.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->delete($id);
            Database::getInstance()->commit();
            flashMessage('success', 'Warehouse deleted successfully.');
        } catch (PDOException $ex) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Cannot delete because it is in use.');
        }
        redirect('warehouses.php');
    }
}