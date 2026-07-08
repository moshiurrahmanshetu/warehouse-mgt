<?php
/**
 * User Model
 * Warehouse Management System
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));

class UserModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a user by their email address.
     */
    public function findByEmail(string $email): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE email = :email LIMIT 1",
            [':email' => $email]
        );
    }

    /**
     * Find a user by their ID.
     */
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT id, name, email, phone, avatar, status, last_login_at, created_at
             FROM users WHERE id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    /**
     * Get all role slugs for a user.
     */
    public function getRoles(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT r.slug FROM roles r
             INNER JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = :uid AND r.is_active = 1",
            [':uid' => $userId]
        );
        return array_column($rows, 'slug');
    }

    /**
     * Get all permission slugs for a user (via their roles).
     */
    public function getPermissions(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT p.slug FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             INNER JOIN user_roles ur       ON ur.role_id = rp.role_id
             WHERE ur.user_id = :uid",
            [':uid' => $userId]
        );
        return array_column($rows, 'slug');
    }

    /**
     * Update the user's last login timestamp and IP.
     */
    public function updateLastLogin(int $userId, string $ip): void
    {
        $this->db->execute(
            "UPDATE users SET last_login_at = NOW(), last_login_ip = :ip WHERE id = :id",
            [':ip' => $ip, ':id' => $userId]
        );
    }

    /**
     * Get total number of active users.
     */
    public function countActive(): int
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS total FROM users WHERE status = 'active'");
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Get total number of users.
     */
    public function countAll(): int
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS total FROM users");
        return (int) ($row['total'] ?? 0);
    }
}
