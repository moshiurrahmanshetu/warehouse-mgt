<?php
/**
 * Brand Model - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class BrandModel extends BaseModel
{
    protected string $table = 'brands';
    protected string $primaryKey = 'id';
    protected array $searchColumns = ['brand_code', 'brand_name', 'description', 'website'];

    public function create(array $data): int
    {
        $id = $this->insert($data);
        logActivity('create_brand', 'brand', "Created Brand: " . ($data['brand_code'] ?? ''), $id);
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $this->updateById($id, $data);
        logActivity('update_brand', 'brand', "Updated Brand ID $id", $id);
    }

    public function softDelete(int $id): bool
    {
        $this->delete($id);
        logActivity('delete_brand', 'brand', "Deleted Brand ID $id", $id);
    }

    public function softRestore(int $id): bool
    {
        $this->restore($id);
        logActivity('restore_brand', 'brand', "Restored Brand ID $id", $id);
    }

    public function toggleStatusLog(int $id): bool
    {
        $this->toggleStatus($id);
        logActivity('toggle_status_brand', 'brand', "Toggled Status for Brand ID $id", $id);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM brands WHERE brand_name = :name AND deleted_at IS NULL";
        $params = [':name' => $name];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }
}
