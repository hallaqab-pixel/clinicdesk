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
                    <h1 class="m-0">Manage Doctors</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Doctors</li>
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
                        <i class="fas fa-user-md mr-2"></i> All Doctors
                    </h3>
                    <div class="card-tools">
                        <a href="<?= BASE_URL ?>/index.php?page=users&action=create"
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Add Doctor
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Specialization</th>
                                <th>Fee</th>
                                <th>Available Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td><?= $doctor['id'] ?></td>
                                <td><?= htmlspecialchars($doctor['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= htmlspecialchars($doctor['specialization_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>$<?= number_format($doctor['consultation_fee'], 2) ?></td>
                                <td>
                                    <small><?= htmlspecialchars($doctor['available_days'], ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $doctor['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $doctor['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/index.php?page=doctors&action=edit&id=<?= $doctor['id'] ?>"
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
                               href="?page=doctors&page_num=<?= $paginator->currentPage() - 1 ?>">
                                &laquo;
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                        <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                            <a class="page-link" href="?page=doctors&page_num=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($paginator->hasNext()): ?>
                        <li class="page-item">
                            <a class="page-link"
                               href="?page=doctors&page_num=<?= $paginator->currentPage() + 1 ?>">
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