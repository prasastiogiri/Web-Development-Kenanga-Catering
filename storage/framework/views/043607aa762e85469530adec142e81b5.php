<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-4">
        <h2 class="mb-5"
            style="font-size: clamp(2rem, 4vw, 3.125rem);
    font-weight: 700;
    color: #9a7726;
    margin-bottom: 2rem;
    text-align: left;">
            Daftar Transaksi
        </h2>
        <div class="card shadow border-0 rounded-4">
            <div class="card-header py-3 bg-gradient d-flex justify-content-between align-items-center"
                style="background: linear-gradient(45deg, #926c15, #bd9542)">
                <h4 class="mb-0 text-white fw-semibold">Data Transaksi</h4>
                <span class="badge bg-light text-dark  px-4 py-2">
                    <i class="fas fa-calendar-alt me-1"></i>
                    <?php echo e(\Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y')); ?>

                </span>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="transaksiTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Order ID</th>
                                <th>Pengguna</th>
                                <th>Item</th>
                                <th>Total Harga</th>
                                <th>Tanggal Acara</th>
                                <th>Tempat Acara</th>
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $groupedTransactions = $transactions->groupBy('order_id');
                            ?>
                            <?php $__currentLoopData = $groupedTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $firstTransaction = $group->first();
                                    $items = $group
                                        ->map(function ($transaction) {
                                            return ($transaction->produk
                                                ? $transaction->produk->nama . ' (Produk)'
                                                : ($transaction->paket
                                                    ? $transaction->paket->nama . ' (Paket)'
                                                    : '-')) .
                                                ' x' .
                                                $transaction->jumlah;
                                        })
                                        ->implode('<br>');
                                ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><span class="badge bg-dark"><?php echo e($firstTransaction->order_id); ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle fa-lg me-2 text-secondary"></i>
                                            <?php echo e($firstTransaction->user->nama ?? '-'); ?>

                                        </div>
                                    </td>
                                    <td><?php echo $items; ?></td>
                                    <td>
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-money-bill-wave me-1"></i>
                                            Rp <?php echo e(number_format($group->sum('total_harga'), 0, ',', '.')); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($firstTransaction->event_date): ?>
                                            <span class="badge bg-info px-3 py-2">
                                                <i class="fas fa-calendar-day me-1"></i>
                                                <?php echo e(\Carbon\Carbon::parse($firstTransaction->event_date)->format('d M Y')); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary px-3 py-2 venue-badge"
                                            style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; cursor: pointer;"
                                            onclick="showMap('<?php echo e($firstTransaction->event_place); ?>')"
                                            data-location="<?php echo e($firstTransaction->event_place); ?>">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo e($firstTransaction->event_place ?? '-'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary px-3 py-2">
                                            <i class="fas fa-credit-card me-1"></i>
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
                                                    'shopeepay' => 'ShopeePay',
                                                ];
                                            ?>
                                            <?php echo e($paymentMethods[$firstTransaction['payment_method']] ?? ($firstTransaction['payment_method'] ?? '-')); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($firstTransaction->status_pemesanan == 'Selesai'): ?>
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Selesai
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                <i class="fas fa-clock me-1"></i>
                                                Belum Selesai
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <?php if($firstTransaction->status_pemesanan != 'Selesai'): ?>
                                                <button class="btn btn-sm btn-outline-success update-status"
                                                    data-order-id="<?php echo e($firstTransaction->order_id); ?>">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Proses
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small"> - </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mapModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lokasi Acara</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="map" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.min.css">
    <style>
        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            background-color: #926c15;
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .modal-body {
            padding: 1.5rem;
        }


        .card {
            transition: all 0.3s ease;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .badge {
            font-weight: 500;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 0.75rem;
        }

        .btn-outline-dark {
            border-width: 2px;
        }

        .btn-outline-dark:hover {
            background-color: #926c15;
            border-color: #926c15;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 1.5rem;
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #926c15;
            box-shadow: 0 0 0 0.25rem rgba(146, 108, 21, 0.25);
        }

        .badge {
            font-weight: 500;
            vertical-align: middle;
        }

        /* Add tooltip on hover for truncated text */
        [data-bs-toggle="tooltip"] {
            cursor: default;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip()
            // Initialize DataTable
            var table = $('#transaksiTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_  data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang ditampilkan",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: ">>",
                        previous: "<<"
                    }
                }
            });

            // Status update handler with SweetAlert
            $('.update-status').on('click', function() {
                const button = $(this);
                const orderId = button.data('order-id');

                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin memperbarui status transaksi ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#198754",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Update!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.prop('disabled', true);
                        const originalHtml = button.html();
                        button.html('<i class="fas fa-spinner fa-spin"></i>');

                        $.ajax({
                            url: '<?php echo e(route('transaksi.update-status')); ?>',
                            type: 'POST',
                            data: {
                                order_id: orderId,
                                _token: '<?php echo e(csrf_token()); ?>'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: response.message,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                button.prop('disabled', false);
                                button.html(originalHtml);
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Terjadi kesalahan saat memperbarui status",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
        });

        let map;

        async function showMap(location) {
            if (!location || location === '-') return;

            const modal = new bootstrap.Modal(document.getElementById('mapModal'));
            modal.show();

            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`);
                const data = await response.json();

                if (data.length > 0) {
                    const {
                        lat,
                        lon
                    } = data[0];

                    if (!map) {
                        map = L.map('map');
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                    }

                    map.setView([lat, lon], 15);
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(location)
                        .openPopup();
                }
            } catch (error) {
                console.error('Error loading map:', error);
            }
        }

        document.getElementById('mapModal').addEventListener('shown.bs.modal', function() {
            map && map.invalidateSize();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/admin/transaksi/index.blade.php ENDPATH**/ ?>