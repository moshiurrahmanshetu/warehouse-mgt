<?php
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
class WarehouseModel {
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }
    public function getAll(): array {
        return $this->db->fetchAll("SELECT * FROM warehouses WHERE deleted_at IS NULL ORDER BY id DESC");
    }
    public function findById(int $id): array|false {
        return $this->db->fetchOne("SELECT * FROM warehouses WHERE id = :id AND deleted_at IS NULL", [':id' => $id]);
    }
    public function codeExists(string $code, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM warehouses WHERE warehouse_code = :code AND deleted_at IS NULL";
        $params = [':code' => $code];
        if ($excludeId) { $sql .= " AND id != :id"; $params[':id'] = $excludeId; }
        return (bool)$this->db->fetchOne($sql, $params);
    }
    public function create(array $data): int {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);
        $sql = "INSERT INTO warehouses (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $params = [];
        foreach ($data as $k => $v) { $params[':'.$k] = $v; }
        $this->db->execute($sql, $params);
        $id = (int)$this->db->lastInsertId();
        logActivity('create_warehouses', 'warehouse', "Created Warehouse ID $id");
        return $id;
    }
    public function update(int $id, array $data): void {
        $sets = [];
        $params = [':id' => $id];
        foreach ($data as $k => $v) { $sets[] = "$k = :$k"; $params[':'.$k] = $v; }
        $sql = "UPDATE warehouses SET " . implode(', ', $sets) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        logActivity('update_warehouses', 'warehouse', "Updated Warehouse ID $id");
    }
    public function delete(int $id): void {
        $this->db->execute("UPDATE warehouses SET deleted_at = NOW() WHERE id = :id", [':id' => $id]);
        logActivity('delete_warehouses', 'warehouse', "Deleted Warehouse ID $id");
    }

}