<?php
/**
 * Base CRUD Model
 * Provides reusable query logic for all simple entity models.
 * Warehouse Management System - Phase 05
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));

abstract class BaseModel
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    /**
     * Set to false in child models whose table has no 'created_by' column.
     * When true, BaseModel::insert() appends created_by = current session user.
     */
    protected bool $useCreatedBy = true;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Build a WHERE clause from common filter params.
     * Subclasses may override $searchColumns.
     */
    protected array $searchColumns = [];

    public function getAll(array $filters = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array
    {
        [$sql, $params] = $this->buildQuery('*', $filters, $includeDeleted);
        $sql .= " ORDER BY id DESC";
        if ($limit > 0) $sql .= " LIMIT $limit OFFSET $offset";
        return $this->db->fetchAll($sql, $params);
    }

    public function countAll(array $filters = [], bool $includeDeleted = false): int
    {
        [$sql, $params] = $this->buildQuery('COUNT(*) AS total', $filters, $includeDeleted);
        $row = $this->db->fetchOne($sql, $params);
        return (int)($row['total'] ?? 0);
    }

    public function findById(int $id, bool $includeDeleted = false): array|false
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id";
        if (!$includeDeleted) $sql .= " AND deleted_at IS NULL";
        return $this->db->fetchOne($sql, [':id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->db->execute("UPDATE `{$this->table}` SET deleted_at = NOW() WHERE `{$this->primaryKey}` = :id", [':id' => $id]);
    }

    public function restore(int $id): void
    {
        $this->db->execute("UPDATE `{$this->table}` SET deleted_at = NULL WHERE `{$this->primaryKey}` = :id", [':id' => $id]);
    }

    public function toggleStatus(int $id): void
    {
        $this->db->execute(
            "UPDATE `{$this->table}` SET status = IF(status='active','inactive','active') WHERE `{$this->primaryKey}` = :id AND deleted_at IS NULL",
            [':id' => $id]
        );
    }

    protected function insert(array $data): int
    {
        // Only inject created_by if the table has that column
        if ($this->useCreatedBy && !array_key_exists('created_by', $data)) {
            $data['created_by'] = $_SESSION['user_id'] ?? null;
        }
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);
        $sql = "INSERT INTO `{$this->table}` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $params = [];
        foreach ($data as $k => $v) $params[":$k"] = $v;
        $this->db->execute($sql, $params);
        return (int)$this->db->lastInsertId();
    }

    protected function updateById(int $id, array $data): void
    {
        $sets = [];
        $params = [':id' => $id];
        foreach ($data as $k => $v) {
            $sets[] = "`$k` = :$k";
            $params[":$k"] = $v;
        }
        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $sets) . " WHERE `{$this->primaryKey}` = :id";
        $this->db->execute($sql, $params);
    }

    // ---------------------------------------------------------------
    // Standard public CRUD API (inherited by all child models)
    // ---------------------------------------------------------------

    /**
     * Insert a new row and return its auto-increment ID.
     * Child models may override to add validation / activity logging.
     */
    public function create(array $data): int
    {
        return $this->insert($data);
    }

    /**
     * Update an existing row by primary key.
     * Returns true (always, for compatibility with overriding models).
     * Child models may override to add validation / activity logging.
     */
    public function update(int $id, array $data): bool
    {
        $this->updateById($id, $data);
        return true;
    }

    /**
     * Soft-delete: sets deleted_at = NOW().
     */
    public function softDelete(int $id): bool
    {
        $this->delete($id);
        return true;
    }

    /**
     * Restore a soft-deleted row: sets deleted_at = NULL.
     */
    public function softRestore(int $id): bool
    {
        $this->restore($id);
        return true;
    }

    /**
     * Toggle the is_active / status column and log it.
     * Child models should override when using is_active (bool) rather than status (enum).
     */
    public function toggleStatusLog(int $id): bool
    {
        $this->toggleStatus($id);
        return true;
    }

    private function buildQuery(string $select, array $filters, bool $includeDeleted): array
    {
        $sql = "SELECT $select FROM `{$this->table}` WHERE 1=1";
        $params = [];

        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        } elseif (!empty($filters['only_deleted'])) {
            $sql .= " AND deleted_at IS NOT NULL";
        }

        if (!empty($filters['search']) && !empty($this->searchColumns)) {
            $parts = array_map(fn($c) => "`$c` LIKE :search", $this->searchColumns);
            $sql .= " AND (" . implode(' OR ', $parts) . ")";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        return [$sql, $params];
    }
}
