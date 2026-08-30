<?php
/**
 * System Setting Model
 * Warehouse Management System - Phase 05.2
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/BaseModel.php';

class SettingModel extends BaseModel
{
    protected string $table = 'system_settings';
    protected string $primaryKey = 'id';
    protected bool $useCreatedBy = false;

    /**
     * Local cache of settings key-value pairs
     */
    private static ?array $settingsCache = null;

    /**
     * Get a setting value by its key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->getAllKeyed();
        return $all[$key] ?? $default;
    }

    /**
     * Get all settings as an associative array of key => value.
     */
    public function getAllKeyed(): array
    {
        if (self::$settingsCache !== null) {
            return self::$settingsCache;
        }

        $rows = $this->db->fetchAll("SELECT `key`, `value` FROM `{$this->table}`");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }

        self::$settingsCache = $settings;
        return $settings;
    }

    /**
     * Clear the in-memory cache.
     */
    public function clearCache(): void
    {
        self::$settingsCache = null;
    }

    /**
     * Get all settings grouped by their group identifier.
     */
    public function getAllGrouped(): array
    {
        $rows = $this->db->fetchAll("SELECT * FROM `{$this->table}` ORDER BY `group`, `id` ASC");
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['group']][] = $row;
        }
        return $grouped;
    }

    /**
     * Update or insert a single setting.
     */
    public function set(string $key, mixed $value, ?string $group = null, ?string $type = null, ?string $description = null, ?int $userId = null): bool
    {
        $existing = $this->db->fetchOne("SELECT `id` FROM `{$this->table}` WHERE `key` = :key", [':key' => $key]);

        if ($existing) {
            $sql = "UPDATE `{$this->table}` SET `value` = :value, `updated_at` = NOW()";
            $params = [':value' => (string)$value, ':key' => $key];

            if ($userId !== null) {
                $sql .= ", `updated_by` = :updated_by";
                $params[':updated_by'] = $userId;
            }
            if ($group !== null) {
                $sql .= ", `group` = :group";
                $params[':group'] = $group;
            }
            if ($type !== null) {
                $sql .= ", `type` = :type";
                $params[':type'] = $type;
            }
            if ($description !== null) {
                $sql .= ", `description` = :description";
                $params[':description'] = $description;
            }

            $sql .= " WHERE `key` = :key";
            $this->db->execute($sql, $params);
        } else {
            $sql = "INSERT INTO `{$this->table}` (`key`, `value`, `type`, `group`, `description`, `updated_by`, `created_at`, `updated_at`)
                    VALUES (:key, :value, :type, :group, :description, :updated_by, NOW(), NOW())";
            $this->db->execute($sql, [
                ':key'         => $key,
                ':value'       => (string)$value,
                ':type'        => $type ?? 'string',
                ':group'       => $group ?? 'general',
                ':description' => $description ?? '',
                ':updated_by'  => $userId,
            ]);
        }

        $this->clearCache();
        return true;
    }

    /**
     * Batch update multiple setting key-value pairs.
     */
    public function setMany(array $settings, ?int $userId = null): bool
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value, null, null, null, $userId);
        }
        $this->clearCache();
        return true;
    }

    /**
     * Fetch active currencies for settings dropdown.
     */
    public function getCurrencies(): array
    {
        return $this->db->fetchAll(
            "SELECT id, currency_code, currency_name, currency_symbol, is_default
             FROM currencies
             WHERE deleted_at IS NULL
             ORDER BY currency_name ASC"
        );
    }

    /**
     * Fetch active warehouses for settings dropdown.
     */
    public function getWarehouses(): array
    {
        return $this->db->fetchAll(
            "SELECT id, warehouse_code, warehouse_name
             FROM warehouses
             WHERE deleted_at IS NULL
             ORDER BY warehouse_name ASC"
        );
    }

    /**
     * Sync default currency in currencies table.
     */
    public function syncDefaultCurrency(int $currencyId): void
    {
        if ($currencyId <= 0) return;
        $this->db->execute("UPDATE currencies SET is_default = 0 WHERE is_default = 1");
        $this->db->execute("UPDATE currencies SET is_default = 1 WHERE id = :id", [':id' => $currencyId]);
    }
}
