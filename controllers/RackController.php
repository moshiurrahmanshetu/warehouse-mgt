<?php
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/RackModel.php';
class RackController {
    private RackModel $model;
    public function __construct() { $this->model = new RackModel(); }
    public function index(): void {
        $items = $this->model->getAll();
        require_once VIEW_PATH . '/racks/index.php';
    }
    public function create(): void {
        requirePermission('racks.manage');
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/racks/create.php';
    }
    public function store(): void {
        requirePermission('racks.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('racks.php');
        verifyCsrf();
        $code = sanitize($_POST['rack_code'] ?? '');
        $name = sanitize($_POST['rack_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect('racks.php?action=create'); }
        if ($this->model->codeExists($code)) { flashMessage('error', 'Code already exists.'); redirect('racks.php?action=create'); }
        $data = ['rack_code' => $code, 'rack_name' => $name, 'status' => $status];
        $parent_id = (int)($_POST['zone_id'] ?? 0);
        if (!$this->model->parentExists($parent_id)) {
            flashMessage('error', 'Invalid parent selected.');
            redirect('racks.php?action=' . ($id ? "edit&id=$id" : 'create'));
        }
        $data['zone_id'] = $parent_id;
        $this->model->create($data);
        flashMessage('success', 'Rack created successfully.');
        redirect('racks.php');
    }
    public function edit(): void {
        requirePermission('racks.manage');
        $id = (int)($_GET['id'] ?? 0);
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Not found.'); redirect('racks.php'); }
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/racks/edit.php';
    }
    public function update(): void {
        requirePermission('racks.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('racks.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $code = sanitize($_POST['rack_code'] ?? '');
        $name = sanitize($_POST['rack_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect("racks.php?action=edit&id=$id"); }
        if ($this->model->codeExists($code, $id)) { flashMessage('error', 'Code already exists.'); redirect("racks.php?action=edit&id=$id"); }
        $data = ['rack_code' => $code, 'rack_name' => $name, 'status' => $status];
        $parent_id = (int)($_POST['zone_id'] ?? 0);
        if (!$this->model->parentExists($parent_id)) {
            flashMessage('error', 'Invalid parent selected.');
            redirect('racks.php?action=' . ($id ? "edit&id=$id" : 'create'));
        }
        $data['zone_id'] = $parent_id;
        $this->model->update($id, $data);
        flashMessage('success', 'Rack updated successfully.');
        redirect('racks.php');
    }
    public function delete(): void {
        requirePermission('racks.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('racks.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->delete($id);
            Database::getInstance()->commit();
            flashMessage('success', 'Rack deleted successfully.');
        } catch (PDOException $ex) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Cannot delete because it is in use.');
        }
        redirect('racks.php');
    }
}