<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('admin', 'doctor', 'patient'); ?>
<?php require_once __DIR__ . '/../../core/CSRF.php'; ?>
<?php require_once __DIR__ . '/../../models/PrescriptionModel.php'; ?>
<?php
$prescriptionModel = new PrescriptionModel();
$prescription      = $prescriptionModel->findByAppointmentId($appointment['id']);
?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointment Detail</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=appointments">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active">#<?= $appointment['id'] ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="row">
                <!-- تفاصيل الموعد -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-check mr-2"></i> Appointment Info
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php
                            $badges = [
                                'pending'   => 'warning',
                                'confirmed' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            ];
                            ?>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-<?= $badges[$appointment['status']] ?? 'secondary' ?> p-2">
                                            <?= $appointment['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Patient</th>
                                    <td><?= htmlspecialchars($appointment['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr>
                                    <th>Doctor</th>
                                    <td><?= htmlspecialchars($appointment['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr>
                                    <th>Specialization</th>
                                    <td><?= htmlspecialchars($appointment['specialization_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td><?= formatDate($appointment['appt_date']) ?></td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td><?= formatTime($appointment['appt_time']) ?></td>
                                </tr>
                                <tr>
                                    <th>Reason</th>
                                    <td><?= htmlspecialchars($appointment['reason'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <?php if ($appointment['doctor_notes']): ?>
                                <tr>
                                    <th>Doctor Notes</th>
                                    <td><?= htmlspecialchars($appointment['doctor_notes'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- الإجراءات -->
                <div class="col-md-6">

                    <!-- Doctor/Admin: تغيير الحالة -->
                    <?php if (in_array(Auth::role(), ['doctor', 'admin']) && $appointment['status'] !== 'cancelled'): ?>
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit mr-2"></i> Update Status
                            </h3>
                        </div>
                        <form method="POST"
                              action="<?= BASE_URL ?>/index.php?page=appointments&action=update_status">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                            <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>New Status</label>
                                    <select name="status" class="form-control">
                                        <?php if ($appointment['status'] === 'pending'): ?>
                                            <option value="confirmed">Confirm</option>
                                            <option value="cancelled">Cancel</option>
                                        <?php elseif ($appointment['status'] === 'confirmed'): ?>
                                            <option value="completed">Complete</option>
                                            <option value="cancelled">Cancel</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Doctor Notes (optional)</label>
                                    <textarea name="doctor_notes" class="form-control" rows="3"
                                              placeholder="Add notes..."><?= htmlspecialchars($appointment['doctor_notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save mr-1"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                    <!-- وصفة طبية -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-medical mr-2"></i> Prescription
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if ($prescription): ?>
                                <p><strong>Diagnosis:</strong><br>
                                    <?= htmlspecialchars($prescription['diagnosis'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <p><strong>Medications:</strong><br>
                                    <?= htmlspecialchars($prescription['medications'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <?php if ($prescription['notes']): ?>
                                <p><strong>Notes:</strong><br>
                                    <?= htmlspecialchars($prescription['notes'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <?php endif; ?>
                                <?php if ($prescription['file_path']): ?>
                                <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=download&id=<?= $prescription['id'] ?>"
                                   class="btn btn-success">
                                    <i class="fas fa-download mr-1"></i> Download PDF
                                </a>
                                <?php endif; ?>

                            <?php elseif (Auth::role() === 'doctor' && $appointment['status'] === 'completed'): ?>
                                <p class="text-muted">No prescription added yet.</p>
                                <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=add&appt_id=<?= $appointment['id'] ?>"
                                   class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Add Prescription
                                </a>

                            <?php else: ?>
                                <p class="text-muted">No prescription available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

            <a href="<?= BASE_URL ?>/index.php?page=appointments" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>