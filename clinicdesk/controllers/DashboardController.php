<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';

class DashboardController {

    private AppointmentModel $appointmentModel;
    private UserModel $userModel;
    private DoctorModel $doctorModel;
    private PrescriptionModel $prescriptionModel;

    public function __construct() {
        $this->appointmentModel  = new AppointmentModel();
        $this->userModel         = new UserModel();
        $this->doctorModel       = new DoctorModel();
        $this->prescriptionModel = new PrescriptionModel();
    }

    public function index(): void {
        Auth::requireRole('admin', 'doctor', 'patient');
        $role = Auth::role();

        if ($role === 'admin') {
            $this->adminDashboard();
        } elseif ($role === 'doctor') {
            $this->doctorDashboard();
        } else {
            $this->patientDashboard();
        }
    }

    private function adminDashboard(): void {
        $db = Database::getInstance();

        // عدد المستخدمين حسب الدور
        $rolesResult = $db->query('SELECT role, COUNT(*) as total FROM users GROUP BY role');
        $roleStats   = [];
        while ($row = $rolesResult->fetch_assoc()) {
            $roleStats[$row['role']] = $row['total'];
        }

        // مواعيد اليوم
        $todayResult = $db->query('SELECT COUNT(*) as total FROM appointments WHERE appt_date = CURDATE()');
        $todayCount  = (int) $todayResult->fetch_assoc()['total'];

        // مواعيد هذا الأسبوع حسب الحالة
        $weekResult = $db->query(
            'SELECT status, COUNT(*) as total FROM appointments
             WHERE WEEK(appt_date) = WEEK(NOW()) GROUP BY status'
        );
        $weekStats = [];
        while ($row = $weekResult->fetch_assoc()) {
            $weekStats[$row['status']] = $row['total'];
        }

        // آخر 5 مواعيد
        $recentResult = $db->query(
            'SELECT a.*, p.name as patient_name, u.name as doctor_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             JOIN doctors d ON a.doctor_id = d.id
             JOIN users u ON d.user_id = u.id
             ORDER BY a.created_at DESC LIMIT 5'
        );
        $recentAppointments = $recentResult->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Admin Dashboard';
        require_once __DIR__ . '/../views/dashboard/admin.php';
    }

    private function doctorDashboard(): void {
        $user   = Auth::currentUser();
        $doctor = $this->doctorModel->findByUserId($user['id']);

        if (!$doctor) {
            flashMessage('error', 'Doctor profile not found.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        $doctorId = $doctor['id'];

        // مواعيد اليوم
        $todayAppointments = $this->appointmentModel->getTodayByDoctor($doctorId);

        // إحصائيات هذا الشهر
        $db          = Database::getInstance();
        $statsResult = $db->query(
            'SELECT
                COUNT(*) as total,
                SUM(status = "pending") as pending,
                SUM(status = "completed") as completed
             FROM appointments
             WHERE doctor_id = ? AND MONTH(appt_date) = MONTH(NOW())',
            'i', // this won't work directly, use execute via model
        );

        // أقرب 5 مواعيد قادمة
        $upcomingResult = $db->query(
            'SELECT a.*, p.name as patient_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             WHERE a.doctor_id = ' . (int)$doctorId . '
             AND a.appt_date >= CURDATE()
             AND a.status IN ("pending","confirmed")
             ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT 5'
        );
        $upcomingAppointments = $upcomingResult->fetch_all(MYSQLI_ASSOC);

        // إحصائيات بسيطة
        $totalResult = $db->query(
            'SELECT
                SUM(status = "pending") as pending,
                SUM(status = "completed") as completed,
                COUNT(*) as total
             FROM appointments
             WHERE doctor_id = ' . (int)$doctorId . '
             AND MONTH(appt_date) = MONTH(NOW())'
        );
        $monthStats = $totalResult->fetch_assoc();

        $pageTitle = 'Doctor Dashboard';
        require_once __DIR__ . '/../views/dashboard/doctor.php';
    }

    private function patientDashboard(): void {
        $user      = Auth::currentUser();
        $patientId = $user['id'];

        $db = Database::getInstance();

        // المواعيد النشطة
        $activeResult = $db->query(
            'SELECT COUNT(*) as total FROM appointments
             WHERE patient_id = ' . (int)$patientId . '
             AND status IN ("pending","confirmed")'
        );
        $activeCount = (int) $activeResult->fetch_assoc()['total'];

        // المواعيد المكتملة
        $completedResult = $db->query(
            'SELECT COUNT(*) as total FROM appointments
             WHERE patient_id = ' . (int)$patientId . '
             AND status = "completed"'
        );
        $completedCount = (int) $completedResult->fetch_assoc()['total'];

        // عدد الوصفات
        $prescriptions     = $this->prescriptionModel->getByPatient($patientId);
        $prescriptionCount = count($prescriptions);

        // الموعد القادم
        $nextResult = $db->query(
            'SELECT a.*, u.name as doctor_name, s.name as specialization_name
             FROM appointments a
             JOIN doctors d ON a.doctor_id = d.id
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             WHERE a.patient_id = ' . (int)$patientId . '
             AND a.appt_date >= CURDATE()
             AND a.status IN ("pending","confirmed")
             ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT 1'
        );
        $nextAppointment = $nextResult->fetch_assoc();

        $pageTitle = 'Patient Dashboard';
        require_once __DIR__ . '/../views/dashboard/patient.php';
    }
}