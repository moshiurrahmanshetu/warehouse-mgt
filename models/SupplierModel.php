<?php
/**
 * Supplier Model
 * Warehouse Management System
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once __DIR__ . '/BaseModel.php';

class SupplierModel extends BaseModel
{
    protected string $table = 'suppliers';
    protected string $primaryKey = 'id';
    

    /**
     * Get all suppliers with optional filtering, search, and pagination.
     */
    public function getAll(array $filters = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array
    {
        $sql = "SELECT * FROM suppliers WHERE 1=1";
        $params = [];

        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        } elseif (!empty($filters['only_deleted'])) {
            $sql .= " AND deleted_at IS NOT NULL";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (supplier_code LIKE :search OR company_name LIKE :search OR contact_person LIKE :search OR phone LIKE :search OR mobile LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['city'])) {
            $sql .= " AND city = :city";
            $params[':city'] = $filters['city'];
        }

        if (!empty($filters['country'])) {
            $sql .= " AND country = :country";
            $params[':country'] = $filters['country'];
        }

        $sql .= " ORDER BY id DESC";

        if ($limit > 0) {
            $sql .= " LIMIT $limit OFFSET $offset"; // Safe since limit and offset are integers
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Count total suppliers for pagination.
     */
    public function countAll(array $filters = [], bool $includeDeleted = false): int
    {
        $sql = "SELECT COUNT(*) AS total FROM suppliers WHERE 1=1";
        $params = [];

        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        } elseif (!empty($filters['only_deleted'])) {
            $sql .= " AND deleted_at IS NOT NULL";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (supplier_code LIKE :search OR company_name LIKE :search OR contact_person LIKE :search OR phone LIKE :search OR mobile LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['city'])) {
            $sql .= " AND city = :city";
            $params[':city'] = $filters['city'];
        }

        if (!empty($filters['country'])) {
            $sql .= " AND country = :country";
            $params[':country'] = $filters['country'];
        }

        $row = $this->db->fetchOne($sql, $params);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Find by ID (can optionally include soft-deleted).
     */
    public function findById(int $id, bool $includeDeleted = false): array|false
    {
        $sql = "SELECT * FROM suppliers WHERE id = :id";
        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        }
        return $this->db->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Check if company name exists.
     */
    public function companyNameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM suppliers WHERE company_name = :name AND deleted_at IS NULL";
        $params = [':name' => $name];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        return (bool)$this->db->fetchOne($sql, $params);
    }

    /**
     * Insert new supplier.
     */
    public function create(array $data): int
    {
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);
        
        $sql = "INSERT INTO suppliers (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $params = [];
        foreach ($data as $k => $v) {
            $params[':'.$k] = $v;
        }
        
        $this->db->execute($sql, $params);
        $id = (int)$this->db->lastInsertId();
        
        logActivity('create_supplier', 'supplier', "Created Supplier: " . ($data['supplier_code'] ?? ''));
        return $id;
    }

    /**
     * Update supplier.
     */
    public function update(int $id, array $data): bool
    {
        $sets = [];
        $params = [':id' => $id];
        foreach ($data as $k => $v) {
            $sets[] = "$k = :$k";
            $params[':'.$k] = $v;
        }
        
        $sql = "UPDATE suppliers SET " . implode(', ', $sets) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        
        logActivity('update_supplier', 'supplier', "Updated Supplier ID $id");
    }

    /**
     * Soft Delete a supplier.
     */
    public function delete(int $id): void
    {
        $this->db->execute("UPDATE suppliers SET deleted_at = NOW() WHERE id = :id", [':id' => $id]);
        logActivity('delete_supplier', 'supplier', "Deleted Supplier ID $id");
    }

    /**
     * Restore a soft-deleted supplier.
     */
    public function restore(int $id): void
    {
        $this->db->execute("UPDATE suppliers SET deleted_at = NULL WHERE id = :id", [':id' => $id]);
        logActivity('restore_supplier', 'supplier', "Restored Supplier ID $id");
    }

    public function softDelete(int $id): bool
    {
        $this->delete($id);
        return true;
    }

    public function softRestore(int $id): bool
    {
        $this->restore($id);
        return true;
    }

    /**
     * Get distinct cities and countries for filter dropdowns.
     */
    public function getDistinctField(string $field): array
    {
        if (!in_array($field, ['city', 'country'])) return [];
        $rows = $this->db->fetchAll("SELECT DISTINCT $field FROM suppliers WHERE $field IS NOT NULL AND $field != '' AND deleted_at IS NULL ORDER BY $field");
        return array_column($rows, $field);
    }
}
