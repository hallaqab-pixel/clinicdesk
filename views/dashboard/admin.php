<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('admin'); ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Admin Dashboard</h1>
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

            <!-- إحصائيات المستخدمين -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $roleStats['doctor'] ?? 0 ?></h3>
                            <p>Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=doctors" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $roleStats['patient'] ?? 0 ?></h3>
                            <p>Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=users&role=patient" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $todayCount ?></h3>
                            <p>Appointments Today</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $weekStats['pending'] ?? 0 ?></h3>
                            <p>Pending This Week</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&status=pending" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- إحصائيات المواعيد هذا الأسبوع -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Appointments This Week by Status
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-3">
                                    <span class="badge badge-warning p-2" style="font-size:1rem;">
                                        Pending: <?= $weekStats['pending'] ?? 0 ?>
                                    </span>
                                </div>
                                <div class="col-3">
                                    <span class="badge badge-info p-2" style="font-size:1rem;">
                                        Confirmed: <?= $weekStats['confirmed'] ?? 0 ?>
                                    </span>
                                </div>
                                <div class="col-3">
                                    <span class="badge badge-success p-2" style="font-size:1rem;">
                                        Completed: <?= $weekStats['completed'] ?? 0 ?>
                                    </span>
                                </div>
                                <div class="col-3">
                                    <span class="badge badge-danger p-2" style="font-size:1rem;">
                                        Cancelled: <?= $weekStats['cancelled'] ?? 0 ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- آخر 5 مواعيد -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Recent Appointments
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($recentAppointments as $appt): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($appt['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= formatDate($appt['appt_date']) ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td>
                                            <?php
                                            $badges = [
                                                'pending'   => 'warning',
                                                'confirmed' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                            $badge = $badges[$appt['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?= $badge ?>">
                                                <?= $appt['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>