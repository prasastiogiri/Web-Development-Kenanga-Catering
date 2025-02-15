
<div class="modal fade" id="detailModal<?php echo e($orderId); ?>" tabindex="-1" aria-labelledby="detailModalLabel<?php echo e($orderId); ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel<?php echo e($orderId); ?>">Detail Pemesanan</h5>
                <button type="button" class="btn-close text-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Pesanan</h6>
                        <p><strong>Order ID:</strong> <?php echo e($orderId); ?></p>
                        <p><strong>Tanggal Pesan:</strong> <?php echo e(date('d M Y H:i', strtotime($order['created_at']))); ?></p>
                        <p><strong>Event Date:</strong> <?php echo e($order['event_date'] ?? '-'); ?></p>
                        <p><strong>Event Place:</strong> <?php echo e($order['event_place'] ?? '-'); ?></p>
                        <p><strong>Payment Method:</strong>
                            <?php
                            $paymentMethods = [
                                'bca_va' => 'BCA Virtual Account',
                                'mandiri_va' => 'Mandiri Virtual Account',
                                'bni_va' => 'BNI Virtual Account',
                                'bri_va' => 'BRI Virtual Account',
                                'permata_va' => 'Permata Virtual Account',
                                'gopay' => 'GoPay',
                                'dana' => 'DANA',
                                'ovo' => 'OVO',
                                'shopeepay' => 'ShopeePay'
                            ];
                            ?>
                            <?php echo e($paymentMethods[$order['payment_method']] ?? $order['payment_method'] ?? '-'); ?>

                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Status</h6>
                        <p><strong>Status Pembayaran:</strong>
                            <?php echo $__env->make('user.pemesanan.partials.status-badge', [
                                'status' => $order['status'],
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </p>
                        <p><strong>Status Pemesanan:</strong> <?php echo e($order['status_pemesanan'] ?? 'Belum selesai'); ?></p>
                    </div>
                </div>

                <hr>

                <h6>Detail Barang</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $allItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e(optional($item['paket'])['nama'] ?? optional($item['produk'])['nama'] ?? '-'); ?></td>
                                    <td><?php echo e($item['jumlah'] ?? 0); ?></td>
                                    <td>Rp <?php echo e(number_format($item['paket']['harga'] ?? $item['produk']['harga'] ?? 0, 0, ',', '.')); ?></td>
                                    <td>Rp <?php echo e(number_format(($item['jumlah'] * ($item['paket']['harga'] ?? $item['produk']['harga'] ?? 0)), 0, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Harga:</th>
                                <th>Rp <?php echo e(number_format($totalPrice, 0, ',', '.')); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button> <?php if($order['status'] === 'paid'): ?>
                <a href="<?php echo e(route('pemesanan.print-pdf', ['orderId' => $orderId])); ?>" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print"></i> Download PDF
                </a><?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/user/pemesanan/partials/order-detail-modal.blade.php ENDPATH**/ ?>