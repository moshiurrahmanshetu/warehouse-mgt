<?php
/**
 * Role Model
 * Warehouse Management System
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once __DIR__ . '/BaseModel.php';

class RoleModel extends BaseModel
{
    protected string $table = 'roles';
    protected string $primaryKey = 'id';
    protected bool $useCreatedBy = false;

    public function __construct()
    {
        parent::__construct('roles');
        $this->searchableFields = ['name', 'slug', 'description'];
    }

    public function softRestore(int $id): bool
    {
        return parent::softRestore($id);
    }

    public function create(array $data): int
    {
        return parent::create($data);
    }

    public function update(int $id, array $data): bool
    {
        // Enforce system role rules
        $role = $this->findById($id);
        if ($role && $role['is_system']) {
            // Cannot rename or deactivate system roles
            unset($data['name']);
            unset($data['slug']);
            unset($data['is_active']);
            unset($data['is_system']);
        }
        return parent::update($id, $data);
    }

    public function softDelete(int $id): bool
    {
        $role = $this->findById($id);
        if ($role && $role['is_system']) {
            throw new Exception("System roles cannot be deleted.");
        }
        return parent::softDelete($id);
    }

    public function toggleStatusLog(int $id): bool
    {
        $role = $this->findById($id);
        if ($role && $role['is_system']) {
            throw new Exception("System roles cannot be deactivated.");
        }
        $newStatus = ($role['is_active'] == 1) ? 0 : 1;
        $res = parent::update($id, ['is_active' => $newStatus]);
        if ($res) {
            logActivity(($newStatus ? 'Activate' : 'Deactivate'), 'roles', "Role: {$role['name']}");
        }
        return $res;
    }

    public function nameExists(string $name, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name AND id != :id";
        $row = $this->db->fetchOne($sql, [':name' => $name, ':id' => $excludeId]);
        return ($row['count'] > 0);
    }
    
    public function slugExists(string $slug, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug AND id != :id";
        $row = $this->db->fetchOne($sql, [':slug' => $slug, ':id' => $excludeId]);
        return ($row['count'] > 0);
    }

    // Role Permissions Management
    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $this->db->execute("DELETE FROM role_permissions WHERE role_id = :role_id", [':role_id' => $roleId]);
        foreach ($permissionIds as $pid) {
            $this->db->execute(
                "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)",
                [':role_id' => $roleId, ':permission_id' => (int)$pid]
            );
        }
    }

    public function getRolePermissions(int $roleId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT permission_id FROM role_permissions WHERE role_id = :role_id",
            [':role_id' => $roleId]
        );
        return array_column($rows, 'permission_id');
    }
}
