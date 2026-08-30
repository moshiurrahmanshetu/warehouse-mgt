<?php
/**
 * Dashboard Controller
 * Warehouse Management System
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));

require_once MODEL_PATH . '/UserModel.php';
require_once MODEL_PATH . '/ActivityLogModel.php';

class DashboardController
{
    private Database $db;
    private UserModel $userModel;
    private ActivityLogModel $activityLogModel;

    public function __construct()
    {
        $this->db               = Database::getInstance();
        $this->userModel        = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    /**
     * Render the main dashboard view.
     */
    public function index(): void
    {
        $data = $this->getStats();
        $pageTitle = 'Dashboard';
        require_once VIEW_PATH . '/dashboard/index.php';
    }

    /**
     * Collect comprehensive dashboard statistics and summaries.
     */
    private function getStats(): array
    {
        return [
            // Core Identity
            'user_name'          => currentUser()['name'] ?? 'Administrator',
            'total_users'        => $this->countTable('users', 'deleted_at IS NULL'),
            'active_users'       => $this->countTable('users', "(status = 'active' OR is_active = 1) AND deleted_at IS NULL"),
            'total_roles'        => $this->countTable('roles', 'deleted_at IS NULL'),
            'total_permissions'  => $this->countTable('permissions'),

            // Warehouse Infrastructure
            'total_warehouses'   => $this->countTable('warehouses', 'deleted_at IS NULL'),
            'active_warehouses'  => $this->countTable('warehouses', "status = 'active' AND deleted_at IS NULL"),
            'total_zones'        => $this->countTable('warehouse_zones', 'deleted_at IS NULL'),
            'total_racks'        => $this->countTable('warehouse_racks', 'deleted_at IS NULL'),
            'total_shelves'      => $this->countTable('warehouse_shelves', 'deleted_at IS NULL'),
            'total_bins'         => $this->countTable('warehouse_bins', 'deleted_at IS NULL'),

            // Master Data
            'total_suppliers'    => $this->countTable('suppliers', 'deleted_at IS NULL'),
            'active_suppliers'   => $this->countTable('suppliers', "status = 'active' AND deleted_at IS NULL"),
            'total_customers'    => $this->countTable('customers', 'deleted_at IS NULL'),
            'active_customers'   => $this->countTable('customers', "status = 'active' AND deleted_at IS NULL"),
            'total_categories'   => $this->countTable('categories', 'deleted_at IS NULL'),
            'total_brands'       => $this->countTable('brands', 'deleted_at IS NULL'),
            'total_units'        => $this->countTable('units', 'deleted_at IS NULL'),
            'total_currencies'   => $this->countTable('currencies', 'deleted_at IS NULL'),

            // Activity & Recent Records
            'recent_logs'        => $this->activityLogModel->getAllWithUser([], 10, 0),
            'recent_suppliers'   => $this->getRecentSuppliers(5),
            'recent_customers'   => $this->getRecentCustomers(5),
            'recent_warehouses'  => $this->getRecentWarehouses(5),
        ];
    }

    /**
     * Count rows in a table safely.
     */
    private function countTable(string $table, ?string $where = null): int
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM `{$table}`";
            if ($where !== null && $where !== '') {
                $sql .= " WHERE {$where}";
            }
            $row = $this->db->fetchOne($sql);
            return (int)($row['total'] ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get recently added suppliers.
     */
    private function getRecentSuppliers(int $limit = 5): array
    {
        try {
            return $this->db->fetchAll(
                "SELECT id, supplier_code, name, contact_person, email, phone, status, created_at
                 FROM suppliers
                 WHERE deleted_at IS NULL
                 ORDER BY id DESC
                 LIMIT " . (int)$limit
            );
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get recently added customers.
     */
    private function getRecentCustomers(int $limit = 5): array
    {
        try {
            return $this->db->fetchAll(
                "SELECT id, customer_code, name, contact_person, email, phone, status, created_at
                 FROM customers
                 WHERE deleted_at IS NULL
                 ORDER BY id DESC
                 LIMIT " . (int)$limit
            );
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get recently added warehouses.
     */
    private function getRecentWarehouses(int $limit = 5): array
    {
        try {
            return $this->db->fetchAll(
                "SELECT id, warehouse_code, warehouse_name, contact_person, phone, status, created_at
                 FROM warehouses
                 WHERE deleted_at IS NULL
                 ORDER BY id DESC
                 LIMIT " . (int)$limit
            );
        } catch (Exception $e) {
            return [];
        }
    }
}
