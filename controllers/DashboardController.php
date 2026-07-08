<?php
/**
 * Dashboard Controller
 * Warehouse Management System
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));

require_once MODEL_PATH . '/UserModel.php';

class DashboardController
{
    private Database $db;
    private UserModel $userModel;

    public function __construct()
    {
        $this->db        = Database::getInstance();
        $this->userModel = new UserModel();
    }

    /**
     * Render the main dashboard view.
     */
    public function index(): void
    {
        $data = $this->getStats();
        require_once VIEW_PATH . '/dashboard/index.php';
    }

    /**
     * Collect dashboard statistics.
     */
    private function getStats(): array
    {
        return [
            'total_users'       => $this->userModel->countAll(),
            'active_users'      => $this->userModel->countActive(),
            'total_roles'       => $this->countTable('roles'),
            'total_permissions' => $this->countTable('permissions'),
            'recent_logs'       => $this->getRecentLogs(10),
        ];
    }

    /**
     * Count all rows in a table.
     */
    private function countTable(string $table): int
    {
        // Table name is hard-coded — no user input, no injection risk
        $row = $this->db->fetchOne("SELECT COUNT(*) AS total FROM `{$table}`");
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Get the most recent activity log entries.
     */
    private function getRecentLogs(int $limit = 10): array
    {
        $limit = max(1, (int) $limit);
        return $this->db->fetchAll(
            "SELECT al.*, u.name AS user_name
             FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT {$limit}"
        );
    }
}
