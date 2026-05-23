<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('admin'); ?>
<?php require_once __DIR__ . '/../../core/CSRF.php'; ?>
<?php require_once __DIR__ . '/../../core/helpers.php'; ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Users</li>
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
                        <i class="fas fa-users mr-2"></i> All Users
                    </h3>
                    <div class="card-tools">
                        <!-- فلتر الدور -->
                        <form method="GET" action="<?= BASE_URL ?>/index.php" class="d-inline-flex">
                            <input type="hidden" name="page" value="users">
                            <select name="role" class="form-control form-control-sm mr-2"
                                    onchange="this.form.submit()">
                                <option value="">All Roles</option>
                                <option value="admin"   <?= ($_GET['role'] ?? '') === 'admin'   ? 'selected' : '' ?>>Admin</option>
                                <option value="doctor"  <?= ($_GET['role'] ?? '') === 'doctor'  ? 'selected' : '' ?>>Doctor</option>
                                <option value="patient" <?= ($_GET['role'] ?? '') === 'patient' ? 'selected' : '' ?>>Patient</option>
                            </select>
                        </form>
                        <a href="<?= BASE_URL ?>/index.php?page=users&action=create"
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Add User
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php
                                    $roleBadge = [
                                        'admin'   => 'danger',
                                        'doctor'  => 'info',
                                        'patient' => 'success',
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $roleBadge[$user['role']] ?? 'secondary' ?>">
                                        <?= $user['role'] ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($user['phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="badge badge-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= formatDate($user['created_at']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/index.php?page=users&action=edit&id=<?= $user['id'] ?>"
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST"
                                          action="<?= BASE_URL ?>/index.php?page=users&action=toggle"
                                          class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit"
                                                class="btn btn-sm btn-<?= $user['is_active'] ? 'danger' : 'success' ?>"
                                                onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                                        </button>
                                    </form>
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
                               href="?page=users&page_num=<?= $paginator->currentPage() - 1 ?>&role=<?= $_GET['role'] ?? '' ?>">
                                &laquo;
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                        <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                            <a class="page-link"
                               href="?page=users&page_num=<?= $i ?>&role=<?= $_GET['role'] ?? '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($paginator->hasNext()): ?>
                        <li class="page-item">
                            <a class="page-link"
                               href="?page=users&page_num=<?= $paginator->currentPage() + 1 ?>&role=<?= $_GET['role'] ?? '' ?>">
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