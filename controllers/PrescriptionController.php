<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class PrescriptionController {

    private PrescriptionModel $prescriptionModel;
    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;

    public function __construct() {
        $this->prescriptionModel = new PrescriptionModel();
        $this->appointmentModel  = new AppointmentModel();
        $this->doctorModel       = new DoctorModel();
    }

    public function index(): void {
        Auth::requireRole('patient');
        $user          = Auth::currentUser();
        $prescriptions = $this->prescriptionModel->getByPatient($user['id']);
        $pageTitle     = 'My Prescriptions';
        require_once __DIR__ . '/../views/prescriptions/view.php';
    }

    public function add(): void {
        Auth::requireRole('doctor');

        $apptId      = (int)($_GET['appt_id'] ?? 0);
        $appointment = $this->appointmentModel->findById($apptId);
        $user        = Auth::currentUser();
        $doctor      = $this->doctorModel->findByUserId($user['id']);

        if (!$appointment || !$doctor) {
            redirect(BASE_URL . '/index.php?page=error&code=403');
        }

        // التحقق من الملكية والحالة
        if ((int)$appointment['doctor_id'] !== $doctor['id']) {
            redirect(BASE_URL . '/index.php?page=error&code=403');
        }
        if ($appointment['status'] !== 'completed') {
            flashMessage('error', 'Prescription can only be added to completed appointments.');
            redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $apptId);
        }
        if ($this->prescriptionModel->findByAppointmentId($apptId)) {
            flashMessage('error', 'Prescription already exists for this appointment.');
            redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $apptId);
        }

        $pageTitle = 'Add Prescription';
        require_once __DIR__ . '/../views/prescriptions/add.php';
    }

    public function store(): void {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $apptId     = (int)($_POST['appointment_id'] ?? 0);
        $diagnosis  = sanitize($_POST['diagnosis'] ?? '');
        $medications = sanitize($_POST['medications'] ?? '');
        $notes      = sanitize($_POST['notes'] ?? '');

        if (!$diagnosis || !$medications) {
            flashMessage('error', 'Diagnosis and medications are required.');
            redirect(BASE_URL . '/index.php?page=prescriptions&action=add&appt_id=' . $apptId);
        }

        // رفع ملف PDF
        $filePath = null;
        if (!empty($_FILES['prescription_file']['name'])) {
            $filePath = $this->uploadPDF($_FILES['prescription_file'], $apptId);
            if (!$filePath) {
                flashMessage('error', 'Invalid file. PDF only, max 3MB.');
                redirect(BASE_URL . '/index.php?page=prescriptions&action=add&appt_id=' . $apptId);
            }
        }

        $this->prescriptionModel->create([
            'appointment_id' => $apptId,
            'diagnosis'      => $diagnosis,
            'medications'    => $medications,
            'notes'          => $notes,
            'file_path'      => $filePath,
        ]);

        flashMessage('success', 'Prescription added successfully.');
        redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $apptId);
    }

    public function download(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        $id          = (int)($_GET['id'] ?? 0);
        $prescription = $this->prescriptionModel->findById($id);

        if (!$prescription) {
            flashMessage('error', 'Prescription not found.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $appointment = $this->appointmentModel->findById($prescription['appointment_id']);
        $user        = Auth::currentUser();
        $role        = Auth::role();

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

        if (!$prescription['file_path']) {
            flashMessage('error', 'No file attached to this prescription.');
            redirect(BASE_URL . '/index.php?page=prescriptions');
        }

        $filePath = UPLOAD_PRESCRIPTIONS . $prescription['file_path'];
        if (!file_exists($filePath)) {
            flashMessage('error', 'File not found on server.');
            redirect(BASE_URL . '/index.php?page=prescriptions');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription.pdf"');
        readfile($filePath);
        exit();
    }

    private function uploadPDF(array $file, int $apptId): ?string {
        if ($file['size'] > MAX_PRESCRIPTION_SIZE) return null;

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mimeType !== 'application/pdf') return null;

        $filename = 'prescription_' . $apptId . '_' . time() . '.pdf';
        $dest     = UPLOAD_PRESCRIPTIONS . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        return $filename;
    }
}