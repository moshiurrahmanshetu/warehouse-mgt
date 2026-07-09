<?php
/**
 * ProductAttribute Model - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class ProductAttributeModel extends BaseModel
{
    protected string $table = 'product_attributes';
    protected string $primaryKey = 'id';
    protected array $searchColumns = ['attribute_code', 'attribute_name'];

    public function create(array $data): int
    {
        $id = $this->insert($data);
        logActivity('create_attribute', 'attribute', "Created Attribute: " . ($data['attribute_code'] ?? ''), $id);
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $this->updateById($id, $data);
        logActivity('update_attribute', 'attribute', "Updated Attribute ID $id", $id);
    }

    public function softDelete(int $id): bool
    {
        $this->delete($id);
        logActivity('delete_attribute', 'attribute', "Deleted Attribute ID $id", $id);
    }

    public function softRestore(int $id): bool
    {
        $this->restore($id);
        logActivity('restore_attribute', 'attribute', "Restored Attribute ID $id", $id);
    }

    public function toggleStatusLog(int $id): bool
    {
        $this->toggleStatus($id);
        logActivity('toggle_status_attribute', 'attribute', "Toggled Status for Attribute ID $id", $id);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM product_attributes WHERE attribute_name = :name AND deleted_at IS NULL";
        $params = [':name' => $name];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }

    public function getActiveForDropdown(): array
    {
        return $this->db->fetchAll("SELECT id, attribute_name FROM product_attributes WHERE deleted_at IS NULL AND status = 'active' ORDER BY attribute_name");
    }

    public function hasValues(int $id): bool
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS cnt FROM product_attribute_values WHERE attribute_id = :id AND deleted_at IS NULL", [':id' => $id]);
        return (int)($row['cnt'] ?? 0) > 0;
    }
}
