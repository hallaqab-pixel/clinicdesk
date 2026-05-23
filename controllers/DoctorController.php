<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class DoctorController {

    private DoctorModel $doctorModel;
    private SpecializationModel $specializationModel;

    public function __construct() {
        $this->doctorModel         = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index(): void {
        Auth::requireRole('admin');
        $page      = max(1, (int)($_GET['page_num'] ?? 1));
        $total     = $this->doctorModel->countAll();
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $doctors   = $this->doctorModel->getAllPaginated($page);
        $pageTitle = 'Manage Doctors';
        require_once __DIR__ . '/../views/doctors/list.php';
    }

    public function edit(): void {
        Auth::requireRole('admin', 'doctor');
        $id     = (int)($_GET['id'] ?? 0);
        $doctor = $this->doctorModel->findById($id);

        if (!$doctor) {
            flashMessage('error', 'Doctor not found.');
            redirect(BASE_URL . '/index.php?page=doctors');
        }

        // الدكتور يعدل فقط ملفه الشخصي
        if (Auth::role() === 'doctor') {
            $currentUser    = Auth::currentUser();
            $currentDoctor  = $this->doctorModel->findByUserId($currentUser['id']);
            if (!$currentDoctor || $currentDoctor['id'] !== $doctor['id']) {
                redirect(BASE_URL . '/index.php?page=error&code=403');
            }
        }

        $specializations = $this->specializationModel->getAll();
        $pageTitle       = 'Edit Doctor';
        require_once __DIR__ . '/../views/doctors/edit.php';
    }

    public function update(): void {
        Auth::requireRole('admin', 'doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=doctors');
        }

        $id   = (int)($_POST['id'] ?? 0);
        $days = isset($_POST['available_days'])
            ? implode(',', $_POST['available_days'])
            : 'Sun,Mon,Tue,Wed,Thu';

        // رفع صورة الدكتور
        $photoPath = null;
        if (!empty($_FILES['doctor_photo']['name'])) {
            $photoPath = $this->uploadPhoto($_FILES['doctor_photo'], $id);
            if (!$photoPath) {
                flashMessage('error', 'Invalid photo. JPEG/PNG only, max 1MB.');
                redirect(BASE_URL . '/index.php?page=doctors&action=edit&id=' . $id);
            }
        }

        $data = [
            'specialization_id' => (int)($_POST['specialization_id'] ?? 1),
            'bio'               => sanitize($_POST['bio'] ?? ''),
            'consultation_fee'  => (float)($_POST['consultation_fee'] ?? 0),
            'available_days'    => $days,
        ];

        $this->doctorModel->update($id, $data);

        if ($photoPath) {
            $doctor = $this->doctorModel->findById($id);
            // احفظ مسار الصورة في جدول users
            $db = Database::getInstance();
            $db->query(
                'UPDATE users SET avatar = ? WHERE id = ?',
                'si', [$photoPath, $doctor['user_id']]
            );
        }

        flashMessage('success', 'Doctor profile updated.');
        redirect(BASE_URL . '/index.php?page=doctors');
    }

    private function uploadPhoto(array $file, int $doctorId): ?string {
        if ($file['size'] > MAX_AVATAR_SIZE) return null;

        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) return null;

        $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowed)) return null;

        $ext      = $imageInfo[2] === IMAGETYPE_JPEG ? 'jpg' : 'png';
        $filename = 'doctor_' . $doctorId . '_' . time() . '.' . $ext;
        $dest     = UPLOAD_DOCTOR_PHOTOS . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        return $filename;
    }
}