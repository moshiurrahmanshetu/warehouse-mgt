<?php
/**
 * Dashboard Entry Point
 * Warehouse Management System
 */

require_once __DIR__ . '/includes/bootstrap.php';

// Enforce authentication
AuthMiddleware::handle();

require_once CONTROLLER_PATH . '/DashboardController.php';

$controller = new DashboardController();
$controller->index();
