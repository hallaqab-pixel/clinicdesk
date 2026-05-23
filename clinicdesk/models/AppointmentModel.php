<?php
require_once __DIR__ . '/BaseModel.php';

class AppointmentModel extends BaseModel {

    public function book(array $data): bool {
        return $this->execute(
            'INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason)
             VALUES (?, ?, ?, ?, ?)',
            'iisss',
            [$data['patient_id'], $data['doctor_id'], $data['appt_date'], $data['appt_time'], $data['reason'] ?? null]
        );
    }

    public function hasConflict(int $doctorId, string $date, string $time): bool {
        $result = $this->execute(
            'SELECT COUNT(*) as total FROM appointments
             WHERE doctor_id = ? AND appt_date = ? AND appt_time = ?
             AND status != "cancelled"',
            'iss', [$doctorId, $date, $time]
        );
        return (int) $result->fetch_assoc()['total'] > 0;
    }

    public function findById(int $id): ?array {
        $result = $this->execute(
            'SELECT a.*, 
                    p.name as patient_name, p.phone as patient_phone,
                    u.name as doctor_name,
                    s.name as specialization_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             JOIN doctors d ON a.doctor_id = d.id
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             WHERE a.id = ?',
            'i', [$id]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function getByPatient(int $patientId, int $page, array $filters = []): array {
        $perPage    = ITEMS_PER_PAGE;
        $offset     = ($page - 1) * $perPage;
        $conditions = ['a.patient_id = ?'];
        $params     = [$patientId];
        $types      = 'i';

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $params[]     = $filters['status'];
            $types       .= 's';
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $params[]     = $filters['start_date'];
            $types       .= 's';
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $params[]     = $filters['end_date'];
            $types       .= 's';
        }

        $where    = 'WHERE ' . implode(' AND ', $conditions);
        $params[] = $perPage;
        $params[] = $offset;
        $types   .= 'ii';

        $result = $this->execute(
            "SELECT a.*, u.name as doctor_name, s.name as specialization_name
             FROM appointments a
             JOIN doctors d ON a.doctor_id = d.id
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             $where ORDER BY a.appt_date DESC, a.appt_time DESC
             LIMIT ? OFFSET ?",
            $types, $params
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByDoctor(int $doctorId, int $page, array $filters = []): array {
        $perPage    = ITEMS_PER_PAGE;
        $offset     = ($page - 1) * $perPage;
        $conditions = ['a.doctor_id = ?'];
        $params     = [$doctorId];
        $types      = 'i';

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $params[]     = $filters['status'];
            $types       .= 's';
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $params[]     = $filters['start_date'];
            $types       .= 's';
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $params[]     = $filters['end_date'];
            $types       .= 's';
        }

        $where    = 'WHERE ' . implode(' AND ', $conditions);
        $params[] = $perPage;
        $params[] = $offset;
        $types   .= 'ii';

        $result = $this->execute(
            "SELECT a.*, p.name as patient_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             $where ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT ? OFFSET ?",
            $types, $params
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll(int $page, array $filters = []): array {
        $perPage    = ITEMS_PER_PAGE;
        $offset     = ($page - 1) * $perPage;
        $conditions = [];
        $params     = [];
        $types      = '';

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $params[]     = $filters['status'];
            $types       .= 's';
        }
        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $params[]     = $filters['doctor_id'];
            $types       .= 'i';
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $params[]     = $filters['start_date'];
            $types       .= 's';
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $params[]     = $filters['end_date'];
            $types       .= 's';
        }

        $where    = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $params[] = $perPage;
        $params[] = $offset;
        $types   .= 'ii';

        $result = $this->execute(
            "SELECT a.*, p.name as patient_name, u.name as doctor_name, s.name as specialization_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             JOIN doctors d ON a.doctor_id = d.id
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             $where ORDER BY a.appt_date DESC, a.appt_time DESC
             LIMIT ? OFFSET ?",
            $types, $params
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countFiltered(string $scope, int $scopeId, array $filters = []): int {
        $conditions = [];
        $params     = [];
        $types      = '';

        if ($scope === 'patient') {
            $conditions[] = 'a.patient_id = ?';
            $params[]     = $scopeId;
            $types       .= 'i';
        } elseif ($scope === 'doctor') {
            $conditions[] = 'a.doctor_id = ?';
            $params[]     = $scopeId;
            $types       .= 'i';
        }

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $params[]     = $filters['status'];
            $types       .= 's';
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $params[]     = $filters['start_date'];
            $types       .= 's';
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $params[]     = $filters['end_date'];
            $types       .= 's';
        }

        $where  = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $result = $this->execute(
            "SELECT COUNT(*) as total FROM appointments a $where",
            $types, $params
        );
        return (int) $result->fetch_assoc()['total'];
    }

    public function updateStatus(int $id, string $status, string $notes = ''): bool {
        return $this->execute(
            'UPDATE appointments SET status = ?, doctor_notes = ? WHERE id = ?',
            'ssi', [$status, $notes, $id]
        );
    }

    public function getTodayByDoctor(int $doctorId): array {
        $result = $this->execute(
            'SELECT a.*, p.name as patient_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             WHERE a.doctor_id = ? AND a.appt_date = CURDATE()
             ORDER BY a.appt_time ASC',
            'i', [$doctorId]
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}