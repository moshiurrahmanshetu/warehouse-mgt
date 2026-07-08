<?php
/**
 * Customer Management Entry Point
 * Warehouse Management System - Phase 04
 */

require_once __DIR__ . '/includes/bootstrap.php';
AuthMiddleware::handle();
requirePermission('customers.view');

require_once CONTROLLER_PATH . '/CustomerController.php';

$controller = new CustomerController();
$action     = sanitize($_GET['action'] ?? 'index');

$allowed = ['index', 'create', 'store', 'edit', 'update', 'details', 'delete', 'restore', 'export', 'printList', 'toggleStatus'];

if (in_array($action, $allowed, true) && method_exists($controller, $action)) {
    $controller->$action();
} else {
    http_response_code(404);
    require_once BASEPATH . '/404.php';
}
