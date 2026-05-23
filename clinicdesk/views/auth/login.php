<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">

<div class="login-box">
    <div class="login-logo">
        <i class="fas fa-clinic-medical fa-2x text-primary"></i><br>
        <b><?= APP_NAME ?></b>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to your account</p>

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <form method="POST" action="<?= BASE_URL ?>/index.php?page=login">
                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control"
                           placeholder="Email" required autofocus
                           value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control"
                           placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>