<?php
/**
 * User Controller
 * Warehouse Management System
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/UserModel.php';

class UserController
{
    private UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index(): void
    {
        requirePermission('users.view');
        
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
        
        require_once VIEW_PATH . '/users/index.php';
    }

    public function create(): void
    {
        requirePermission('users.manage');
        $db = Database::getInstance();
        $roles = $db->fetchAll("SELECT * FROM roles WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name");
        require_once VIEW_PATH . '/users/create.php';
    }

    public function store(): void
    {
        requirePermission('users.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('users.php');
        verifyCsrf();

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $roleIds = $_POST['roles'] ?? [];

        if (empty($name) || empty($email) || empty($password)) {
            flashMessage('error', 'Name, Email, and Password are required.');
            redirect('users.php?action=create');
        }

        if ($this->model->emailExists($email)) {
            flashMessage('error', 'Email already exists.');
            redirect('users.php?action=create');
        }
        
        $avatar = $this->handleAvatarUpload();

        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            $userId = $this->model->create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                'avatar' => $avatar,
                'is_active' => $isActive,
                'status' => $isActive ? 'active' : 'inactive'
            ]);
            
            $this->model->syncRoles($userId, $roleIds);
            
            logActivity('Create', 'users', "Created user: {$name}");
            
            $db->commit();
            flashMessage('success', 'User created successfully.');
        } catch (Exception $e) {
            $db->rollBack();
            flashMessage('error', 'Failed to create user.');
        }

        redirect('users.php');
    }

    public function edit(): void
    {
        requirePermission('users.manage');
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->findOrAbort($id);
        
        $db = Database::getInstance();
        $roles = $db->fetchAll("SELECT * FROM roles WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name");
        $userRoles = $this->model->getUserRoles($id);
        
        require_once VIEW_PATH . '/users/edit.php';
    }

    public function update(): void
    {
        requirePermission('users.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('users.php');
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $user = $this->findOrAbort($id);
        
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $roleIds = $_POST['roles'] ?? [];
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email)) {
            flashMessage('error', 'Name and Email are required.');
            redirect("users.php?action=edit&id=$id");
        }

        if ($this->model->emailExists($email, $id)) {
            flashMessage('error', 'Email already exists.');
            redirect("users.php?action=edit&id=$id");
        }

        $avatar = $this->handleAvatarUpload();

        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            $data = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ];
            
            if ($id !== (int)($_SESSION['user_id'] ?? 0)) {
                $data['is_active'] = $isActive;
                $data['status'] = $isActive ? 'active' : 'inactive';
            }
            
            if ($avatar) {
                $data['avatar'] = $avatar;
            }
            
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            }
            
            $this->model->update($id, $data);
            
            // Handle role changes
            $currentRoles = $this->model->getUserRoles($id);
            if (array_diff($currentRoles, $roleIds) || array_diff($roleIds, $currentRoles)) {
                $this->model->syncRoles($id, $roleIds);
                logActivity('Role Assignment', 'users', "Updated roles for user: {$name}");
            }
            
            if (!empty($password)) {
                logActivity('Password Change', 'users', "Changed password for user: {$name}");
            }
            
            logActivity('Update', 'users', "Updated user: {$name}");
            
            $db->commit();
            flashMessage('success', 'User updated successfully.');
        } catch (Exception $e) {
            $db->rollBack();
            flashMessage('error', $e->getMessage() ?: 'Failed to update user.');
        }

        redirect('users.php');
    }

    public function delete(): void
    {
        requirePermission('users.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('users.php');
        verifyCsrf();
        
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $user = $this->model->findById($id);
            $this->model->softDelete($id);
            logActivity('Delete', 'users', "Deleted user: {$user['name']}");
            Database::getInstance()->commit();
            flashMessage('success', 'User deleted successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', $e->getMessage() ?: 'Failed to delete user.');
        }
        redirect('users.php');
    }

    public function restore(): void
    {
        requirePermission('users.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('users.php');
        verifyCsrf();
        
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->softRestore($id);
            $user = $this->model->findById($id);
            logActivity('Restore', 'users', "Restored user: {$user['name']}");
            Database::getInstance()->commit();
            flashMessage('success', 'User restored successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to restore user.');
        }
        redirect('users.php?only_deleted=1');
    }

    public function toggleStatus(): void
    {
        requirePermission('users.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('users.php');
        verifyCsrf();
        
        $id = (int)($_POST['id'] ?? 0);
        try {
            Database::getInstance()->beginTransaction();
            $this->model->toggleStatusLog($id);
            Database::getInstance()->commit();
            flashMessage('success', 'User status updated.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', $e->getMessage() ?: 'Failed to update status.');
        }
        redirect('users.php');
    }

    private function handleAvatarUpload(): ?string
    {
        if (empty($_FILES['avatar']['name'])) {
            return null;
        }

        $file = $_FILES['avatar'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            flashMessage('error', 'Avatar upload failed.');
            return null;
        }

        // Validate size (2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            flashMessage('error', 'Avatar size must be less than 2MB.');
            return null;
        }

        // Validate type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) {
            flashMessage('error', 'Invalid avatar format. Only JPG, PNG, and WebP are allowed.');
            return null;
        }

        // Generate unique filename
        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg'
        };
        $filename = uniqid('avatar_', true) . '.' . $ext;
        
        $uploadDir = BASEPATH . '/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return 'uploads/avatars/' . $filename;
        }

        flashMessage('error', 'Failed to save avatar.');
        return null;
    }

    private function findOrAbort(int $id): array
    {
        $item = $this->model->findById($id);
        if (!$item) {
            flashMessage('error', 'User not found.');
            redirect('users.php');
            exit;
        }
        return $item;
    }
}
