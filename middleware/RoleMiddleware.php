<?php
/**
 * Role Middleware
 * Ensures the authenticated user has the required role(s) or permission(s).
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));

class RoleMiddleware
{
    /**
     * Require the user to have at least one of the specified role slugs.
     *
     * @param string|string[] $roles  Role slug(s) to check against.
     */
    public static function requireRole(string|array $roles): void
    {
        $roles = (array) $roles;

        foreach ($roles as $role) {
            if (hasRole($role)) {
                return; // Allowed
            }
        }

        http_response_code(403);
        include BASEPATH . '/views/errors/403.php';
        exit;
    }

    /**
     * Require the user to have a specific permission slug.
     *
     * @param string $permission  Permission slug to check.
     */
    public static function requirePermission(string $permission): void
    {
        if (!hasPermission($permission)) {
            http_response_code(403);
            include BASEPATH . '/views/errors/403.php';
            exit;
        }
    }

    /**
     * Return true if the current user has the given role slug.
     */
    public static function can(string $roleSlug): bool
    {
        return hasRole($roleSlug);
    }
}
