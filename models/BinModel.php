<?php
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once __DIR__ . '/BaseModel.php';
class BinModel extends BaseModel
{
    protected string $table = 'bins';
    protected string $primaryKey = 'id';
    
    public function getAll(array $filters = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array {
        return $this->db->fetchAll("SELECT * FROM warehouse_bins WHERE deleted_at IS NULL ORDER BY id DESC");
    }
    public function findById(int $id, bool $includeDeleted = false): array|false {
        return $this->db->fetchOne("SELECT * FROM warehouse_bins WHERE id = :id AND deleted_at IS NULL", [':id' => $id]);
    }
    public function codeExists(string $code, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM warehouse_bins WHERE bin_code = :code AND deleted_at IS NULL";
        $params = [':code' => $code];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }
    public function create(array $data): int {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);
        $sql = "INSERT INTO warehouse_bins (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $params = [];
        foreach ($data as $k => $v) { $params[':'.$k] = $v; }
        $this->db->execute($sql, $params);
        $id = (int)$this->db->lastInsertId();
        logActivity('create_warehouse_bins', 'warehouse', "Created Bin ID $id");
        return $id;
    }
    public function update(int $id, array $data): bool {
        $sets = [];
        $params = [':id' => $id];
        foreach ($data as $k => $v) { $sets[] = "$k = :$k"; $params[':'.$k] = $v; }
        $sql = "UPDATE warehouse_bins SET " . implode(', ', $sets) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        logActivity('update_warehouse_bins', 'warehouse', "Updated Bin ID $id");
    }
    public function delete(int $id): void {
        $this->db->execute("UPDATE warehouse_bins SET deleted_at = NOW() WHERE id = :id", [':id' => $id]);
        logActivity('delete_warehouse_bins', 'warehouse', "Deleted Bin ID $id");
    }
    public function softDelete(int $id): bool {
        $this->delete($id);
        return true;
    }
    public function softRestore(int $id): bool {
        $this->db->execute("UPDATE warehouse_bins SET deleted_at = NULL WHERE id = :id", [':id' => $id]);
        logActivity('restore_warehouse_bins', 'warehouse', "Restored Bin ID $id");
        return true;
    }
    public function getAllParents(): array {
        return $this->db->fetchAll("SELECT id, shelf_name AS name FROM warehouse_shelves WHERE status = 'active' AND deleted_at IS NULL ORDER BY shelf_name");
    }
    public function parentExists(int $id): bool {
        $row = $this->db->fetchOne("SELECT id FROM warehouse_shelves WHERE id = :id AND deleted_at IS NULL", [':id' => $id]);
        return (bool)$row;
    }
}