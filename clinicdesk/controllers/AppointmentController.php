<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class AppointmentController {

    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;

    public function __construct() {
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel      = new DoctorModel();
    }

    public function index(): void {
        Auth::requireRole('admin', 'doctor', 'patient');
        $role    = Auth::role();
        $user    = Auth::currentUser();
        $page    = max(1, (int)($_GET['page_num'] ?? 1));
        $filters = [
            'status'     => $_GET['status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date'   => $_GET['end_date'] ?? '',
            'doctor_id'  => $_GET['doctor_id'] ?? '',
        ];

        if ($role === 'patient') {
            $total        = $this->appointmentModel->countFiltered('patient', $user['id'], $filters);
            $paginator    = new Paginator($total, ITEMS_PER_PAGE, $page);
            $appointments = $this->appointmentModel->getByPatient($user['id'], $page, $filters);
        } elseif ($role === 'doctor') {
            $doctor       = $this->doctorModel->findByUserId($user['id']);
            $total        = $this->appointmentModel->countFiltered('doctor', $doctor['id'], $filters);
            $paginator    = new Paginator($total, ITEMS_PER_PAGE, $page);
            $appointments = $this->appointmentModel->getByDoctor($doctor['id'], $page, $filters);
        } else {
            $total        = $this->appointmentModel->countFiltered('all', 0, $filters);
            $paginator    = new Paginator($total, ITEMS_PER_PAGE, $page);
            $appointments = $this->appointmentModel->getAll($page, $filters);
        }

        $doctors   = $this->doctorModel->getAll();
        $pageTitle = 'Appointments';
        require_once __DIR__ . '/../views/appointments/list.php';
    }

    public function book(): void {
        Auth::requireRole('patient');
        $doctors   = $this->doctorModel->getAll();
        $pageTitle = 'Book Appointment';
        require_once __DIR__ . '/../views/appointments/book.php';
    }

    public function store(): void {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        $user      = Auth::currentUser();
        $doctorId  = (int)($_POST['doctor_id'] ?? 0);
        $date      = $_POST['appt_date'] ?? '';
        $time      = $_POST['appt_time'] ?? '';
        $reason    = sanitize($_POST['reason'] ?? '');

        // التحقق من التاريخ
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            flashMessage('error', 'Cannot book an appointment in the past.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        // التحقق من أيام العمل
        $availableDays = $this->doctorModel->getAvailableDays($doctorId);
        $dayOfWeek     = date('D', strtotime($date));
        if (!in_array($dayOfWeek, $availableDays)) {
            flashMessage('error', 'Doctor is not available on this day.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        // التحقق من التعارض
        if ($this->appointmentModel->hasConflict($doctorId, $date, $time)) {
            flashMessage('error', 'This slot is already booked. Please choose another time.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        $this->appointmentModel->book([
            'patient_id' => $user['id'],
            'doctor_id'  => $doctorId,
            'appt_date'  => $date,
            'appt_time'  => $time,
            'reason'     => $reason,
        ]);

        flashMessage('success', 'Appointment booked successfully.');
        redirect(BASE_URL . '/index.php?page=appointments');
    }

    public function detail(): void {
        Auth::requireRole('admin', 'doctor', 'patient');
        $id          = (int)($_GET['id'] ?? 0);
        $appointment = $this->appointmentModel->findById($id);

        if (!$appointment) {
            flashMessage('error', 'Appointment not found.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $user = Auth::currentUser();
        $role = Auth::role();

        // التحقق من الملكية
        if ($role === 'patient' && (int)$appointment['patient_id'] !== $user['id']) {
            redirect(BASE_URL . '/index.php?page=error&code=403');
        }
        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if (!$doctor || (int)$appointment['doctor_id'] !== $doctor['id']) {
                redirect(BASE_URL . '/index.php?page=error&code=403');
            }
        }

        $pageTitle = 'Appointment Detail';
        require_once __DIR__ . '/../views/appointments/detail.php';
    }

    public function updateStatus(): void {
        Auth::requireRole('admin', 'doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $notes  = sanitize($_POST['doctor_notes'] ?? '');

        $allowed = ['confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            flashMessage('error', 'Invalid status.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $this->appointmentModel->updateStatus($id, $status, $notes);
        flashMessage('success', 'Appointment status updated.');
        redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $id);
    }

    public function cancel(): void {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $id          = (int)($_POST['id'] ?? 0);
        $appointment = $this->appointmentModel->findById($id);
        $user        = Auth::currentUser();

        if (!$appointment || (int)$appointment['patient_id'] !== $user['id']) {
            redirect(BASE_URL . '/index.php?page=error&code=403');
        }

        if ($appointment['status'] !== 'pending') {
            flashMessage('error', 'Only pending appointments can be cancelled.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $this->appointmentModel->updateStatus($id, 'cancelled');
        flashMessage('success', 'Appointment cancelled.');
        redirect(BASE_URL . '/index.php?page=appointments');
    }
}