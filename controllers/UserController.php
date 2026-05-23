<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class UserController {

    private UserModel $userModel;
    private DoctorModel $doctorModel;
    private SpecializationModel $specializationModel;

    public function __construct() {
        $this->userModel           = new UserModel();
        $this->doctorModel         = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index(): void {
        Auth::requireRole('admin');
        $page      = max(1, (int)($_GET['page_num'] ?? 1));
        $role      = $_GET['role'] ?? '';
        $total     = $this->userModel->countAll($role);
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $users     = $this->userModel->getAllPaginated($page, $role);
        $pageTitle = 'Manage Users';
        require_once __DIR__ . '/../views/users/list.php';
    }

    public function create(): void {
        Auth::requireRole('admin');
        $specializations = $this->specializationModel->getAll();
        $pageTitle       = 'Create User';
        require_once __DIR__ . '/../views/users/create.php';
    }

    public function store(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $name     = sanitize($_POST['name'] ?? '');
        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'patient';
        $phone    = sanitize($_POST['phone'] ?? '');

        if (!$name || !$email || !$password) {
            flashMessage('error', 'All fields are required.');
            redirect(BASE_URL . '/index.php?page=users&action=create');
        }

        if ($this->userModel->findByEmail($email)) {
            flashMessage('error', 'Email already exists.');
            redirect(BASE_URL . '/index.php?page=users&action=create');
        }

        $userId = $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role'     => $role,
            'phone'    => $phone,
        ]);

        // إذا كان الدور دكتور، أنشئ سجل في جدول doctors
        if ($role === 'doctor') {
            $days = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : 'Sun,Mon,Tue,Wed,Thu';
            $this->doctorModel->create([
                'user_id'           => $userId,
                'specialization_id' => (int)($_POST['specialization_id'] ?? 1),
                'bio'               => sanitize($_POST['bio'] ?? ''),
                'consultation_fee'  => (float)($_POST['consultation_fee'] ?? 0),
                'available_days'    => $days,
            ]);
        }

        flashMessage('success', 'User created successfully.');
        redirect(BASE_URL . '/index.php?page=users');
    }

    public function edit(): void {
        Auth::requireRole('admin');
        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            flashMessage('error', 'User not found.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $pageTitle = 'Edit User';
        require_once __DIR__ . '/../views/users/edit.php';
    }

    public function update(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $id    = (int)($_POST['id'] ?? 0);
        $name  = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        $this->userModel->update($id, [
            'name'   => $name,
            'phone'  => $phone,
            'avatar' => null,
        ]);

        flashMessage('success', 'User updated successfully.');
        redirect(BASE_URL . '/index.php?page=users');
    }

    public function toggleActive(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $id          = (int)($_POST['id'] ?? 0);
        $currentUser = Auth::currentUser();

        if ($id === (int)$currentUser['id']) {
            flashMessage('error', 'You cannot deactivate your own account.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $this->userModel->toggleActive($id);
        flashMessage('success', 'User status updated.');
        redirect(BASE_URL . '/index.php?page=users');
    }
}