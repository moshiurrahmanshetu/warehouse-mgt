<?php
/**
 * Auth Controller
 * Handles login and logout logic.
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));

require_once MODEL_PATH . '/UserModel.php';

class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display the login form.
     */
    public function showLogin(): void
    {
        if (isLoggedIn()) {
            redirect(APP_URL . '/dashboard.php');
        }
        require_once VIEW_PATH . '/auth/login.php';
    }

    /**
     * Process login form submission.
     */
    public function processLogin(): void
    {
        // CSRF check
        verifyCsrf();

        $email    = sanitize($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($email) || empty($password)) {
            flashMessage('error', 'Email and password are required.');
            redirect(APP_URL . '/login.php');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashMessage('error', 'Invalid email address format.');
            redirect(APP_URL . '/login.php');
        }

        // Rate limiting — simple session-based attempt counter
        $this->checkLoginAttempts();

        // Find user
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_attempt']   = time();
            flashMessage('error', 'Invalid email or password.');
            redirect(APP_URL . '/login.php');
        }

        // Check account status
        if ($user['status'] !== 'active') {
            flashMessage('error', 'Your account is inactive. Please contact the administrator.');
            redirect(APP_URL . '/login.php');
        }

        // Successful — regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Store user in session
        $_SESSION['user_id']     = (int) $user['id'];
        $_SESSION['user']        = [
            'id'     => (int) $user['id'],
            'name'   => $user['name'],
            'email'  => $user['email'],
            'avatar' => $user['avatar'],
            'status' => $user['status'],
        ];
        $_SESSION['roles']       = $this->userModel->getRoles((int) $user['id']);
        $_SESSION['permissions'] = $this->userModel->getPermissions((int) $user['id']);
        $_SESSION['_last_activity'] = time();
        $_SESSION['_initiated']     = true;

        // Clear login attempts
        unset($_SESSION['login_attempts'], $_SESSION['last_attempt']);

        // Update last login record
        $this->userModel->updateLastLogin((int) $user['id'], getClientIp());

        // Log activity
        logActivity('Login', 'auth', 'User logged in: ' . $user['email']);

        flashMessage('success', 'Welcome back, ' . e($user['name']) . '!');
        redirect(APP_URL . '/dashboard.php');
    }

    /**
     * Log out the current user.
     */
    public function logout(): void
    {
        if (isLoggedIn()) {
            logActivity('Logout', 'auth', 'User logged out: ' . ($_SESSION['user']['email'] ?? ''));
        }

        session_unset();
        session_destroy();

        // Clear the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        redirect(APP_URL . '/login.php');
    }

    /**
     * Block repeated failed login attempts (simple lockout).
     */
    private function checkLoginAttempts(): void
    {
        $maxAttempts = 5;
        $lockoutTime = 300; // 5 minutes

        $attempts = $_SESSION['login_attempts'] ?? 0;
        $lastTime = $_SESSION['last_attempt']   ?? 0;

        if ($attempts >= $maxAttempts) {
            $elapsed = time() - $lastTime;
            if ($elapsed < $lockoutTime) {
                $wait = $lockoutTime - $elapsed;
                flashMessage('error', "Too many failed attempts. Please wait {$wait} seconds.");
                redirect(APP_URL . '/login.php');
            } else {
                // Reset after lockout period
                unset($_SESSION['login_attempts'], $_SESSION['last_attempt']);
            }
        }
    }
}
