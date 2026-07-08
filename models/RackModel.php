<?php
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
class RackModel {
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }
    public function getAll(): array {
        return $this->db->fetchAll("SELECT * FROM warehouse_racks WHERE deleted_at IS NULL ORDER BY id DESC");
    }
    public function findById(int $id): array|false {
        return $this->db->fetchOne("SELECT * FROM warehouse_racks WHERE id = :id AND deleted_at IS NULL", [':id' => $id]);
    }
    public function codeExists(string $code, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM warehouse_racks WHERE rack_code = :code AND deleted_at IS NULL";
        $params = [':code' => $code];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }
    public function create(array $data): int {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);
        $sql = "INSERT INTO warehouse_racks (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $params = [];
        foreach ($data as $k => $v) { $params[':'.$k] = $v; }
        $this->db->execute($sql, $params);
        $id = (int)$this->db->lastInsertId();
        logActivity('create_warehouse_racks', 'warehouse', "Created Rack ID $id");
        return $id;
    }
    public function update(int $id, array $data): void {
        $sets = [];
        $params = [':id' => $id];
        foreach ($data as $k => $v) { $sets[] = "$k = :$k"; $params[':'.$k] = $v; }
        $sql = "UPDATE warehouse_racks SET " . implode(', ', $sets) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        logActivity('update_warehouse_racks', 'warehouse', "Updated Rack ID $id");
    }
    public function delete(int $id): void {
        $this->db->execute("UPDATE warehouse_racks SET deleted_at = NOW() WHERE id = :id", [':id' => $id]);
        logActivity('delete_warehouse_racks', 'warehouse', "Deleted Rack ID $id");
    }
    public function getAllParents(): array {
        return $this->db->fetchAll("SELECT id, zone_name AS name FROM warehouse_zones WHERE status = 'active' AND deleted_at IS NULL ORDER BY zone_name");
    }
    public function parentExists(int $id): bool {
        $row = $this->db->fetchOne("SELECT id FROM warehouse_zones WHERE id = :id AND deleted_at IS NULL", [':id' => $id]);
        return (bool)$row;
    }
}