<link rel="stylesheet" href="<?php echo e(asset('css/produk.css')); ?>">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="sub-title">
            <h1>Produk</h1>
        </div>

        <div class="row justify-content-start">
            <?php $__currentLoopData = $produk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="package-card">
                        <img src="<?php echo e(asset('storage/' . $item->foto)); ?>" class="card-img-top" alt="<?php echo e($item->nama); ?>">
                        <div class="card-body d-flex justify-content-between flex-column">
                            <p class="card-title" data-bs-toggle="tooltip" title="<?php echo e($item->nama); ?>">
                                <?php echo e(Str::limit($item->nama, 50, '...')); ?>

                            </p>
                            <p class="card-price">
                                Rp<?php echo e(number_format($item->harga, 0, ',', '.')); ?>

                            </p>
                            <p class="card-text" data-bs-toggle="tooltip" title="<?php echo e($item->deskripsi); ?>">
                                <?php echo e(Str::limit($item->deskripsi, 150, '...')); ?>

                            </p>

                            <div class="d-flex">

                                <button type="button"  class="btn btn-primary flex-grow-1 me-2 pesan-sekarang" data-bs-toggle="modal"
                                data-login="<?php echo e(Auth::check() ? 'true' : 'false'); ?>"
                                data-login-url="<?php echo e(route('user.login')); ?>"
                                    data-bs-target="#cartModal" data-id="<?php echo e($item->id); ?>"
                                    data-nama="<?php echo e($item->nama); ?>" data-harga="<?php echo e($item->harga); ?>"
                                    data-foto="<?php echo e(asset('storage/' . $item->foto)); ?>"
                                    data-deskripsi="<?php echo e($item->deskripsi); ?>">
                                    <i class="bi bi-cart"></i> Masukkan Ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php echo $__env->make('user.homepage.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Modal Keranjang -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-card">
                <form action="<?php echo e(route('keranjang.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="produk_id" id="modalProdukId">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <img id="modalProdukFoto" src="" alt="Foto Produk" class="img-fluid">
                                </div>
                                <div class="col-md-6">
                                    <p id="modalProdukNama" class="card-title"></p>
                                    <p id="modalProdukDeskripsi" class="card-text"></p>
                                    <p id="modalProdukHarga" class="card-price"></p>

                                    <div class="mb-3">
                                        <label for="modalJumlah" class="form-label">Jumlah Porsi</label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="decrement">-</button>
                                            <input type="number" name="jumlah" id="modalJumlah"
                                                class="form-control text-center" value="100" min="100"
                                                step="10" required readonly>
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="increment">+</button>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">+ Keranjang</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        cartModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const produkId = button.getAttribute('data-id');
            const produkNama = button.getAttribute('data-nama');
            const produkHarga = button.getAttribute('data-harga');
            const produkFoto = button.getAttribute('data-foto');
            const produkDeskripsi = button.getAttribute('data-deskripsi');
            const modalJumlah = document.getElementById('modalJumlah');

            // Daftar produk khusus
            const specialProducts = ["Kambing Guling", "Sate Kambing 500 Tusuk + Gulai Kambing 1 Panci"];
            const isSpecialProduct = specialProducts.includes(produkNama);

            // Sesuaikan input berdasarkan jenis produk
            if (isSpecialProduct) {
                modalJumlah.min = 1;
                modalJumlah.max = 5;
                modalJumlah.step = 1;
                modalJumlah.value = 1;
            } else {
                modalJumlah.min = 100;
                modalJumlah.step = 10;
                modalJumlah.value = 100;
                modalJumlah.removeAttribute('max');
            }

            // Update konten modal
            document.getElementById('modalProdukId').value = produkId;
            document.getElementById('modalProdukNama').textContent = produkNama;
            document.getElementById('modalProdukHarga').textContent =
                `Rp${parseInt(produkHarga).toLocaleString('id-ID')}`;
            document.getElementById('modalProdukFoto').src = produkFoto;
            document.getElementById('modalProdukDeskripsi').textContent = produkDeskripsi;
        });



        // Fungsi untuk memperbarui badge di ikon keranjang
        function updateCartBadge(itemCount) {
            const cartItemCount = document.getElementById('cart-item-count');
            if (cartItemCount) {
                cartItemCount.innerText = itemCount;
                cartItemCount.style.display = itemCount > 0 ? 'inline-block' : 'none';
            }
        }

        // Panggil fungsi pembaruan ikon keranjang segera setelah halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/keranjang/item-count')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartBadge(data.itemCount);
                    } else {
                        console.error('Failed to fetch cart item count');
                    }
                })
                .catch(error => console.error('Error fetching cart item count:', error));
        });
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');

            searchInput.addEventListener('input', function() {
                clearTimeout(this.searchTimer); // Reset timer jika ada input baru
                this.searchTimer = setTimeout(() => {
                    searchForm.submit(); // Kirim form setelah user selesai mengetik
                }, 1); // Tunggu 500ms sebelum mengirim
            });
        });

       document.addEventListener("DOMContentLoaded", function() {
        const modalJumlah = document.getElementById("modalJumlah");
        const incrementButton = document.getElementById("increment");
        const decrementButton = document.getElementById("decrement");

        incrementButton.addEventListener("click", function() {
            const step = parseInt(modalJumlah.step) || 1;
            const max = parseInt(modalJumlah.max) || Infinity;
            const currentValue = parseInt(modalJumlah.value) || 0;
            const newValue = currentValue + step;

            if (newValue > max) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: `Maaf, Maksimal pemesanan adalah ${max} porsi`,
                    confirmButtonText: 'OK'
                });
                return;
            }

            modalJumlah.value = newValue;
        });

        decrementButton.addEventListener("click", function() {
            const step = parseInt(modalJumlah.step) || 1;
            const min = parseInt(modalJumlah.min) || 100;
            const currentValue = parseInt(modalJumlah.value) || 0;
            const newValue = currentValue - step;

            if (newValue < min) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: `Maaf, Minimal pemesanan adalah ${min} porsi`,
                    confirmButtonText: 'OK'
                });
                return;
            }

            modalJumlah.value = newValue;
        });

        modalJumlah.addEventListener("input", function() {
            const min = parseInt(this.min) || 100;
            const max = parseInt(this.max) || Infinity;
            const currentValue = parseInt(this.value) || 0;

            if (currentValue < min) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: `Minimal pemesanan adalah ${min} porsi`,
                    confirmButtonText: 'OK'
                });
                this.value = min;
            } else if (currentValue > max) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: `Maksimal pemesanan adalah ${max} porsi`,
                    confirmButtonText: 'OK'
                });
                this.value = max;
            }
        });
    });

        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".pesan-sekarang");

            buttons.forEach(button => {
                button.addEventListener("click", function(e) {
                    const isLoggedIn = this.getAttribute("data-login") === "true";
                    const loginUrl = this.getAttribute("data-login-url");

                    if (!isLoggedIn) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Anda harus login',
                            text: 'Harap login terlebih dahulu untuk melakukan pemesanan.',
                            confirmButtonText: 'Login'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = loginUrl;
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/user/produkUser/index.blade.php ENDPATH**/ ?>