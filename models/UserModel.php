<?php
/**
 * User Model
 * Warehouse Management System
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    protected bool $useCreatedBy = false;

    public function __construct()
    {
        parent::__construct('users');
        $this->searchableFields = ['name', 'email', 'phone'];
    }

    public function create(array $data): int
    {
        return parent::create($data);
    }

    public function softRestore(int $id): bool
    {
        return parent::softRestore($id);
    }

    public function findByEmail(string $email): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE email = :email LIMIT 1",
            [':email' => $email]
        );
    }

    public function findById(int $id, bool $includeDeleted = false): array|false
    {
        return $this->db->fetchOne(
            "SELECT id, name, email, phone, avatar, status, is_active, last_login_at, last_login, last_activity, created_at, deleted_at
             FROM users WHERE id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public function getRoles(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT r.slug FROM roles r
             INNER JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = :uid AND r.is_active = 1",
            [':uid' => $userId]
        );
        return array_column($rows, 'slug');
    }

    public function getPermissions(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT p.slug FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             INNER JOIN user_roles ur       ON ur.role_id = rp.role_id
             WHERE ur.user_id = :uid",
            [':uid' => $userId]
        );
        return array_column($rows, 'slug');
    }

    public function updateLastLogin(int $userId, string $ip): void
    {
        $this->db->execute(
            "UPDATE users SET last_login_at = NOW(), last_login = NOW(), last_login_ip = :ip WHERE id = :id",
            [':ip' => $ip, ':id' => $userId]
        );
    }

    public function countActive(): int
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS total FROM users WHERE status = 'active' OR is_active = 1");
        return (int) ($row['total'] ?? 0);
    }

    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email AND id != :id";
        $row = $this->db->fetchOne($sql, [':email' => $email, ':id' => $excludeId]);
        return ($row['count'] > 0);
    }

    public function syncRoles(int $userId, array $roleIds): void
    {
        // Check if removing last administrator role
        if (in_array(1, $roleIds) === false) {
            // Check if this user had admin role
            $hadAdmin = $this->db->fetchOne("SELECT 1 FROM user_roles WHERE user_id = :uid AND role_id = 1", [':uid' => $userId]);
            if ($hadAdmin) {
                $adminCount = $this->db->fetchOne("SELECT COUNT(DISTINCT user_id) as count FROM user_roles ur JOIN users u ON ur.user_id = u.id WHERE role_id = 1 AND u.is_active = 1 AND u.deleted_at IS NULL AND u.id != :uid", [':uid' => $userId]);
                if ($adminCount['count'] < 1) {
                    throw new Exception("Cannot remove the last active Administrator role assignment.");
                }
            }
        }

        $this->db->execute("DELETE FROM user_roles WHERE user_id = :user_id", [':user_id' => $userId]);
        foreach ($roleIds as $rid) {
            $this->db->execute(
                "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)",
                [':user_id' => $userId, ':role_id' => (int)$rid]
            );
        }
    }

    public function getUserRoles(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT role_id FROM user_roles WHERE user_id = :user_id",
            [':user_id' => $userId]
        );
        return array_column($rows, 'role_id');
    }

    public function softDelete(int $id): bool
    {
        $currentUserId = $_SESSION['user_id'] ?? 0;
        if ($id === (int)$currentUserId) {
            throw new Exception("You cannot delete your own account.");
        }
        
        $roles = $this->getUserRoles($id);
        if (in_array(1, $roles)) {
            $adminCount = $this->db->fetchOne("SELECT COUNT(DISTINCT user_id) as count FROM user_roles ur JOIN users u ON ur.user_id = u.id WHERE role_id = 1 AND u.is_active = 1 AND u.deleted_at IS NULL AND u.id != :uid", [':uid' => $id]);
            if ($adminCount['count'] < 1) {
                throw new Exception("Cannot delete the last active Administrator.");
            }
        }
        
        return parent::softDelete($id);
    }

    public function toggleStatusLog(int $id): bool
    {
        $currentUserId = $_SESSION['user_id'] ?? 0;
        if ($id === (int)$currentUserId) {
            throw new Exception("You cannot deactivate your own account.");
        }
        
        $user = $this->findById($id);
        $newStatus = ($user['is_active'] == 1) ? 0 : 1;
        $statusStr = $newStatus ? 'active' : 'inactive';
        
        if ($newStatus == 0) { // Deactivating
            $roles = $this->getUserRoles($id);
            if (in_array(1, $roles)) {
                $adminCount = $this->db->fetchOne("SELECT COUNT(DISTINCT user_id) as count FROM user_roles ur JOIN users u ON ur.user_id = u.id WHERE role_id = 1 AND u.is_active = 1 AND u.deleted_at IS NULL AND u.id != :uid", [':uid' => $id]);
                if ($adminCount['count'] < 1) {
                    throw new Exception("Cannot deactivate the last active Administrator.");
                }
            }
        }
        
        $res = parent::update($id, ['is_active' => $newStatus, 'status' => $statusStr]);
        if ($res) {
            logActivity(($newStatus ? 'Activate' : 'Deactivate'), 'users', "User: {$user['name']}");
        }
        return $res;
    }
}
