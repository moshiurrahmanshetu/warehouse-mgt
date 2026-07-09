<?php
/**
 * Currency Management Entry Point
 * Warehouse Management System
 */

require_once __DIR__ . '/includes/bootstrap.php';
AuthMiddleware::handle();
requirePermission('currencies.view');

require_once CONTROLLER_PATH . '/CurrencyController.php';

$controller = new CurrencyController();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    http_response_code(404);
    require_once BASEPATH . '/404.php';
}
