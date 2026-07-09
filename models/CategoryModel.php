<?php
/**
 * Category Model - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class CategoryModel extends BaseModel
{
    protected string $table = 'categories';
    protected array $searchColumns = ['category_code', 'category_name', 'description'];

    public function create(array $data): int
    {
        $id = $this->insert($data);
        logActivity('create_category', 'category', "Created Category: " . ($data['category_code'] ?? ''), $id);
        return $id;
    }

    public function update(int $id, array $data): void
    {
        $this->updateById($id, $data);
        logActivity('update_category', 'category', "Updated Category ID $id", $id);
    }

    public function softDelete(int $id): void
    {
        $this->delete($id);
        logActivity('delete_category', 'category', "Deleted Category ID $id", $id);
    }

    public function softRestore(int $id): void
    {
        $this->restore($id);
        logActivity('restore_category', 'category', "Restored Category ID $id", $id);
    }

    public function toggleStatusLog(int $id): void
    {
        $this->toggleStatus($id);
        logActivity('toggle_status_category', 'category', "Toggled Status for Category ID $id", $id);
    }

    /** Get all active categories for parent dropdown */
    public function getActiveForDropdown(): array
    {
        return $this->db->fetchAll("SELECT id, category_name, parent_id FROM categories WHERE deleted_at IS NULL AND status = 'active' ORDER BY category_name");
    }

    /** Check if name exists (excluding id on edit) */
    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM categories WHERE category_name = :name AND deleted_at IS NULL";
        $params = [':name' => $name];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }

    /** Check if category has children */
    public function hasChildren(int $id): bool
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS cnt FROM categories WHERE parent_id = :id AND deleted_at IS NULL", [':id' => $id]);
        return (int)($row['cnt'] ?? 0) > 0;
    }
}
