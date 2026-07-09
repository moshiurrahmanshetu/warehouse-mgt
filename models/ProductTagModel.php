<?php
/**
 * ProductTag Model - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class ProductTagModel extends BaseModel
{
    protected string $table = 'product_tags';
    protected array $searchColumns = ['tag_code', 'tag_name'];

    public function create(array $data): int
    {
        $id = $this->insert($data);
        logActivity('create_product_tag', 'tag', "Created Tag: " . ($data['tag_code'] ?? ''), $id);
        return $id;
    }

    public function update(int $id, array $data): void
    {
        $this->updateById($id, $data);
        logActivity('update_product_tag', 'tag', "Updated Tag ID $id", $id);
    }

    public function softDelete(int $id): void
    {
        $this->delete($id);
        logActivity('delete_product_tag', 'tag', "Deleted Tag ID $id", $id);
    }

    public function softRestore(int $id): void
    {
        $this->restore($id);
        logActivity('restore_product_tag', 'tag', "Restored Tag ID $id", $id);
    }

    public function toggleStatusLog(int $id): void
    {
        $this->toggleStatus($id);
        logActivity('toggle_status_product_tag', 'tag', "Toggled Status for Tag ID $id", $id);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM product_tags WHERE tag_name = :name AND deleted_at IS NULL";
        $params = [':name' => $name];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }
}
