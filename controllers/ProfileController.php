<?php
/**
 * Profile Controller
 * Handles user self-service profile management and password change.
 * Warehouse Management System - Phase 05.2
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/UserModel.php';

class ProfileController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display the My Profile page.
     */
    public function index(): void
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            redirect(APP_URL . '/login.php');
        }

        // Update last activity timestamp
        $this->userModel->touchActivity($userId);

        $user = $this->userModel->findById($userId);
        if (!$user) {
            flashMessage('error', 'User account not found.');
            redirect(APP_URL . '/logout.php');
        }

        $roles = $this->userModel->getRoles($userId);
        $user['roles'] = $roles;

        // Pass data to view
        $pageTitle = 'My Profile';
        require_once VIEW_PATH . '/profile/index.php';
    }

    /**
     * Process Profile details update.
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/profile.php');
        }

        verifyCsrf();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            redirect(APP_URL . '/login.php');
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            flashMessage('error', 'User not found.');
            redirect(APP_URL . '/login.php');
        }

        $name  = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        // Validation
        if (empty($name)) {
            flashMessage('error', 'Full Name is required.');
            redirect(APP_URL . '/profile.php');
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashMessage('error', 'A valid Email address is required.');
            redirect(APP_URL . '/profile.php');
        }

        if ($this->userModel->emailExists($email, $userId)) {
            flashMessage('error', 'The email address is already in use by another account.');
            redirect(APP_URL . '/profile.php');
        }

        // Handle Avatar upload
        $avatarPath = $this->handleAvatarUpload();

        $data = [
            'name'  => $name,
            'email' => $email,
            'phone' => $phone,
        ];

        if ($avatarPath !== null) {
            $data['avatar'] = $avatarPath;
        }

        try {
            $db = Database::getInstance();
            $db->beginTransaction();

            $this->userModel->updateProfile($userId, $data);

            // Update session cache
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;
            if ($avatarPath !== null) {
                $_SESSION['user']['avatar'] = $avatarPath;
            }

            // Log activity
            logActivity('Profile Updated', 'profile', "Updated profile for user: {$email}");
            if ($avatarPath !== null) {
                logActivity('Avatar Updated', 'profile', "Updated avatar for user: {$email}");
            }

            $db->commit();
            flashMessage('success', 'Profile updated successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to update profile: ' . $e->getMessage());
        }

        redirect(APP_URL . '/profile.php');
    }

    /**
     * Process Change Password submission.
     */
    public function changePassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/profile.php');
        }

        verifyCsrf();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            redirect(APP_URL . '/login.php');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            flashMessage('error', 'All password fields are required.');
            redirect(APP_URL . '/profile.php#password');
        }

        if (strlen($newPassword) < 6) {
            flashMessage('error', 'New password must be at least 6 characters long.');
            redirect(APP_URL . '/profile.php#password');
        }

        if ($newPassword !== $confirmPassword) {
            flashMessage('error', 'New password and confirmation do not match.');
            redirect(APP_URL . '/profile.php#password');
        }

        $user = $this->userModel->findWithPassword($userId);
        if (!$user) {
            flashMessage('error', 'User not found.');
            redirect(APP_URL . '/login.php');
        }

        if (!password_verify($currentPassword, $user['password'])) {
            flashMessage('error', 'Current password is incorrect.');
            redirect(APP_URL . '/profile.php#password');
        }

        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            $this->userModel->updatePassword($userId, $hashedPassword);

            logActivity('Password Changed', 'profile', "Changed password for user: {$user['email']}");
            flashMessage('success', 'Password changed successfully.');
        } catch (Exception $e) {
            flashMessage('error', 'Failed to change password.');
        }

        redirect(APP_URL . '/profile.php#password');
    }

    /**
     * Handle Avatar upload with size and MIME validation.
     */
    private function handleAvatarUpload(): ?string
    {
        if (empty($_FILES['avatar']['name'])) {
            return null;
        }

        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            flashMessage('error', 'Avatar upload encountered an error.');
            return null;
        }

        // Validate size (2MB max)
        if ($file['size'] > 2 * 1024 * 1024) {
            flashMessage('error', 'Avatar image size must be 2MB or less.');
            return null;
        }

        // Validate MIME type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes, true)) {
            flashMessage('error', 'Invalid avatar format. Only JPG, PNG, and WebP images are allowed.');
            return null;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $filename = uniqid('avatar_', true) . '.' . $ext;
        $uploadDir = BASEPATH . '/uploads/avatars/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return 'uploads/avatars/' . $filename;
        }

        flashMessage('error', 'Failed to save avatar image file.');
        return null;
    }
}
