<link rel="stylesheet" href="<?php echo e(asset('css/riwayatPemesanan.css')); ?>">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="sub-title">
            <h1>Riwayat Pemesanan</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-body shadow-lg">
                    <?php if($riwayat->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x"></i>
                            <h5>Belum ada pesanan</h5>
                            <p class="text-muted">Anda belum melakukan pemesanan apapun.</p>
                            <a href="<?php echo e(route('homepage')); ?>" class="btn btn-primary">
                                Mulai Belanja
                            </a>
                        </div>
                    <?php else: ?>
                        <?php
                            $groupedOrders = $riwayat->groupBy('order_id');
                        ?>
                        <div class="table-responsive">
                            <table class="table" id="pemesananTable">
                                <thead>
                                    <tr class="text-center">
                                        <th>Order ID</th>
                                        <th>Tanggal Pesan</th>
                                        <th>Items</th>
                                        <th>Total Harga</th>
                                        <th>Status Pembayaran</th>
                                        <th>Status Pemesanan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $groupedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderId => $orderGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $order = $orderGroup->first();
                                            $allItems = $orderGroup->flatMap(function ($item) {
                                                return $item['items'] ?? [];
                                            });
                                            $totalPrice = $orderGroup->sum('total_harga');
                                        ?>
                                        <tr class="text-justify">
                                            <td><?php echo e($orderId); ?></td>
                                            <td><?php echo e(date('d M Y H:i', strtotime($order['created_at']))); ?></td>
                                            <td class="order-items-cell">
                                                <?php $__currentLoopData = $allItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="mb-2">
                                                        <strong><?php echo e(optional($item['paket'])['nama'] ?? (optional($item['produk'])['nama'] ?? 'N/A')); ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            Jumlah: <?php echo e($item['jumlah'] ?? 0); ?>

                                                        </small>
                                                    </div>
                                                    <?php if(!$loop->last): ?>
                                                        <hr class="my-1">
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </td>
                                            <td class="text-center">
                                                <strong>Rp <?php echo e(number_format($totalPrice, 0, ',', '.')); ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $__env->make('user.pemesanan.partials.status-badge', [
                                                    'status' => $order['status'],
                                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo e($order['status_pemesanan'] ?? 'Belum selesai'); ?>

                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detailModal<?php echo e($orderId); ?>">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </button>
                                                    <?php if($order['status'] !== 'paid'): ?>
                                                        <a href="<?php echo e(route('pemesanan.status', ['orderId' => $orderId])); ?>"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fas fa-credit-card"></i> Bayar
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <?php $__currentLoopData = $groupedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderId => $orderGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $order = $orderGroup->first();
                                $allItems = $orderGroup->flatMap(function ($item) {
                                    return $item['items'] ?? [];
                                });
                                $totalPrice = $orderGroup->sum('total_harga');
                            ?>
                            <?php echo $__env->make('user.pemesanan.partials.order-detail-modal', [
                                'orderId' => $orderId,
                                'order' => $order,
                                'allItems' => $allItems,
                                'totalPrice' => $totalPrice,
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('user.homepage.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo e(config('midtrans.client_key')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/user/pemesanan/riwayatPemesanan.blade.php ENDPATH**/ ?>