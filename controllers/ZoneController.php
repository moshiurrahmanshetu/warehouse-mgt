<?php
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/ZoneModel.php';
class ZoneController {
    private ZoneModel $model;
    public function __construct() { $this->model = new ZoneModel(); }
    public function index(): void {
        $items = $this->model->getAll();
        require_once VIEW_PATH . '/zones/index.php';
    }
    public function create(): void {
        requirePermission('zones.manage');
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/zones/create.php';
    }
    public function store(): void {
        requirePermission('zones.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('zones.php');
        verifyCsrf();
        $code = sanitize($_POST['zone_code'] ?? '');
        $name = sanitize($_POST['zone_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect('zones.php?action=create'); }
        if ($this->model->codeExists($code)) { flashMessage('error', 'Code already exists.'); redirect('zones.php?action=create'); }
        $data = ['zone_code' => $code, 'zone_name' => $name, 'status' => $status];
        $parent_id = (int)($_POST['warehouse_id'] ?? 0);
        if (!$this->model->parentExists($parent_id)) {
            flashMessage('error', 'Invalid parent selected.');
            redirect('zones.php?action=' . ($id ? "edit&id=$id" : 'create'));
        }
        $data['warehouse_id'] = $parent_id;
        $this->model->create($data);
        flashMessage('success', 'Zone created successfully.');
        redirect('zones.php');
    }
    public function edit(): void {
        requirePermission('zones.manage');
        $id = (int)($_GET['id'] ?? 0);
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Not found.'); redirect('zones.php'); }
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/zones/edit.php';
    }
    public function update(): void {
        requirePermission('zones.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('zones.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $code = sanitize($_POST['zone_code'] ?? '');
        $name = sanitize($_POST['zone_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect("zones.php?action=edit&id=$id"); }
        if ($this->model->codeExists($code, $id)) { flashMessage('error', 'Code already exists.'); redirect("zones.php?action=edit&id=$id"); }
        $data = ['zone_code' => $code, 'zone_name' => $name, 'status' => $status];
        $parent_id = (int)($_POST['warehouse_id'] ?? 0);
        if (!$this->model->parentExists($parent_id)) {
            flashMessage('error', 'Invalid parent selected.');
            redirect('zones.php?action=' . ($id ? "edit&id=$id" : 'create'));
        }
        $data['warehouse_id'] = $parent_id;
        $this->model->update($id, $data);
        flashMessage('success', 'Zone updated successfully.');
        redirect('zones.php');
    }
    public function delete(): void {
        requirePermission('zones.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('zones.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->delete($id);
            Database::getInstance()->commit();
            flashMessage('success', 'Zone deleted successfully.');
        } catch (PDOException $ex) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Cannot delete because it is in use.');
        }
        redirect('zones.php');
    }
}