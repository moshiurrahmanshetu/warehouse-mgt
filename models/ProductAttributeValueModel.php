<?php
/**
 * ProductAttributeValue Model - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class ProductAttributeValueModel extends BaseModel
{
    protected string $table = 'product_attribute_values';
    protected string $primaryKey = 'id';
    protected array $searchColumns = ['value'];

    public function getAllByAttribute(int $attributeId, array $filters = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array
    {
        $sql = "SELECT av.*, pa.attribute_name FROM product_attribute_values av JOIN product_attributes pa ON av.attribute_id = pa.id WHERE av.attribute_id = :attr_id";
        $params = [':attr_id' => $attributeId];
        if (!$includeDeleted) $sql .= " AND av.deleted_at IS NULL";
        elseif (!empty($filters['only_deleted'])) $sql .= " AND av.deleted_at IS NOT NULL";
        if (!empty($filters['search'])) {
            $sql .= " AND av.value LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) { $sql .= " AND av.status = :status"; $params[':status'] = $filters['status']; }
        $sql .= " ORDER BY av.sort_order ASC, av.id DESC";
        if ($limit > 0) $sql .= " LIMIT $limit OFFSET $offset";
        return $this->db->fetchAll($sql, $params);
    }

    public function getAll(array $filters = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array
    {
        $sql = "SELECT av.*, pa.attribute_name FROM product_attribute_values av JOIN product_attributes pa ON av.attribute_id = pa.id WHERE 1=1";
        $params = [];
        if (!$includeDeleted) $sql .= " AND av.deleted_at IS NULL";
        elseif (!empty($filters['only_deleted'])) $sql .= " AND av.deleted_at IS NOT NULL";
        if (!empty($filters['search'])) {
            $sql .= " AND (av.value LIKE :search OR pa.attribute_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) { $sql .= " AND av.status = :status"; $params[':status'] = $filters['status']; }
        if (!empty($filters['attribute_id'])) { $sql .= " AND av.attribute_id = :attr_id"; $params[':attr_id'] = $filters['attribute_id']; }
        $sql .= " ORDER BY pa.attribute_name, av.sort_order ASC";
        if ($limit > 0) $sql .= " LIMIT $limit OFFSET $offset";
        return $this->db->fetchAll($sql, $params);
    }

    public function countAll(array $filters = [], bool $includeDeleted = false): int
    {
        $sql = "SELECT COUNT(*) AS total FROM product_attribute_values av JOIN product_attributes pa ON av.attribute_id = pa.id WHERE 1=1";
        $params = [];
        if (!$includeDeleted) $sql .= " AND av.deleted_at IS NULL";
        elseif (!empty($filters['only_deleted'])) $sql .= " AND av.deleted_at IS NOT NULL";
        if (!empty($filters['search'])) {
            $sql .= " AND (av.value LIKE :search OR pa.attribute_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) { $sql .= " AND av.status = :status"; $params[':status'] = $filters['status']; }
        if (!empty($filters['attribute_id'])) { $sql .= " AND av.attribute_id = :attr_id"; $params[':attr_id'] = $filters['attribute_id']; }
        $row = $this->db->fetchOne($sql, $params);
        return (int)($row['total'] ?? 0);
    }

    public function create(array $data): int
    {
        $id = $this->insert($data);
        logActivity('create_attribute_value', 'attribute', "Created Attribute Value ID $id", $id);
        return $id;
    }

    public function update(int $id, array $data): void
    {
        $this->updateById($id, $data);
        logActivity('update_attribute_value', 'attribute', "Updated Attribute Value ID $id", $id);
    }

    public function softDelete(int $id): void
    {
        $this->delete($id);
        logActivity('delete_attribute_value', 'attribute', "Deleted Attribute Value ID $id", $id);
    }

    public function softRestore(int $id): void
    {
        $this->restore($id);
        logActivity('restore_attribute_value', 'attribute', "Restored Attribute Value ID $id", $id);
    }

    public function toggleStatusLog(int $id): void
    {
        $this->toggleStatus($id);
        logActivity('toggle_status_attribute_value', 'attribute', "Toggled Status for Attribute Value ID $id", $id);
    }

    public function valueExistsForAttribute(int $attributeId, string $value, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM product_attribute_values WHERE attribute_id = :attr_id AND value = :value AND deleted_at IS NULL";
        $params = [':attr_id' => $attributeId, ':value' => $value];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }
}
