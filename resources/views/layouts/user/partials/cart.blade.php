{{-- layouts/user/partials/cart.blade.php --}}

<a href="" class="text-light text-decoration-none position-relative">
    <i class="bi bi-cart fs-4 "></i>
    @if (auth()->check() && auth()->user()->keranjangItems->count() > 0)
        <span id="cart-item-count"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger "
            style="display: {{ auth()->check() && auth()->user()->keranjangItems->count() > 0 ? 'inline-block' : 'none' }};">
            {{ auth()->check() ? auth()->user()->keranjangItems->count() : 0 }}
        </span>
    @endif
</a>
<div class="cart-popup">
    @if (auth()->check() && auth()->user()->keranjangItems->count() > 0)
        <ul>
            @php
                $totalHarga = 0;
            @endphp
            @foreach (auth()->user()->keranjangItems as $item)
                @php
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
                @endphp
                <li class="cart-item" data-id="{{ $item->id }}">
                    <div class="cart-item-img">
                        <img src="{{ asset('storage/' . $itemFoto) }}" alt="{{ $itemNama }}" class="img-fluid">
                    </div>
                    <div class="cart-item-details">
                        <p>{{ $itemNama }}</p>
                        <div class="input-group">
                            <button type="button" class="btn update-jumlah" data-item-id="{{ $item->id }}"
                                data-change="-10">-</button>
                            <input type="number" name="jumlah" id="jumlah-{{ $item->id }}" class="form-input"
                                value="{{ $item->jumlah }}" min="0" step="10" required readonly>
                            <button type="button" class="btn update-jumlah" data-item-id="{{ $item->id }}"
                                data-change="10">+</button>

                        </div>
                        <p id="total-price-{{ $item->id }}" data-harga="{{ $itemHarga }}">
                            Rp{{ number_format($itemHarga * $item->jumlah, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="btn btn-danger delete-item" data-item-id="{{ $item->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="cart-total d-flex justify-content-between align-items-center">
            <h5>Total Harga:</h5>
            <h5 id="total-harga">
                Rp{{ number_format($totalHarga, 0, ',', '.') }}
            </h5>
        </div>
        <div class="text-center mt-3">
            <form action=" " method="POST" class="d-inline">
                @csrf
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal">
                    Pesan Sekarang
                </button>

            </form>
        </div>
    @else
        <div class="empty-icon">
            <i class="bi bi-cart-x"></i>
        </div>
        <p class="empty">Keranjang Anda Kosong</p>
    @endif
</div>
@auth
    @include('layouts.user.partials.pesan-sekarang-modal')
@endauth
