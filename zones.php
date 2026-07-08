<?php
require_once __DIR__ . '/includes/bootstrap.php';
AuthMiddleware::handle();
requirePermission('zones.view');
require_once CONTROLLER_PATH . '/ZoneController.php';
$controller = new ZoneController();
$action = $_GET['action'] ?? 'index';
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    http_response_code(404);
    require_once BASEPATH . '/404.php';
}