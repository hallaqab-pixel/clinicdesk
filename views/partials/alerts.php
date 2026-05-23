<?php
$flash = getFlash();
if ($flash):
    $type = $flash['type'] === 'success' ? 'success' : 'danger';
?>
<div class="alert alert-<?= $type ?> alert-dismissible fade show mx-3 mt-3">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> mr-2"></i>
    <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>