<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Brand Logo -->
    <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="brand-link">
        <i class="fas fa-clinic-medical brand-image ml-3" style="font-size:1.5rem;color:#fff;"></i>
        <span class="brand-text font-weight-light"><?= APP_NAME ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x ml-1" style="color:#c2c7d0;"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?= htmlspecialchars(Auth::currentUser()['name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <!-- Dashboard — كل الأدوار -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=dashboard"
                       class="nav-link <?= isActivePage('dashboard') ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if (Auth::role() === 'admin'): ?>

                    <!-- إدارة المستخدمين -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=users"
                           class="nav-link <?= isActivePage('users') ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>

                    <!-- إدارة الأطباء -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=doctors"
                           class="nav-link <?= isActivePage('doctors') ?>">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Doctors</p>
                        </a>
                    </li>

                    <!-- المواعيد -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments"
                           class="nav-link <?= isActivePage('appointments') ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Appointments</p>
                        </a>
                    </li>

                    <!-- التقارير -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=reports"
                           class="nav-link <?= isActivePage('reports') ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports</p>
                        </a>
                    </li>

                <?php elseif (Auth::role() === 'doctor'): ?>

                    <!-- جدول المواعيد -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments"
                           class="nav-link <?= isActivePage('appointments') ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>My Schedule</p>
                        </a>
                    </li>

                <?php elseif (Auth::role() === 'patient'): ?>

                    <!-- حجز موعد -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=book"
                           class="nav-link">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Book Appointment</p>
                        </a>
                    </li>

                    <!-- مواعيدي -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments"
                           class="nav-link <?= isActivePage('appointments') ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>My Appointments</p>
                        </a>
                    </li>

                    <!-- وصفاتي -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions"
                           class="nav-link <?= isActivePage('prescriptions') ?>">
                            <i class="nav-icon fas fa-file-medical"></i>
                            <p>My Prescriptions</p>
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </nav>
    </div>
</aside>