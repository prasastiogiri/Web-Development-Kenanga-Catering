@extends('layouts.user.navbar')

<link rel="stylesheet" href="{{ asset('css/paket.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

@section('content')
    <div class="container">
        <div class="sub-title">
            <h1>Paket</h1>
        </div>

        <div class="row justify-content-start">
            @foreach ($pakets as $paket)
                <div class="col-12 col-md-4 mb-4">
                    <div class="package-card">
                        <img src="{{ asset('storage/' . $paket->foto) }}" class="card-img-top" alt="{{ $paket->nama }}">
                        <div class="card-body">
                            <p class="card-title" data-bs-toggle="tooltip" title="{{ $paket->nama }}">
                                {{ Str::limit($paket->nama, 50, '...') }}
                            </p>
                            <!-- Harga Paket -->
                            <p class="card-price">
                                Rp{{ number_format($paket->harga, 0, ',', '.') }}
                            </p>
                            <p class="card-text" data-bs-toggle="tooltip" title="{{ $paket->deskripsi }}">
                                {{ Str::limit($paket->deskripsi, 150, '...') }}
                            </p>

                            <!-- Tombol Pesan Sekarang dan Tambah ke Keranjang -->
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-primary flex-grow-1 me-2 pesan-sekarang" data-bs-toggle="modal"
                                    data-bs-target="#cartModal" data-login="{{ Auth::check() ? 'true' : 'false' }}"
                                    data-login-url="{{ route('user.login') }}" data-id="{{ $paket->id }}"
                                    data-nama="{{ $paket->nama }}" data-harga="{{ $paket->harga }}"
                                    data-foto="{{ asset('storage/' . $paket->foto) }}"
                                    data-deskripsi="{{ $paket->deskripsi }}">
                                    <i class="bi bi-cart"></i> Masukkan Ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @include('user.homepage.footer')

    <!-- Modal Keranjang -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-card">
                <form action="{{ route('keranjang.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="paket_id" id="modalPaketId">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <!-- Gambar Paket -->
                                <div class="col-md-6">
                                    <img id="modalPaketFoto" src="" alt="Foto Paket" class="img-fluid">
                                </div>
                                <!-- Detail Paket -->
                                <div class="col-md-6">
                                    <p id="modalPaketNama" class="card-title"></p>
                                    <p id="modalPaketDeskripsi" class="card-text"></p>
                                    <p id="modalPaketHarga" class="card-price"></p>

                                    <!-- Input Jumlah Porsi -->
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

                                    <!-- Tombol Masukkan ke Keranjang -->
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
        const cartModal = document.getElementById('cartModal');

        // Event listener untuk mempersiapkan data pada modal
        cartModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const paketId = button.getAttribute('data-id');
            const paketNama = button.getAttribute('data-nama');
            const paketHarga = button.getAttribute('data-harga');
            const paketFoto = button.getAttribute('data-foto');
            const paketDeskripsi = button.getAttribute('data-deskripsi');

            // Update modal content
            document.getElementById('modalPaketId').value = paketId;
            document.getElementById('modalPaketNama').textContent = paketNama;
            document.getElementById('modalPaketHarga').textContent =
                `Rp${parseInt(paketHarga).toLocaleString('id-ID')}`;
            document.getElementById('modalPaketFoto').src = paketFoto;
            document.getElementById('modalPaketDeskripsi').textContent = paketDeskripsi;
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
                if (currentValue + step <= max) {
                    modalJumlah.value = currentValue + step;
                }
            });

            decrementButton.addEventListener("click", function() {
                const step = parseInt(modalJumlah.step) || 1;
                const min = parseInt(modalJumlah.min) || 100;
                const currentValue = parseInt(modalJumlah.value) || 0;

                if ((currentValue - step) < min) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal pemesanan adalah 100 porsi',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (currentValue - step >= min) {
                    modalJumlah.value = currentValue - step;
                }
            });

            // Add input event listener to prevent manual entry below minimum
            modalJumlah.addEventListener("input", function() {
                const min = parseInt(this.min) || 100;
                const currentValue = parseInt(this.value) || 0;

                if (currentValue < min) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal pemesanan adalah 100 porsi',
                        confirmButtonText: 'OK'
                    });
                    this.value = min;
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
@endsection
