<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('admin', 'doctor', 'patient'); ?>
<?php require_once __DIR__ . '/../../core/CSRF.php'; ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- فلتر -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-2"></i> Filter</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php">
                        <input type="hidden" name="page" value="appointments">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All</option>
                                        <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                                        <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                                            <?= ucfirst($s) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control"
                                           value="<?= htmlspecialchars($_GET['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                           value="<?= htmlspecialchars($_GET['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </div>
                            <?php if (Auth::role() === 'admin'): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Doctor</label>
                                    <select name="doctor_id" class="form-control">
                                        <option value="">All Doctors</option>
                                        <?php foreach ($doctors as $doc): ?>
                                        <option value="<?= $doc['id'] ?>"
                                            <?= ($_GET['doctor_id'] ?? '') == $doc['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($doc['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Search
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="btn btn-secondary ml-2">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    </form>
                </div>
            </div>

            <!-- الجدول -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i> Results
                    </h3>
                    <?php if (Auth::role() === 'patient'): ?>
                    <div class="card-tools">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=book"
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Book New
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php if (Auth::role() !== 'patient'): ?>
                                <th>Patient</th>
                                <?php endif; ?>
                                <?php if (Auth::role() !== 'doctor'): ?>
                                <th>Doctor</th>
                                <?php endif; ?>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $badges = [
                            'pending'   => 'warning',
                            'confirmed' => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                        ];
                        ?>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?= $appt['id'] ?></td>
                                <?php if (Auth::role() !== 'patient'): ?>
                                <td><?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <?php endif; ?>
                                <?php if (Auth::role() !== 'doctor'): ?>
                                <td><?= htmlspecialchars($appt['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <?php endif; ?>
                                <td><?= formatDate($appt['appt_date']) ?></td>
                                <td><?= formatTime($appt['appt_time']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $badges[$appt['status']] ?? 'secondary' ?>">
                                        <?= $appt['status'] ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($appt['reason'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appt['id'] ?>"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <?php if (Auth::role() === 'patient' && $appt['status'] === 'pending'): ?>
                                    <form method="POST"
                                          action="<?= BASE_URL ?>/index.php?page=appointments&action=cancel"
                                          class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                        <input type="hidden" name="id" value="<?= $appt['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Cancel this appointment?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">
                                    No appointments found.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($paginator->totalPages() > 1): ?>
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php if ($paginator->hasPrev()): ?>
                        <li class="page-item">
                            <a class="page-link"
                               href="?page=appointments&page_num=<?= $paginator->currentPage() - 1 ?>">
                                &laquo;
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                        <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                            <a class="page-link" href="?page=appointments&page_num=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($paginator->hasNext()): ?>
                        <li class="page-item">
                            <a class="page-link"
                               href="?page=appointments&page_num=<?= $paginator->currentPage() + 1 ?>">
                                &raquo;
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>