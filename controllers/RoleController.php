<?php
/**
 * Role Controller
 * Warehouse Management System
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/RoleModel.php';

class RoleController
{
    private RoleModel $model;

    public function __construct()
    {
        $this->model = new RoleModel();
    }

    public function index(): void
    {
        requirePermission('roles.view');
        
        $filters = [
            'search' => sanitize($_GET['search'] ?? ''),
            'status' => sanitize($_GET['status'] ?? ''),
            'only_deleted' => !empty($_GET['only_deleted'])
        ];
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $totalRecords = $this->model->countAll($filters, $filters['only_deleted']);
        $items = $this->model->getAll($filters, $limit, $offset, $filters['only_deleted']);
        
        require_once VIEW_PATH . '/roles/index.php';
    }

    public function create(): void
    {
        requirePermission('roles.manage');
        
        // Fetch all permissions for the form
        $db = Database::getInstance();
        $permissions = $db->fetchAll("SELECT * FROM permissions ORDER BY module, name");
        $groupedPermissions = [];
        foreach ($permissions as $p) {
            $groupedPermissions[$p['module']][] = $p;
        }
        
        require_once VIEW_PATH . '/roles/create.php';
    }

    public function store(): void
    {
        requirePermission('roles.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('roles.php');
        }
        verifyCsrf();

        $name = sanitize($_POST['name'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $permissionIds = $_POST['permissions'] ?? [];

        if (empty($name) || empty($slug)) {
            flashMessage('error', 'Name and Slug are required.');
            redirect('roles.php?action=create');
        }

        if ($this->model->slugExists($slug)) {
            flashMessage('error', 'Slug already exists.');
            redirect('roles.php?action=create');
        }

        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            $roleId = $this->model->create([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'is_active' => $isActive,
                'is_system' => 0 // Custom roles are never system by default
            ]);
            
            $this->model->syncPermissions($roleId, $permissionIds);
            
            logActivity('Create', 'roles', "Created role: {$name}");
            
            $db->commit();
            flashMessage('success', 'Role created successfully.');
        } catch (Exception $e) {
            $db->rollBack();
            flashMessage('error', 'Failed to create role.');
        }

        redirect('roles.php');
    }

    public function edit(): void
    {
        requirePermission('roles.manage');
        $id = (int)($_GET['id'] ?? 0);
        $role = $this->findOrAbort($id);
        
        $db = Database::getInstance();
        $permissions = $db->fetchAll("SELECT * FROM permissions ORDER BY module, name");
        $groupedPermissions = [];
        foreach ($permissions as $p) {
            $groupedPermissions[$p['module']][] = $p;
        }
        
        $rolePermissions = $this->model->getRolePermissions($id);
        
        require_once VIEW_PATH . '/roles/edit.php';
    }

    public function update(): void
    {
        requirePermission('roles.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('roles.php');
        }
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $role = $this->findOrAbort($id);
        
        $name = sanitize($_POST['name'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $permissionIds = $_POST['permissions'] ?? [];

        if (!$role['is_system']) {
            if (empty($name) || empty($slug)) {
                flashMessage('error', 'Name and Slug are required.');
                redirect("roles.php?action=edit&id=$id");
            }

            if ($this->model->slugExists($slug, $id)) {
                flashMessage('error', 'Slug already exists.');
                redirect("roles.php?action=edit&id=$id");
            }
        }

        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            $data = [
                'description' => $description
            ];
            
            if (!$role['is_system']) {
                $data['name'] = $name;
                $data['slug'] = $slug;
                $data['is_active'] = $isActive;
            }
            
            $this->model->update($id, $data);
            
            // Only admins should ideally manage permissions of system roles, but for now we sync
            if (hasPermission('permissions.manage')) {
                $this->model->syncPermissions($id, $permissionIds);
            }
            
            logActivity('Update', 'roles', "Updated role: " . ($role['is_system'] ? $role['name'] : $name));
            
            $db->commit();
            flashMessage('success', 'Role updated successfully.');
        } catch (Exception $e) {
            $db->rollBack();
            flashMessage('error', 'Failed to update role.');
        }

        redirect('roles.php');
    }

    public function delete(): void
    {
        requirePermission('roles.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('roles.php');
        }
        verifyCsrf();
        
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $role = $this->model->findById($id);
            $this->model->softDelete($id);
            logActivity('Delete', 'roles', "Deleted role: {$role['name']}");
            Database::getInstance()->commit();
            flashMessage('success', 'Role deleted successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', $e->getMessage() ?: 'Failed to delete role.');
        }
        redirect('roles.php');
    }

    public function restore(): void
    {
        requirePermission('roles.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('roles.php');
        }
        verifyCsrf();
        
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->softRestore($id);
            $role = $this->model->findById($id);
            logActivity('Restore', 'roles', "Restored role: {$role['name']}");
            Database::getInstance()->commit();
            flashMessage('success', 'Role restored successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to restore role.');
        }
        redirect('roles.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('roles.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('roles.php');
        }
        verifyCsrf();
        
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->toggleStatusLog($id);
            Database::getInstance()->commit();
            flashMessage('success', 'Role status updated.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', $e->getMessage() ?: 'Failed to update status.');
        }
        redirect('roles.php');
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) {
            flashMessage('error', 'Role not found.');
            redirect('roles.php');
            exit;
        }
        return $item;
    }
}
