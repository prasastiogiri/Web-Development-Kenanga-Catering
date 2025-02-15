<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-4">

        <h2 class="mb-5"
            style="font-size: clamp(2rem, 4vw, 3.125rem);
        font-weight: 700;
        color: #9a7726;
        margin-bottom: 2rem;
        text-align: left;">
            Daftar Produk
        </h2>

        <div class="card shadow border-0 rounded-4">
            <div class="card-header py-3 bg-gradient d-flex justify-content-between align-items-center"
                style="background: linear-gradient(45deg, #926c15, #bd9542)">
                <h4 class="mb-0 text-white fw-semibold">Data Produk</h4>
                <button type="button" class="btn btn-outline-primary px-4 py-2 rounded" data-bs-toggle="modal"
                    data-bs-target="#createProdukModal">
                    <i class="fas fa-plus me-1"></i> Tambah Produk
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="produkTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Produk</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Foto</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $produk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($loop->iteration); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-box fa-lg me-2 text-secondary"></i>
                                            <?php echo e($produk->nama); ?>

                                        </div>
                                    </td>
                                    <td><?php echo e($produk->deskripsi); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-tag me-1"></i>
                                            Rp <?php echo e(number_format($produk->harga, 0, ',', '.')); ?>

                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if($produk->foto): ?>
                                            <img src="<?php echo e(asset('storage/' . $produk->foto)); ?>" alt="<?php echo e($produk->nama); ?>"
                                                width="50" height="50" class="rounded-circle cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#imageModal-<?php echo e($produk->id); ?>">
                                        <?php else: ?>
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="fas fa-image me-1"></i>
                                                No Image
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editProdukModal-<?php echo e($produk->id); ?>">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>
                                            <form action="<?php echo e(route('admin-produk.destroy', $produk->id)); ?>" method="POST"
                                                class="form-hapus d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Image Modal -->
                                <div class="modal fade" id="imageModal-<?php echo e($produk->id); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><?php echo e($produk->nama); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="<?php echo e(asset('storage/' . $produk->foto)); ?>"
                                                    alt="<?php echo e($produk->nama); ?>" class="img-fluid rounded">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editProdukModal-<?php echo e($produk->id); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Produk</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?php echo e(route('admin-produk.update', $produk->id)); ?>"
                                                    method="POST" enctype="multipart/form-data">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PUT'); ?>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Produk</label>
                                                        <input type="text" class="form-control" name="nama"
                                                            value="<?php echo e($produk->nama); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea class="form-control" name="deskripsi" rows="3" required><?php echo e($produk->deskripsi); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Harga</label>
                                                        <input type="number" class="form-control" name="harga"
                                                            value="<?php echo e($produk->harga); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Foto Produk</label>
                                                        <input type="file" class="form-control" name="foto">
                                                        <?php if($produk->foto): ?>
                                                            <div class="mt-2">
                                                                <small class="text-muted">Current Image:</small>
                                                                <img src="<?php echo e(asset('storage/' . $produk->foto)); ?>"
                                                                    alt="Current" class="d-block mt-2" width="100">
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-save me-1"></i> Simpan
                                                        </button>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i> Batal
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createProdukModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo e(route('admin-produk.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" name="harga" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Produk</label>
                            <input type="file" class="form-control" name="foto">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.min.css">
    <style>
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

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#produkTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
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

            // SweetAlert for delete confirmation
            $('.form-hapus').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menghapus produk ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Display success messages with SweetAlert
            <?php if(session('success')): ?>
                Swal.fire({
                    title: "Berhasil!",
                    text: "<?php echo e(session('success')); ?>",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
            <?php endif; ?>
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/admin/produk/index.blade.php ENDPATH**/ ?>