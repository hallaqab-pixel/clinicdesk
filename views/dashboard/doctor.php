<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('doctor'); ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Doctor Dashboard</h1>
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

            <!-- إحصائيات الشهر -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $monthStats['total'] ?? 0 ?></h3>
                            <p>Total This Month</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $monthStats['pending'] ?? 0 ?></h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&status=pending" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $monthStats['completed'] ?? 0 ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&status=completed" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- مواعيد اليوم -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-day mr-2"></i>
                                Today's Appointments
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($todayAppointments)): ?>
                                <p class="text-center text-muted p-3">No appointments today.</p>
                            <?php else: ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Time</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($todayAppointments as $appt): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td><?= htmlspecialchars($appt['reason'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
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
                                        <td>
                                            <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appt['id'] ?>"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المواعيد القادمة -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-check mr-2"></i>
                                Upcoming Appointments
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($upcomingAppointments)): ?>
                                <p class="text-center text-muted p-3">No upcoming appointments.</p>
                            <?php else: ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($upcomingAppointments as $appt): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= formatDate($appt['appt_date']) ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $badges[$appt['status']] ?? 'secondary' ?>">
                                                <?= $appt['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appt['id'] ?>"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>