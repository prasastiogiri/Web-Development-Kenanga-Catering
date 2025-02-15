@section('content')
    <div class="container mt-5">
        <div class="sub-title">
            <h1>Keranjang Saya</h1>
        </div>
        @if ($keranjangItems->count() > 0)
            <div class="table-responsive rounded shadow-sm p-4 bg-white">
                <table id="keranjang-table" class="table table-striped dt-responsive nowrap" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($keranjangItems as $item)
                            @php
                                $itemHarga = $item->paket
                                    ? $item->paket->harga
                                    : ($item->produk
                                        ? $item->produk->harga
                                        : 0);
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
                            @endphp
                            <tr class="cart-item" data-id="{{ $item->id }}">
                                <td><img src="{{ asset('storage/' . $itemFoto) }}" alt="{{ $itemNama }}"
                                        class="img-fluid rounded" style="width: 80px;"></td>
                                <td class="align-middle">{{ $itemNama }}</td>
                                <td class="align-middle">Rp{{ number_format($itemHarga, 0, ',', '.') }}</td>
                                <td class="align-middle">
                                    <div class="input-group">
                                        <button type="button" class="btn"
                                            onclick="updateJumlah({{ $item->id }}, -10)">-</button>
                                        <input type="number" id="keranjang-jumlah-{{ $item->id }}" class="form-input"
                                            value="{{ $item->jumlah }}" readonly>
                                        <button type="button" class="btn"
                                            onclick="updateJumlah({{ $item->id }}, 10)">+</button>
                                    </div>
                                </td>
                                <td id="keranjang-total-price-{{ $item->id }}" data-harga="{{ $itemHarga }}"
                                    class="align-middle">
                                    Rp{{ number_format($itemHarga * $item->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="align-middle">
                                    <button class="btn btn-sm btn-danger" onclick="hapusItem({{ $item->id }})">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 text-end">
                    <h3>Total Harga: <span id="keranjang-total-harga">
                            Rp{{ number_format($keranjangItems->sum(fn($item) => $item->jumlah * ($item->paket ? $item->paket->harga : $item->produk->harga)), 0, ',', '.') }}
                        </span></h3>
                    <form action="{{ route('pemesanan.checkout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn- mt-3 px-5">Pesan Sekarang</button>
                    </form>
                </div>
            </div>
        @else
            <div class="empty-cart-container text-center">
                <div class="empty-cart-icon mb-3">
                    <i class="bi bi-cart-x text-secondary"></i>
                </div>
                <h3 class="text-dark">Keranjang Anda Kosong</h3>
                <p class="text-muted">Silakan tambahkan beberapa item ke keranjang Anda</p>
                <a href="{{ route('produkUser.index') }}" class="btn btn-primary mt-3 px-5">Mulai Belanja</a>
            </div>
        @endif
    </div>
    <script>
        function updateJumlah(itemId, change) {
            const jumlahInput = document.getElementById(`keranjang-jumlah-${itemId}`);
            const totalPriceElement = document.getElementById(`keranjang-total-price-${itemId}`);
            const currentJumlah = parseInt(jumlahInput.value);
            const newJumlah = currentJumlah + change;

            if (newJumlah <= 0) {
                Swal.fire({
                    title: 'Jumlah tidak valid',
                    text: 'Jumlah tidak boleh kurang dari 1.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                });
                return;
            }

            fetch(`/keranjang/update-jumlah/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        jumlah: newJumlah
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        jumlahInput.value = newJumlah;
                        totalPriceElement.innerText = `Rp${data.totalHarga.toLocaleString('id-ID')}`;
                        updateTotalHarga();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        }

        function updateTotalHarga() {
            let totalHarga = 0;
            document.querySelectorAll('.cart-item').forEach((item) => {
                const jumlah = parseInt(item.querySelector('input').value);
                const harga = parseFloat(item.querySelector('[data-harga]').getAttribute('data-harga'));
                totalHarga += jumlah * harga;
            });
            document.getElementById('keranjang-total-harga').innerText = `Rp${totalHarga.toLocaleString('id-ID')}`;
        }
    </script>
@endsection
