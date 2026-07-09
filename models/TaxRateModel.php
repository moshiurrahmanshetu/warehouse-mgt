<?php
/**
 * TaxRate Model - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class TaxRateModel extends BaseModel
{
    protected string $table = 'tax_rates';
    protected array $searchColumns = ['tax_name'];

    public function create(array $data): int
    {
        $id = $this->insert($data);
        logActivity('create_tax_rate', 'tax_rate', "Created Tax Rate: " . ($data['tax_name'] ?? ''), $id);
        return $id;
    }

    public function update(int $id, array $data): void
    {
        $this->updateById($id, $data);
        logActivity('update_tax_rate', 'tax_rate', "Updated Tax Rate ID $id", $id);
    }

    public function softDelete(int $id): void
    {
        $this->delete($id);
        logActivity('delete_tax_rate', 'tax_rate', "Deleted Tax Rate ID $id", $id);
    }

    public function softRestore(int $id): void
    {
        $this->restore($id);
        logActivity('restore_tax_rate', 'tax_rate', "Restored Tax Rate ID $id", $id);
    }

    public function toggleStatusLog(int $id): void
    {
        $this->toggleStatus($id);
        logActivity('toggle_status_tax_rate', 'tax_rate', "Toggled Status for Tax Rate ID $id", $id);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM tax_rates WHERE tax_name = :name AND deleted_at IS NULL";
        $params = [':name' => $name];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }
}
