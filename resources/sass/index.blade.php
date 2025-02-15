@extends('layouts.user.navbar')

<link rel="stylesheet" href="{{ asset('css/produk.css') }}">

@section('content')
    <div class="container">
        <div class="sub-title">
            <h1>Produk</h1>
        </div>

        <form method="GET" action="{{ url('produk') }}" class="d-flex mb-3">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari produk berdasarkan nama..."
                value="{{ request('search') }}" style="z-index: 1;">
            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>

        <!-- Dropdown untuk sorting -->
        <form method="GET" action="{{ url('produk') }}" class="mb-4 justify-content-end">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama A-Z</option>
                <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama Z-A</option>
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                <option value="harga_asc" {{ request('sort') == 'harga_asc' ? 'selected' : '' }}>Harga Terendah Ke Tertinggi
                </option>
                <option value="harga_desc" {{ request('sort') == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi Ke
                    Terendah</option>
            </select>
        </form>

        <div class="row justify-content-start">
            @foreach ($produk as $item)
                <div class="col-12 col-md-4 mb-4">
                    <div class="package-card">
                        <img src="{{ asset('storage/' . $item->foto) }}" class="card-img-top" alt="{{ $item->nama }}">
                        <div class="card-body">
                            <p class="card-title" data-bs-toggle="tooltip" title="{{ $item->nama }}">
                                {{ Str::limit($item->nama, 50, '...') }}
                            </p>
                            <!-- Harga Produk -->
                            <p class="card-price">
                                Rp{{ number_format($item->harga, 0, ',', '.') }}
                            </p>
                            <p class="card-text" data-bs-toggle="tooltip" title="{{ $item->deskripsi }}">
                                {{ Str::limit($item->deskripsi, 150, '...') }}
                            </p>

                            <!-- Tombol Pesan Sekarang dan Tambah ke Keranjang -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('pemesanan.index', ['produk_id' => $item->id]) }}"
                                    class="btn btn-primary flex-grow-1 me-2" style="z-index: 1;">Pesan Sekarang</a>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#cartModal" data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama }}" data-harga="{{ $item->harga }}"
                                    data-foto="{{ asset('storage/' . $item->foto) }}"
                                    data-deskripsi="{{ $item->deskripsi }}">
                                    <i class="bi bi-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modal Keranjang -->
        <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-card">
                    <form action="{{ url('keranjang') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produk_id" id="modalProdukId">
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <!-- Gambar Produk -->
                                    <div class="col-md-6">
                                        <img id="modalProdukFoto" src="" alt="Foto Produk" class="img-fluid">
                                    </div>
                                    <!-- Detail Produk -->
                                    <div class="col-md-6">
                                        <p id="modalProdukNama" class="card-title"></p>
                                        <p id="modalProdukDeskripsi" class="card-text"></p>
                                        <p id="modalProdukHarga" class="card-price"></p>

                                        <!-- Input Jumlah -->
                                        <div class="mb-3">
                                            <label for="modalQuantity" class="form-label">Jumlah</label>
                                            <input type="number" name="quantity" id="modalQuantity" class="form-control"
                                                value="100" min="100" step="10" required>
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
            cartModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const produkId = button.getAttribute('data-id');
                const produkNama = button.getAttribute('data-nama');
                const produkHarga = button.getAttribute('data-harga');
                const produkFoto = button.getAttribute('data-foto');
                const produkDeskripsi = button.getAttribute('data-deskripsi');

                // Update modal content
                document.getElementById('modalProdukId').value = produkId;
                document.getElementById('modalProdukNama').textContent = produkNama;
                document.getElementById('modalProdukHarga').textContent =
                    `Rp${parseInt(produkHarga).toLocaleString('id-ID')}`;
                document.getElementById('modalProdukFoto').src = produkFoto;
                document.getElementById('modalProdukDeskripsi').textContent = produkDeskripsi;
            });
        </script>
    @endsection
