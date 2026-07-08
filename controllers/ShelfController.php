<?php
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/ShelfModel.php';
class ShelfController {
    private ShelfModel $model;
    public function __construct() { $this->model = new ShelfModel(); }
    public function index(): void {
        $items = $this->model->getAll();
        require_once VIEW_PATH . '/shelves/index.php';
    }
    public function create(): void {
        requirePermission('shelves.manage');
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/shelves/create.php';
    }
    public function store(): void {
        requirePermission('shelves.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('shelves.php');
        verifyCsrf();
        $code = sanitize($_POST['shelf_code'] ?? '');
        $name = sanitize($_POST['shelf_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect('shelves.php?action=create'); }
        if ($this->model->codeExists($code)) { flashMessage('error', 'Code already exists.'); redirect('shelves.php?action=create'); }
        $data = ['shelf_code' => $code, 'shelf_name' => $name, 'status' => $status];
        $parent_id = (int)($_POST['rack_id'] ?? 0);
        if (!$this->model->parentExists($parent_id)) {
            flashMessage('error', 'Invalid parent selected.');
            redirect('shelves.php?action=' . ($id ? "edit&id=$id" : 'create'));
        }
        $data['rack_id'] = $parent_id;
        $this->model->create($data);
        flashMessage('success', 'Shelf created successfully.');
        redirect('shelves.php');
    }
    public function edit(): void {
        requirePermission('shelves.manage');
        $id = (int)($_GET['id'] ?? 0);
        $item = $this->model->findById($id);
        if (!$item) { flashMessage('error', 'Not found.'); redirect('shelves.php'); }
        $parents = method_exists($this->model, 'getAllParents') ? $this->model->getAllParents() : [];
        require_once VIEW_PATH . '/shelves/edit.php';
    }
    public function update(): void {
        requirePermission('shelves.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('shelves.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $code = sanitize($_POST['shelf_code'] ?? '');
        $name = sanitize($_POST['shelf_name'] ?? '');
        $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
        if (empty($code) || empty($name)) { flashMessage('error', 'Code and Name are required.'); redirect("shelves.php?action=edit&id=$id"); }
        if ($this->model->codeExists($code, $id)) { flashMessage('error', 'Code already exists.'); redirect("shelves.php?action=edit&id=$id"); }
        $data = ['shelf_code' => $code, 'shelf_name' => $name, 'status' => $status];
        $parent_id = (int)($_POST['rack_id'] ?? 0);
        if (!$this->model->parentExists($parent_id)) {
            flashMessage('error', 'Invalid parent selected.');
            redirect('shelves.php?action=' . ($id ? "edit&id=$id" : 'create'));
        }
        $data['rack_id'] = $parent_id;
        $this->model->update($id, $data);
        flashMessage('success', 'Shelf updated successfully.');
        redirect('shelves.php');
    }
    public function delete(): void {
        requirePermission('shelves.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('shelves.php');
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->delete($id);
            Database::getInstance()->commit();
            flashMessage('success', 'Shelf deleted successfully.');
        } catch (PDOException $ex) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Cannot delete because it is in use.');
        }
        redirect('shelves.php');
    }
}