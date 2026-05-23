<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('patient'); ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">My Prescriptions</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Prescriptions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-medical mr-2"></i> All Prescriptions
                    </h3>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($prescriptions)): ?>
                        <p class="text-center text-muted p-4">
                            <i class="fas fa-file-medical fa-3x mb-3 d-block"></i>
                            No prescriptions available yet.
                        </p>
                    <?php else: ?>
                    <table class="table table-striped table-hover data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Diagnosis</th>
                                <th>Medications</th>
                                <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($prescriptions as $prescription): ?>
                            <tr>
                                <td><?= $prescription['id'] ?></td>
                                <td><?= htmlspecialchars($prescription['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= formatDate($prescription['appt_date']) ?></td>
                                <td>
                                    <?= htmlspecialchars(
                                        strlen($prescription['diagnosis']) > 60
                                            ? substr($prescription['diagnosis'], 0, 60) . '...'
                                            : $prescription['diagnosis'],
                                        ENT_QUOTES, 'UTF-8'
                                    ) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(
                                        strlen($prescription['medications']) > 60
                                            ? substr($prescription['medications'], 0, 60) . '...'
                                            : $prescription['medications'],
                                        ENT_QUOTES, 'UTF-8'
                                    ) ?>
                                </td>
                                <td>
                                    <?php if ($prescription['file_path']): ?>
                                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=download&id=<?= $prescription['id'] ?>"
                                           class="btn btn-sm btn-success">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No file</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>