<?php
/**
 * Activity Log Controller
 * Warehouse Management System
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/ActivityLogModel.php';

class ActivityLogController
{
    private ActivityLogModel $model;

    public function __construct()
    {
        $this->model = new ActivityLogModel();
    }

    /**
     * Display list of activity logs with filters and pagination.
     */
    public function index(): void
    {
        $this->authorize();

        $filters = [
            'search'    => sanitize($_GET['search'] ?? ''),
            'user_id'   => !empty($_GET['user_id']) ? (int)$_GET['user_id'] : '',
            'module'    => sanitize($_GET['module'] ?? ''),
            'action'    => sanitize($_GET['action'] ?? ''),
            'date_from' => sanitize($_GET['date_from'] ?? ''),
            'date_to'   => sanitize($_GET['date_to'] ?? ''),
        ];

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = (int)getSetting('items_per_page', 25);
        if ($limit <= 0) $limit = 25;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->model->countFiltered($filters);
        $logs         = $this->model->getAllWithUser($filters, $limit, $offset);
        $modules      = $this->model->getDistinctModules();
        $actions      = $this->model->getDistinctActions();
        $usersList    = $this->model->getUsersList();

        $pageTitle = 'Activity Logs';
        require_once VIEW_PATH . '/activity_logs/index.php';
    }

    /**
     * Display full details of a specific activity log record.
     */
    public function details(): void
    {
        $this->authorize();

        $id  = (int)($_GET['id'] ?? 0);
        $log = $this->model->findByIdWithUser($id);

        if (!$log) {
            flashMessage('error', 'Activity log entry not found.');
            redirect(APP_URL . '/activity_logs.php');
        }

        $pageTitle = 'Activity Log Details #' . $id;
        require_once VIEW_PATH . '/activity_logs/details.php';
    }

    /**
     * Enforce authorization for activity logs.
     */
    private function authorize(): void
    {
        if (!hasPermission('logs.view') && !hasPermission('activity_logs.view')) {
            http_response_code(403);
            include BASEPATH . '/views/errors/403.php';
            exit;
        }
    }
}
