
<?php switch($status):
    case ('pending'): ?>
        <span class="badge py-2 bg-warning">Menunggu Pembayaran</span>
        <?php break; ?>
    <?php case ('settlement'): ?>
        <span class="badge py-2 bg-success">Pembayaran Berhasil</span>
        <?php break; ?>
    <?php case ('capture'): ?>
        <span class="badge py-2 bg-success">Pembayaran Berhasil</span>
        <?php break; ?>
    <?php case ('deny'): ?>
        <span class="badge py-2 bg-danger">Pembayaran Ditolak</span>
        <?php break; ?>
    <?php case ('cancel'): ?>
        <span class="badge py-2 bg-danger">Pembayaran Dibatalkan</span>
        <?php break; ?>
    <?php case ('expire'): ?>
        <span class="badge py-2 bg-secondary">Pembayaran Kedaluwarsa</span>
        <?php break; ?>
    <?php default: ?>
        <span class="badge py-2 bg-info"><?php echo e(ucfirst($status)); ?></span>
<?php endswitch; ?>
<?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/user/pemesanan/partials/status-badge.blade.php ENDPATH**/ ?>