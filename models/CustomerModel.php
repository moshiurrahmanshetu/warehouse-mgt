<?php
/**
 * Customer Model
 * Warehouse Management System - Phase 04
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once __DIR__ . '/BaseModel.php';

class CustomerModel extends BaseModel
{
    protected string $table = 'customers';
    protected string $primaryKey = 'id';
    

    /**
     * Get all customers with optional filtering, search, and pagination.
     */
    public function getAll(array $filters = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array
    {
        $sql    = "SELECT * FROM customers WHERE 1=1";
        $params = [];

        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        } elseif (!empty($filters['only_deleted'])) {
            $sql .= " AND deleted_at IS NOT NULL";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (
                customer_code  LIKE :search OR
                customer_name  LIKE :search OR
                company_name   LIKE :search OR
                phone          LIKE :search OR
                mobile         LIKE :search OR
                email          LIKE :search OR
                tax_number     LIKE :search
            )";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['customer_type'])) {
            $sql .= " AND customer_type = :customer_type";
            $params[':customer_type'] = $filters['customer_type'];
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
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Count total customers for pagination.
     */
    public function countAll(array $filters = [], bool $includeDeleted = false): int
    {
        $sql    = "SELECT COUNT(*) AS total FROM customers WHERE 1=1";
        $params = [];

        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        } elseif (!empty($filters['only_deleted'])) {
            $sql .= " AND deleted_at IS NOT NULL";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (
                customer_code  LIKE :search OR
                customer_name  LIKE :search OR
                company_name   LIKE :search OR
                phone          LIKE :search OR
                mobile         LIKE :search OR
                email          LIKE :search OR
                tax_number     LIKE :search
            )";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['customer_type'])) {
            $sql .= " AND customer_type = :customer_type";
            $params[':customer_type'] = $filters['customer_type'];
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
     * Find a customer by ID (optionally include soft-deleted).
     */
    public function findById(int $id, bool $includeDeleted = false): array|false
    {
        $sql = "SELECT * FROM customers WHERE id = :id";
        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        }
        return $this->db->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Check if a duplicate active customer exists (name + mobile).
     */
    public function duplicateNameMobileExists(string $name, string $mobile, ?int $excludeId = null): bool
    {
        if (empty($mobile)) return false;
        $sql    = "SELECT id FROM customers WHERE customer_name = :name AND mobile = :mobile AND deleted_at IS NULL";
        $params = [':name' => $name, ':mobile' => $mobile];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        return (bool)$this->db->fetchOne($sql, $params);
    }

    /**
     * Check if a duplicate active customer exists (name + email).
     */
    public function duplicateNameEmailExists(string $name, string $email, ?int $excludeId = null): bool
    {
        if (empty($email)) return false;
        $sql    = "SELECT id FROM customers WHERE customer_name = :name AND email = :email AND deleted_at IS NULL";
        $params = [':name' => $name, ':email' => $email];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        return (bool)$this->db->fetchOne($sql, $params);
    }

    /**
     * Insert a new customer. Returns the new ID.
     */
    public function create(array $data): int
    {
        $data['created_by'] = $_SESSION['user_id'] ?? null;

        $fields       = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);

        $sql    = "INSERT INTO customers (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $params = [];
        foreach ($data as $k => $v) {
            $params[':' . $k] = $v;
        }

        $this->db->execute($sql, $params);
        $id = (int)$this->db->lastInsertId();

        logActivity('create_customer', 'customer', "Created Customer: " . ($data['customer_code'] ?? ''), $id);
        return $id;
    }

    /**
     * Update an existing customer.
     */
    public function update(int $id, array $data): bool
    {
        $sets   = [];
        $params = [':id' => $id];
        foreach ($data as $k => $v) {
            $sets[]         = "$k = :$k";
            $params[":$k"] = $v;
        }

        $sql = "UPDATE customers SET " . implode(', ', $sets) . " WHERE id = :id";
        $this->db->execute($sql, $params);

        logActivity('update_customer', 'customer', "Updated Customer ID $id", $id);
    }

    /**
     * Soft-delete a customer.
     */
    public function delete(int $id): void
    {
        $this->db->execute("UPDATE customers SET deleted_at = NOW() WHERE id = :id", [':id' => $id]);
        logActivity('delete_customer', 'customer', "Deleted Customer ID $id", $id);
    }

    /**
     * Restore a soft-deleted customer.
     */
    public function restore(int $id): void
    {
        $this->db->execute("UPDATE customers SET deleted_at = NULL WHERE id = :id", [':id' => $id]);
        logActivity('restore_customer', 'customer', "Restored Customer ID $id", $id);
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
     * Toggle status between active and inactive.
     */
    public function toggleStatus(int $id): void
    {
        $this->db->execute(
            "UPDATE customers SET status = IF(status='active','inactive','active') WHERE id = :id AND deleted_at IS NULL",
            [':id' => $id]
        );
        logActivity('toggle_status_customer', 'customer', "Toggled Status for Customer ID $id", $id);
    }

    /**
     * Get distinct values of a filterable column.
     */
    public function getDistinctField(string $field): array
    {
        if (!in_array($field, ['city', 'country', 'customer_type'], true)) return [];
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT $field FROM customers WHERE $field IS NOT NULL AND $field != '' AND deleted_at IS NULL ORDER BY $field"
        );
        return array_column($rows, $field);
    }
}
