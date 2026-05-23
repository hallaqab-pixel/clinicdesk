<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark"></aside>

<!-- Main Footer -->
<footer class="main-footer">
    <strong>
        <i class="fas fa-clinic-medical mr-1"></i>
        <?= APP_NAME ?>
    </strong>
    &mdash; Clinic Management Dashboard
    <div class="float-right d-none d-sm-inline-block">
        <b>Islamic University of Gaza</b>
    </div>
</footer>

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<!-- AdminLTE -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/dist/js/adminlte.min.js"></script>

<!-- تفعيل DataTables تلقائياً -->
<script>
    $(document).ready(function () {
        $('.data-table').DataTable({
            "paging":   false,
            "ordering": true,
            "info":     false
        });
    });
</script>

</body>
</html>