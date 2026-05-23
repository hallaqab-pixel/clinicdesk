<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {

    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLogin(): void {
        if (Auth::check()) {
            redirect(BASE_URL . '/index.php?page=dashboard');
        }
        $pageTitle = 'Login';
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function handleLogin(): void {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request. Please try again.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $user     = $this->userModel->findByEmail($email);

        if (!$user) {
            flashMessage('error', 'Invalid credentials.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        if ((int)$user['is_active'] === 0) {
            flashMessage('error', 'Account suspended. Contact admin.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        if (!password_verify($password, $user['password'])) {
            flashMessage('error', 'Invalid credentials.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        Auth::login($user);
        redirect(BASE_URL . '/index.php?page=dashboard');
    }

    public function handleLogout(): void {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            redirect(BASE_URL . '/index.php?page=dashboard');
        }
        Auth::logout();
    }
}