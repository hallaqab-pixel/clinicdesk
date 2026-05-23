<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('admin', 'doctor'); ?>
<?php require_once __DIR__ . '/../../core/CSRF.php'; ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=appointments">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i> Update Appointment #<?= $appointment['id'] ?>
                    </h3>
                </div>
                <form method="POST"
                      action="<?= BASE_URL ?>/index.php?page=appointments&action=update_status">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Patient</label>
                                    <input type="text" class="form-control"
                                           value="<?= htmlspecialchars($appointment['patient_name'], ENT_QUOTES, 'UTF-8') ?>"
                                           disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Doctor</label>
                                    <input type="text" class="form-control"
                                           value="<?= htmlspecialchars($appointment['doctor_name'], ENT_QUOTES, 'UTF-8') ?>"
                                           disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="text" class="form-control"
                                           value="<?= formatDate($appointment['appt_date']) ?>"
                                           disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Time</label>
                                    <input type="text" class="form-control"
                                           value="<?= formatTime($appointment['appt_time']) ?>"
                                           disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Status</label><br>
                                    <?php
                                    $badges = [
                                        'pending'   => 'warning',
                                        'confirmed' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $badges[$appointment['status']] ?? 'secondary' ?> p-2">
                                        <?= $appointment['status'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>New Status</label>
                                    <select name="status" class="form-control">
                                        <?php if ($appointment['status'] === 'pending'): ?>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="cancelled">Cancelled</option>
                                        <?php elseif ($appointment['status'] === 'confirmed'): ?>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        <?php else: ?>
                                            <option value="<?= $appointment['status'] ?>">
                                                <?= ucfirst($appointment['status']) ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Doctor Notes</label>
                            <textarea name="doctor_notes" class="form-control" rows="4"
                                      placeholder="Add notes about this appointment...">
<?= htmlspecialchars($appointment['doctor_notes'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </textarea>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Save Changes
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