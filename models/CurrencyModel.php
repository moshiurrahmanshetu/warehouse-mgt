<?php
/**
 * Currency Model - Phase 05
 */
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class CurrencyModel extends BaseModel
{
    protected string $table = 'currencies';
    protected string $primaryKey = 'id';
    protected array $searchColumns = ['currency_code', 'currency_name', 'currency_symbol'];

    public function create(array $data): int
    {
        if (!empty($data['is_default']) && $data['is_default']) {
            $this->clearDefault();
        }
        $id = $this->insert($data);
        logActivity('create_currency', 'currency', "Created Currency: " . ($data['currency_code'] ?? ''), $id);
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['is_default']) && $data['is_default']) {
            $this->clearDefault($id);
        }
        $this->updateById($id, $data);
        logActivity('update_currency', 'currency', "Updated Currency ID $id", $id);
    }

    public function softDelete(int $id): bool
    {
        $this->delete($id);
        logActivity('delete_currency', 'currency', "Deleted Currency ID $id", $id);
    }

    public function softRestore(int $id): bool
    {
        $this->restore($id);
        logActivity('restore_currency', 'currency', "Restored Currency ID $id", $id);
    }

    public function toggleStatusLog(int $id): bool
    {
        $this->toggleStatus($id);
        logActivity('toggle_status_currency', 'currency', "Toggled Status for Currency ID $id", $id);
    }

    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM currencies WHERE currency_code = :code AND deleted_at IS NULL";
        $params = [':code' => $code];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }

    private function clearDefault(?int $excludeId = null): bool
    {
        $sql = "UPDATE currencies SET is_default = 0 WHERE 1=1";
        $params = [];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        $this->db->execute($sql, $params);
    }
}
