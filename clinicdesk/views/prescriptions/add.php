<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('doctor'); ?>
<?php require_once __DIR__ . '/../../core/CSRF.php'; ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Prescription</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=appointments">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active">Add Prescription</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- معلومات الموعد -->
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i> Appointment Info
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Patient:</strong><br>
                            <?= htmlspecialchars($appointment['patient_name'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Date:</strong><br>
                            <?= formatDate($appointment['appt_date']) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Time:</strong><br>
                            <?= formatTime($appointment['appt_time']) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Reason:</strong><br>
                            <?= htmlspecialchars($appointment['reason'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- فورم الوصفة -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-medical mr-2"></i> New Prescription
                    </h3>
                </div>
                <form method="POST"
                      action="<?= BASE_URL ?>/index.php?page=prescriptions&action=store"
                      enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                    <div class="card-body">

                        <div class="form-group">
                            <label>Diagnosis <span class="text-danger">*</span></label>
                            <textarea name="diagnosis" class="form-control" rows="4"
                                      placeholder="Enter diagnosis..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Medications <span class="text-danger">*</span></label>
                            <textarea name="medications" class="form-control" rows="4"
                                      placeholder="List medications, dosage, frequency..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Optional notes..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Prescription PDF (optional, max 3MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="prescription_file"
                                           class="custom-file-input" accept=".pdf">
                                    <label class="custom-file-label">Choose PDF file...</label>
                                </div>
                            </div>
                            <small class="text-muted">Only PDF files are accepted.</small>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Save Prescription
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appointment['id'] ?>"
                           class="btn btn-secondary ml-2">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>