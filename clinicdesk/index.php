<?php
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';
require_once __DIR__ . '/core/Database.php';

$page   = $_GET['page']   ?? 'login';
$action = $_GET['action'] ?? 'index';

switch ($page) {

    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleLogin();
        } else {
            $controller->showLogin();
        }
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->handleLogout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'users':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        match($action) {
            'create' => $controller->create(),
            'store'  => $controller->store(),
            'edit'   => $controller->edit(),
            'update' => $controller->update(),
            'toggle' => $controller->toggleActive(),
            default  => $controller->index(),
        };
        break;

    case 'doctors':
        require_once __DIR__ . '/controllers/DoctorController.php';
        $controller = new DoctorController();
        match($action) {
            'edit'   => $controller->edit(),
            'update' => $controller->update(),
            default  => $controller->index(),
        };
        break;

    case 'appointments':
        require_once __DIR__ . '/controllers/AppointmentController.php';
        $controller = new AppointmentController();
        match($action) {
            'book'          => $controller->book(),
            'store'         => $controller->store(),
            'detail'        => $controller->detail(),
            'update_status' => $controller->updateStatus(),
            'cancel'        => $controller->cancel(),
            default         => $controller->index(),
        };
        break;

    case 'prescriptions':
        require_once __DIR__ . '/controllers/PrescriptionController.php';
        $controller = new PrescriptionController();
        match($action) {
            'add'      => $controller->add(),
            'store'    => $controller->store(),
            'download' => $controller->download(),
            default    => $controller->index(),
        };
        break;

    case 'reports':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController();
        $controller->index();
        break;

    case 'error':
        $code = $_GET['code'] ?? '404';
        if ($code === '403') {
            require_once __DIR__ . '/views/errors/403.php';
        } else {
            require_once __DIR__ . '/views/errors/404.php';
        }
        break;

    default:
        require_once __DIR__ . '/views/errors/404.php';
        break;
}