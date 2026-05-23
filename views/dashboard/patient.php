<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('patient'); ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Patient Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- الموعد القادم -->
            <?php if ($nextAppointment): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-check mr-2"></i>
                                Next Appointment
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <i class="fas fa-user-md fa-3x text-primary"></i>
                                    <h5 class="mt-2"><?= htmlspecialchars($nextAppointment['doctor_name'], ENT_QUOTES, 'UTF-8') ?></h5>
                                    <small class="text-muted"><?= htmlspecialchars($nextAppointment['specialization_name'], ENT_QUOTES, 'UTF-8') ?></small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <i class="fas fa-calendar fa-3x text-info"></i>
                                    <h5 class="mt-2"><?= formatDate($nextAppointment['appt_date']) ?></h5>
                                    <small class="text-muted">Date</small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <i class="fas fa-clock fa-3x text-warning"></i>
                                    <h5 class="mt-2"><?= formatTime($nextAppointment['appt_time']) ?></h5>
                                    <small class="text-muted">Time</small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <?php
                                    $badges = [
                                        'pending'   => 'warning',
                                        'confirmed' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                    $badge = $badges[$nextAppointment['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge badge-<?= $badge ?> p-3" style="font-size:1rem;">
                                        <?= $nextAppointment['status'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- الإحصائيات -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $activeCount ?></h3>
                            <p>Active Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $completedCount ?></h3>
                            <p>Completed Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&status=completed" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $prescriptionCount ?></h3>
                            <p>Prescriptions</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-medical"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- أزرار سريعة -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-2"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <a href="<?= BASE_URL ?>/index.php?page=appointments&action=book"
                               class="btn btn-primary mr-2">
                                <i class="fas fa-plus-circle mr-1"></i> Book Appointment
                            </a>
                            <a href="<?= BASE_URL ?>/index.php?page=appointments"
                               class="btn btn-info mr-2">
                                <i class="fas fa-calendar-check mr-1"></i> My Appointments
                            </a>
                            <a href="<?= BASE_URL ?>/index.php?page=prescriptions"
                               class="btn btn-success">
                                <i class="fas fa-file-medical mr-1"></i> My Prescriptions
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>