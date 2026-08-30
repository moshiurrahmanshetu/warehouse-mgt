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

    public function getAdminRoleId(): ?int
    {
        $row = $this->db->fetchOne("SELECT id FROM roles WHERE slug = 'admin' AND deleted_at IS NULL LIMIT 1");
        return $row ? (int)$row['id'] : null;
    }

    public function isUserAdmin(int $userId): bool
    {
        $sql = "SELECT 1 FROM user_roles ur
                INNER JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id = :uid AND r.slug = 'admin' AND r.is_active = 1 AND r.deleted_at IS NULL LIMIT 1";
        $row = $this->db->fetchOne($sql, [':uid' => $userId]);
        return !empty($row);
    }

    public function countOtherActiveAdmins(int $excludeUserId = 0): int
    {
        $sql = "SELECT COUNT(DISTINCT u.id) as total
                FROM users u
                INNER JOIN user_roles ur ON ur.user_id = u.id
                INNER JOIN roles r ON r.id = ur.role_id
                WHERE r.slug = 'admin'
                  AND r.is_active = 1
                  AND r.deleted_at IS NULL
                  AND (u.is_active = 1 OR u.status = 'active')
                  AND u.deleted_at IS NULL
                  AND u.id != :exclude_id";
        $row = $this->db->fetchOne($sql, [':exclude_id' => $excludeUserId]);
        return (int)($row['total'] ?? 0);
    }

    public function validateRoleIds(array $roleIds): array
    {
        if (empty($roleIds)) return [];
        $roleIds = array_values(array_unique(array_filter(array_map('intval', $roleIds))));
        if (empty($roleIds)) return [];

        $placeholders = [];
        $params = [];
        foreach ($roleIds as $idx => $id) {
            $key = ":r$idx";
            $placeholders[] = $key;
            $params[$key] = $id;
        }

        $inClause = implode(',', $placeholders);
        $sql = "SELECT id FROM roles WHERE id IN ($inClause) AND is_active = 1 AND deleted_at IS NULL";
        $rows = $this->db->fetchAll($sql, $params);
        return array_map('intval', array_column($rows, 'id'));
    }

    public function getAll(array $filters = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array
    {
        $params = [];
        $where = [];

        if (!$includeDeleted) {
            $where[] = "u.deleted_at IS NULL";
        } elseif (!empty($filters['only_deleted'])) {
            $where[] = "u.deleted_at IS NOT NULL";
        }

        if (!empty($filters['search'])) {
            $where[] = "(u.name LIKE :s1 OR u.email LIKE :s2 OR u.phone LIKE :s3)";
            $params[':s1'] = '%' . $filters['search'] . '%';
            $params[':s2'] = '%' . $filters['search'] . '%';
            $params[':s3'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $where[] = "(u.status = :status OR u.is_active = :is_active)";
            $params[':status'] = $filters['status'];
            $params[':is_active'] = ($filters['status'] === 'active') ? 1 : 0;
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT u.*, 
                       GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ', ') AS role_names,
                       GROUP_CONCAT(DISTINCT r.slug ORDER BY r.slug SEPARATOR ',') AS role_slugs
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id AND r.deleted_at IS NULL
                $whereSql
                GROUP BY u.id
                ORDER BY u.id DESC";

        if ($limit > 0) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function getRolesDisplay(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT r.name FROM roles r
             INNER JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = :uid AND r.is_active = 1 AND r.deleted_at IS NULL ORDER BY r.name",
            [':uid' => $userId]
        );
        return array_column($rows, 'name');
    }

    public function syncRoles(int $userId, array $roleIds): void
    {
        $roleIds = array_values(array_unique(array_filter(array_map('intval', $roleIds))));

        if (empty($roleIds)) {
            throw new Exception("A user must have at least one role assigned.");
        }

        $validRoleIds = $this->validateRoleIds($roleIds);
        if (count($validRoleIds) !== count($roleIds)) {
            throw new Exception("One or more selected roles are invalid or inactive.");
        }

        // Administrator protection check: only if target user is currently an Administrator
        if ($this->isUserAdmin($userId)) {
            $adminRoleId = $this->getAdminRoleId();
            $willKeepAdmin = ($adminRoleId !== null && in_array($adminRoleId, $validRoleIds, true));
            if (!$willKeepAdmin) {
                $otherAdmins = $this->countOtherActiveAdmins($userId);
                if ($otherAdmins < 1) {
                    throw new Exception("Cannot remove the last active Administrator role assignment.");
                }
            }
        }

        $this->db->execute("DELETE FROM user_roles WHERE user_id = :user_id", [':user_id' => $userId]);
        foreach ($validRoleIds as $rid) {
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
        return array_map('intval', array_column($rows, 'role_id'));
    }

    public function softDelete(int $id): bool
    {
        $currentUserId = $_SESSION['user_id'] ?? 0;
        if ($id === (int)$currentUserId) {
            throw new Exception("You cannot delete your own account.");
        }
        
        if ($this->isUserAdmin($id)) {
            $otherAdmins = $this->countOtherActiveAdmins($id);
            if ($otherAdmins < 1) {
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
        if (!$user) {
            throw new Exception("User not found.");
        }

        $newStatus = ($user['is_active'] == 1) ? 0 : 1;
        $statusStr = $newStatus ? 'active' : 'inactive';
        
        if ($newStatus == 0) { // Deactivating
            if ($this->isUserAdmin($id)) {
                $otherAdmins = $this->countOtherActiveAdmins($id);
                if ($otherAdmins < 1) {
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

    /**
     * Find user with password hash (for credential verification).
     */
    public function findWithPassword(int $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT id, name, email, password, phone, avatar, status, is_active, last_login_at, last_login, last_activity, created_at
             FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1",
            [':id' => $id]
        );
    }

    /**
     * Update profile details for a user (self-service).
     */
    public function updateProfile(int $id, array $data): bool
    {
        $allowed = ['name', 'email', 'phone', 'avatar'];
        $filtered = array_intersect_key($data, array_flip($allowed));
        if (empty($filtered)) return false;

        $this->updateById($id, $filtered);
        return true;
    }

    /**
     * Update user password hash.
     */
    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $this->db->execute(
            "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id",
            [':password' => $hashedPassword, ':id' => $id]
        );
        return true;
    }

    /**
     * Update user's last activity timestamp.
     */
    public function touchActivity(int $id): void
    {
        $this->db->execute(
            "UPDATE users SET last_activity = NOW() WHERE id = :id",
            [':id' => $id]
        );
    }
}

