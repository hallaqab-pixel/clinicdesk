<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('admin'); ?>
<?php require_once __DIR__ . '/../../core/CSRF.php'; ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- فلتر التقرير -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-2"></i> Report Filters
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php">
                        <input type="hidden" name="page" value="reports">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control"
                                           value="<?= htmlspecialchars($_GET['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control"
                                           value="<?= htmlspecialchars($_GET['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           required>
                                </div>
                            </div>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All</option>
                                        <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                                        <option value="<?= $s ?>"
                                            <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                                            <?= ucfirst($s) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Generate Report
                        </button>
                        <?php if (!empty($results)): ?>
                        <a href="<?= BASE_URL ?>/index.php?page=reports&start_date=<?= urlencode($_GET['start_date'] ?? '') ?>&end_date=<?= urlencode($_GET['end_date'] ?? '') ?>&doctor_id=<?= urlencode($_GET['doctor_id'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&export=csv"
                           class="btn btn-success ml-2">
                            <i class="fas fa-file-csv mr-1"></i> Export CSV
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- نتائج التقرير -->
            <?php if (!empty($results)): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Results
                        <span class="badge badge-primary ml-2"><?= $summary['total'] ?> total</span>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
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
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['specialization_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= formatDate($row['appt_date']) ?></td>
                                <td><?= formatTime($row['appt_time']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $badges[$row['status']] ?? 'secondary' ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['reason'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ملخص -->
                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col">
                            <strong>Total:</strong>
                            <span class="badge badge-primary p-2"><?= $summary['total'] ?></span>
                        </div>
                        <div class="col">
                            <strong>Pending:</strong>
                            <span class="badge badge-warning p-2"><?= $summary['pending'] ?></span>
                        </div>
                        <div class="col">
                            <strong>Confirmed:</strong>
                            <span class="badge badge-info p-2"><?= $summary['confirmed'] ?></span>
                        </div>
                        <div class="col">
                            <strong>Completed:</strong>
                            <span class="badge badge-success p-2"><?= $summary['completed'] ?></span>
                        </div>
                        <div class="col">
                            <strong>Cancelled:</strong>
                            <span class="badge badge-danger p-2"><?= $summary['cancelled'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif (isset($_GET['start_date'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                No results found for the selected filters.
            </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>