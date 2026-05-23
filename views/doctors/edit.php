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
                    <h1 class="m-0">Edit Doctor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=doctors">Doctors</a>
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
                        <i class="fas fa-user-md mr-2"></i>
                        Edit: <?= htmlspecialchars($doctor['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=doctors&action=update"
                      enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="id" value="<?= $doctor['id'] ?>">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Specialization</label>
                                    <select name="specialization_id" class="form-control">
                                        <?php foreach ($specializations as $spec): ?>
                                        <option value="<?= $spec['id'] ?>"
                                            <?= (int)$spec['id'] === (int)$doctor['specialization_id'] ? 'selected' : '' ?>>
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
                                           value="<?= $doctor['consultation_fee'] ?>"
                                           step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Bio</label>
                                    <textarea name="bio" class="form-control" rows="4">
<?= htmlspecialchars($doctor['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label><br>
                            <?php
                            $allDays      = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                            $selectedDays = explode(',', $doctor['available_days']);
                            foreach ($allDays as $day):
                            ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox"
                                       name="available_days[]" value="<?= $day ?>"
                                       <?= in_array($day, $selectedDays) ? 'checked' : '' ?>>
                                <label class="form-check-label"><?= $day ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-group">
                            <label>Profile Photo (JPEG/PNG, max 1MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="doctor_photo"
                                           class="custom-file-input" accept="image/*">
                                    <label class="custom-file-label">Choose photo...</label>
                                </div>
                            </div>
                            <?php if (!empty($doctor['avatar'])): ?>
                            <small class="text-muted">Current photo: <?= htmlspecialchars($doctor['avatar'], ENT_QUOTES, 'UTF-8') ?></small>
                            <?php endif; ?>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Save Changes
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=doctors" class="btn btn-secondary ml-2">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>