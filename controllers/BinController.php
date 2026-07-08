<?php
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BinModel.php';
class BinController {
    private BinModel $model;
    public function __construct() { $this->model = new BinModel(); }
    public function index(): void {
        $items = $this->model->getAll();
        require_once VIEW_PATH . '/bins/index.php';
    }
    public function create(): void {
        requirePermission('bins.manage');
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/bins/create.php';
    }
    public function store(): void {
        requirePermission('bins.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('bins.php');
        verifyCsrf();
        $code = sanitize($_POST['bin_code'] ?? '');
        $name = sanitize($_POST['bin_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect('bins.php?action=create'); }
        if ($this->model->codeExists($code)) { flashMessage('error', 'Code already exists.'); redirect('bins.php?action=create'); }
        $data = ['bin_code' => $code, 'bin_name' => $name, 'status' => $status];
        $parent_id = (int)($_POST['shelf_id'] ?? 0);
        if (!$this->model->parentExists($parent_id)) {
            flashMessage('error', 'Invalid parent selected.');
            redirect('bins.php?action=' . ($id ? "edit&id=$id" : 'create'));
        }
        $data['shelf_id'] = $parent_id;
        $this->model->create($data);
        flashMessage('success', 'Bin created successfully.');
        redirect('bins.php');
    }
    public function edit(): void {
        requirePermission('bins.manage');
        $id = (int)($_GET['id'] ?? 0);
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Not found.'); redirect('bins.php'); }
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/bins/edit.php';
    }
    public function update(): void {
        requirePermission('bins.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('bins.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $code = sanitize($_POST['bin_code'] ?? '');
        $name = sanitize($_POST['bin_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect("bins.php?action=edit&id=$id"); }
        if ($this->model->codeExists($code, $id)) { flashMessage('error', 'Code already exists.'); redirect("bins.php?action=edit&id=$id"); }
        $data = ['bin_code' => $code, 'bin_name' => $name, 'status' => $status];
        $parent_id = (int)($_POST['shelf_id'] ?? 0);
        if (!$this->model->parentExists($parent_id)) {
            flashMessage('error', 'Invalid parent selected.');
            redirect('bins.php?action=' . ($id ? "edit&id=$id" : 'create'));
        }
        $data['shelf_id'] = $parent_id;
        $this->model->update($id, $data);
        flashMessage('success', 'Bin updated successfully.');
        redirect('bins.php');
    }
    public function delete(): void {
        requirePermission('bins.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('bins.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->delete($id);
            Database::getInstance()->commit();
            flashMessage('success', 'Bin deleted successfully.');
        } catch (PDOException $ex) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Cannot delete because it is in use.');
        }
        redirect('bins.php');
    }
}