<?php require_once __DIR__ . '/../../core/Auth.php'; Auth::requireRole('patient'); ?>
<?php require_once __DIR__ . '/../../core/CSRF.php'; ?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Book Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Book Appointment</li>
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
                        <i class="fas fa-calendar-plus mr-2"></i> New Appointment
                    </h3>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=store">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Doctor</label>
                                    <select name="doctor_id" class="form-control" id="doctorSelect" required>
                                        <option value="">-- Choose Doctor --</option>
                                        <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= $doctor['id'] ?>"
                                                data-days="<?= htmlspecialchars($doctor['available_days'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($doctor['name'], ENT_QUOTES, 'UTF-8') ?>
                                            — <?= htmlspecialchars($doctor['specialization_name'], ENT_QUOTES, 'UTF-8') ?>
                                            ($<?= number_format($doctor['consultation_fee'], 2) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- أيام عمل الدكتور -->
                                <div id="availableDaysInfo" class="alert alert-info" style="display:none;">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Available Days:</strong>
                                    <span id="availableDaysText"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Preferred Date</label>
                                    <input type="date" name="appt_date" class="form-control"
                                           min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Time Slot</label>
                                    <select name="appt_time" class="form-control" required>
                                        <option value="">-- Choose Time --</option>
                                        <?php
                                        $times = [
                                            '09:00','09:30','10:00','10:30',
                                            '11:00','11:30','12:00','12:30',
                                            '13:00','13:30','14:00','14:30',
                                            '15:00','15:30','16:00'
                                        ];
                                        foreach ($times as $time):
                                        ?>
                                        <option value="<?= $time ?>">
                                            <?= date('h:i A', strtotime($time)) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reason for Visit</label>
                                    <input type="text" name="reason" class="form-control"
                                           placeholder="Brief reason for visit">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-check mr-1"></i> Book Appointment
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="btn btn-secondary ml-2">
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
document.getElementById('doctorSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const days     = selected.getAttribute('data-days');
    const infoBox  = document.getElementById('availableDaysInfo');
    const daysText = document.getElementById('availableDaysText');

    if (days) {
        daysText.textContent = days;
        infoBox.style.display = 'block';
    } else {
        infoBox.style.display = 'none';
    }
});
</script>