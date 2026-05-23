<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="nav-link">Home</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <!-- اسم المستخدم -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle mr-1"></i>
                <?= htmlspecialchars(Auth::currentUser()['name'], ENT_QUOTES, 'UTF-8') ?>
                <span class="badge badge-<?= Auth::role() === 'admin' ? 'danger' : (Auth::role() === 'doctor' ? 'info' : 'success') ?> ml-1">
                    <?= Auth::role() ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <!-- زر الخروج -->
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->