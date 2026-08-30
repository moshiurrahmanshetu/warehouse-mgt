<?php
/**
 * Activity Log Model
 * Warehouse Management System
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class ActivityLogModel extends BaseModel
{
    protected string $table = 'activity_logs';
    protected string $primaryKey = 'id';
    protected bool $useCreatedBy = false;

    /**
     * Fetch activity logs with joined user information and flexible filters.
     */
    public function getAllWithUser(array $filters = [], int $limit = 25, int $offset = 0): array
    {
        [$sql, $params] = $this->buildFilterQuery(
            "SELECT al.*, u.name AS user_name, u.email AS user_email, u.avatar AS user_avatar
             FROM `{$this->table}` al
             LEFT JOIN users u ON u.id = al.user_id",
            $filters
        );

        $sql .= " ORDER BY al.id DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Count total activity logs matching filters.
     */
    public function countFiltered(array $filters = []): int
    {
        [$sql, $params] = $this->buildFilterQuery(
            "SELECT COUNT(*) AS total
             FROM `{$this->table}` al
             LEFT JOIN users u ON u.id = al.user_id",
            $filters
        );

        $row = $this->db->fetchOne($sql, $params);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Find single log by ID with user details.
     */
    public function findByIdWithUser(int $id): array|false
    {
        $sql = "SELECT al.*, u.name AS user_name, u.email AS user_email, u.avatar AS user_avatar, u.phone AS user_phone
                FROM `{$this->table}` al
                LEFT JOIN users u ON u.id = al.user_id
                WHERE al.id = :id LIMIT 1";

        return $this->db->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Fetch distinct module names for filter dropdown.
     */
    public function getDistinctModules(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT `module` FROM `{$this->table}` WHERE `module` IS NOT NULL AND `module` != '' ORDER BY `module` ASC"
        );
        return array_column($rows, 'module');
    }

    /**
     * Fetch distinct action names for filter dropdown.
     */
    public function getDistinctActions(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT `action` FROM `{$this->table}` WHERE `action` IS NOT NULL AND `action` != '' ORDER BY `action` ASC"
        );
        return array_column($rows, 'action');
    }

    /**
     * Fetch users list for filter dropdown.
     */
    public function getUsersList(): array
    {
        return $this->db->fetchAll("SELECT id, name, email FROM users ORDER BY name ASC");
    }

    /**
     * Build WHERE conditions based on filter parameters.
     */
    private function buildFilterQuery(string $baseSql, array $filters): array
    {
        $sql = $baseSql . " WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $searchVal = '%' . trim($filters['search']) . '%';
            $sql .= " AND (al.action LIKE :s1 OR al.module LIKE :s2 OR al.description LIKE :s3 OR al.ip_address LIKE :s4 OR u.name LIKE :s5 OR u.email LIKE :s6)";
            $params[':s1'] = $searchVal;
            $params[':s2'] = $searchVal;
            $params[':s3'] = $searchVal;
            $params[':s4'] = $searchVal;
            $params[':s5'] = $searchVal;
            $params[':s6'] = $searchVal;
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND al.user_id = :user_id";
            $params[':user_id'] = (int)$filters['user_id'];
        }

        if (!empty($filters['module'])) {
            $sql .= " AND al.module = :module";
            $params[':module'] = trim($filters['module']);
        }

        if (!empty($filters['action'])) {
            $sql .= " AND al.action = :action";
            $params[':action'] = trim($filters['action']);
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND al.created_at >= :date_from";
            $params[':date_from'] = trim($filters['date_from']) . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND al.created_at <= :date_to";
            $params[':date_to'] = trim($filters['date_to']) . ' 23:59:59';
        }

        return [$sql, $params];
    }
}
