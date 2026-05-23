<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden | ClinicDesk</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="error-page mt-5">
                <h2 class="headline text-danger">403</h2>
                <div class="error-content">
                    <h3>
                        <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                        Access Denied
                    </h3>
                    <p>You do not have permission to access this page.</p>
                    <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="btn btn-danger">
                        <i class="fas fa-home mr-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>