<?php
/**
 * Application Entry Point
 * Warehouse Management System
 *
 * Bootstraps the app and routes the visitor:
 *  - Authenticated  → Dashboard
 *  - Guest          → Login
 */

require_once __DIR__ . '/includes/bootstrap.php';

if (isLoggedIn()) {
    redirect(APP_URL . '/dashboard.php');
} else {
    redirect(APP_URL . '/login.php');
}
