<?php
/**
 * Authentication Middleware
 * Ensures the user is authenticated before allowing access.
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));

class AuthMiddleware
{
    /**
     * Run the authentication check.
     * Redirects to login if user is not authenticated or session has timed out.
     */
    public static function handle(): void
    {
        // Session must be started first
        if (session_status() !== PHP_SESSION_ACTIVE) {
            startSecureSession();
        }

        // Check for session timeout
        if (checkSessionTimeout()) {
            flashMessage('warning', 'Your session has expired. Please log in again.');
            redirect(APP_URL . '/login.php');
        }

        // Check authentication
        if (!isLoggedIn()) {
            flashMessage('error', 'You must be logged in to access this page.');
            redirect(APP_URL . '/login.php');
        }

        // Validate session integrity — user_id must be a positive integer
        $userId = $_SESSION['user_id'] ?? 0;
        if (!is_int($userId) || $userId <= 0) {
            session_unset();
            session_destroy();
            redirect(APP_URL . '/login.php');
        }
    }
}
