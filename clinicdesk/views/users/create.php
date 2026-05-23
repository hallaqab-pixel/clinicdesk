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
                    <h1 class="m-0">Create User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=users">Users</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus mr-2"></i> New User
                    </h3>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=users&action=store">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="name" class="form-control"
                                           placeholder="Full Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                           placeholder="Email" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control"
                                           placeholder="Password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                           placeholder="Phone">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" class="form-control" id="roleSelect">
                                        <option value="patient">Patient</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- حقول خاصة بالدكتور -->
                        <div id="doctorFields" style="display:none;">
                            <hr>
                            <h5><i class="fas fa-user-md mr-2"></i>Doctor Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Specialization</label>
                                        <select name="specialization_id" class="form-control">
                                            <?php foreach ($specializations as $spec): ?>
                                            <option value="<?= $spec['id'] ?>">
                                                <?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Consultation Fee ($)</label>
                                        <input type="number" name="consultation_fee" class="form-control"
                                               placeholder="0.00" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Bio</label>
                                        <textarea name="bio" class="form-control" rows="3"
                                                  placeholder="Doctor bio..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Available Days</label><br>
                                <?php
                                $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                                foreach ($days as $day):
                                ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           name="available_days[]" value="<?= $day ?>"
                                           <?= in_array($day, ['Sun','Mon','Tue','Wed','Thu']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $day ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Create User
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=users" class="btn btn-secondary ml-2">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    document.getElementById('doctorFields').style.display =
        this.value === 'doctor' ? 'block' : 'none';
});
</script>