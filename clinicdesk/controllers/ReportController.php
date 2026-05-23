<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class ReportController {

    private DoctorModel $doctorModel;

    public function __construct() {
        $this->doctorModel = new DoctorModel();
    }

    public function index(): void {
        Auth::requireRole('admin');

        $doctors    = $this->doctorModel->getAll();
        $results    = [];
        $summary    = [];
        $startDate  = $_GET['start_date'] ?? '';
        $endDate    = $_GET['end_date'] ?? '';
        $doctorId   = (int)($_GET['doctor_id'] ?? 0);
        $status     = $_GET['status'] ?? '';

        if ($startDate && $endDate) {
            if (strtotime($startDate) > strtotime($endDate)) {
                flashMessage('error', 'Start date must be before end date.');
                redirect(BASE_URL . '/index.php?page=reports');
            }

            $results = $this->getReportData($startDate, $endDate, $doctorId, $status);
            $summary = $this->getSummary($results);

            // تصدير CSV
            if (($_GET['export'] ?? '') === 'csv') {
                $this->exportCSV($results);
            }
        }

        $pageTitle = 'Reports';
        require_once __DIR__ . '/../views/reports/index.php';
    }

    private function getReportData(string $start, string $end, int $doctorId, string $status): array {
        $db         = Database::getInstance();
        $conditions = ['a.appt_date >= ?', 'a.appt_date <= ?'];
        $params     = [$start, $end];
        $types      = 'ss';

        if ($doctorId) {
            $conditions[] = 'a.doctor_id = ?';
            $params[]     = $doctorId;
            $types       .= 'i';
        }
        if ($status) {
            $conditions[] = 'a.status = ?';
            $params[]     = $status;
            $types       .= 's';
        }

        $where  = 'WHERE ' . implode(' AND ', $conditions);
        $result = $db->query(
            "SELECT a.id, p.name as patient_name, u.name as doctor_name,
                    s.name as specialization_name, a.appt_date, a.appt_time,
                    a.status, a.reason
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             JOIN doctors d ON a.doctor_id = d.id
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             $where
             ORDER BY a.appt_date ASC, a.appt_time ASC",
            $types, $params
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getSummary(array $results): array {
        $summary = [
            'total'     => count($results),
            'pending'   => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];
        foreach ($results as $row) {
            $summary[$row['status']]++;
        }
        return $summary;
    }

    private function exportCSV(array $results): void {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // رأس الجدول
        fputcsv($output, [
            'ID', 'Patient Name', 'Doctor Name', 'Specialization',
            'Date', 'Time', 'Status', 'Reason'
        ]);

        // البيانات
        foreach ($results as $row) {
            fputcsv($output, [
                $row['id'],
                $row['patient_name'],
                $row['doctor_name'],
                $row['specialization_name'],
                $row['appt_date'],
                $row['appt_time'],
                $row['status'],
                $row['reason'],
            ]);
        }

        fclose($output);
        exit();
    }
}