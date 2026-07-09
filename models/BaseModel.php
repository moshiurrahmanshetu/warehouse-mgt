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
        $data['created_by'] = $_SESSION['user_id'] ?? null;
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
