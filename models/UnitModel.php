<?php
/**
 * Unit Model - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class UnitModel extends BaseModel
{
    protected string $table = 'units';
    protected string $primaryKey = 'id';
    protected array $searchColumns = ['unit_code', 'unit_name', 'short_name', 'unit_type'];

    public function create(array $data): int
    {
        $id = $this->insert($data);
        logActivity('create_unit', 'unit', "Created Unit: " . ($data['unit_code'] ?? ''), $id);
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $this->updateById($id, $data);
        logActivity('update_unit', 'unit', "Updated Unit ID $id", $id);
    }

    public function softDelete(int $id): bool
    {
        $this->delete($id);
        logActivity('delete_unit', 'unit', "Deleted Unit ID $id", $id);
    }

    public function softRestore(int $id): bool
    {
        $this->restore($id);
        logActivity('restore_unit', 'unit', "Restored Unit ID $id", $id);
    }

    public function toggleStatusLog(int $id): bool
    {
        $this->toggleStatus($id);
        logActivity('toggle_status_unit', 'unit', "Toggled Status for Unit ID $id", $id);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM units WHERE unit_name = :name AND deleted_at IS NULL";
        $params = [':name' => $name];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }

    public function getActiveForDropdown(): array
    {
        return $this->db->fetchAll("SELECT id, unit_name, short_name FROM units WHERE deleted_at IS NULL AND status = 'active' ORDER BY unit_name");
    }
}
