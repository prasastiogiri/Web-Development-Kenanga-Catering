

<a href="" class="text-light text-decoration-none position-relative">
    <i class="bi bi-cart fs-4 "></i>
    <?php if(auth()->check() && auth()->user()->keranjangItems->count() > 0): ?>
        <span id="cart-item-count"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger "
            style="display: <?php echo e(auth()->check() && auth()->user()->keranjangItems->count() > 0 ? 'inline-block' : 'none'); ?>;">
            <?php echo e(auth()->check() ? auth()->user()->keranjangItems->count() : 0); ?>

        </span>
    <?php endif; ?>
</a>
<div class="cart-popup">
    <?php if(auth()->check() && auth()->user()->keranjangItems->count() > 0): ?>
        <ul>
            <?php
                $totalHarga = 0;
            ?>
            <?php $__currentLoopData = auth()->user()->keranjangItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $itemHarga = $item->paket ? $item->paket->harga : ($item->produk ? $item->produk->harga : 0);
                    $itemNama = $item->paket
                        ? $item->paket->nama
                        : ($item->produk
                            ? $item->produk->nama
                            : 'Tidak Diketahui');
                    $itemFoto = $item->paket
                        ? $item->paket->foto
                        : ($item->produk
                            ? $item->produk->foto
                            : 'default.jpg');
                    $totalHarga += $itemHarga * $item->jumlah;
                ?>
                <li class="cart-item" data-id="<?php echo e($item->id); ?>">
                    <div class="cart-item-img">
                        <img src="<?php echo e(asset('storage/' . $itemFoto)); ?>" alt="<?php echo e($itemNama); ?>" class="img-fluid">
                    </div>
                    <div class="cart-item-details">
                        <p><?php echo e($itemNama); ?></p>
                        <div class="input-group">
                            <button type="button" class="btn update-jumlah" data-item-id="<?php echo e($item->id); ?>"
                                data-change="-10">-</button>
                            <input type="number" name="jumlah" id="jumlah-<?php echo e($item->id); ?>" class="form-input"
                                value="<?php echo e($item->jumlah); ?>" min="0" step="10" required readonly>
                            <button type="button" class="btn update-jumlah" data-item-id="<?php echo e($item->id); ?>"
                                data-change="10">+</button>

                        </div>
                        <p id="total-price-<?php echo e($item->id); ?>" data-harga="<?php echo e($itemHarga); ?>">
                            Rp<?php echo e(number_format($itemHarga * $item->jumlah, 0, ',', '.')); ?>

                        </p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="btn btn-danger delete-item" data-item-id="<?php echo e($item->id); ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <div class="cart-total d-flex justify-content-between align-items-center">
            <h5>Total Harga:</h5>
            <h5 id="total-harga">
                Rp<?php echo e(number_format($totalHarga, 0, ',', '.')); ?>

            </h5>
        </div>
        <div class="text-center mt-3">
            <form action=" " method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal">
                    Pesan Sekarang
                </button>

            </form>
        </div>
    <?php else: ?>
        <div class="empty-icon">
            <i class="bi bi-cart-x"></i>
        </div>
        <p class="empty">Keranjang Anda Kosong</p>
    <?php endif; ?>
</div>
<?php if(auth()->guard()->check()): ?>
    <?php echo $__env->make('layouts.user.partials.pesan-sekarang-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/layouts/user/partials/cart.blade.php ENDPATH**/ ?>