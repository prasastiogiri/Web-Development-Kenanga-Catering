<button class="dropdown-toggle text-light" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
    <?php echo e(auth()->user()->nama); ?>

</button>
<ul class="dropdown-menu" aria-labelledby="userMenu" data-bs-proper="false">
    <li>
        <a class="dropdown-item" href="<?php echo e(route('user.profile')); ?>">Profil Saya</a>
    </li>
    <li>
        <a class="dropdown-item" href="<?php echo e(route('pemesanan.riwayatPemesanan')); ?>">Riwayat
            Pesanan</a>
    </li>
    <li>
        <a href="#" class="dropdown-item"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>
        <form id="logout-form" action="<?php echo e(route('user.logout')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
    </li>
</ul>
<?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/layouts/user/partials/user-menu.blade.php ENDPATH**/ ?>