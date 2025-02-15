@extends('layouts.user.navbar')

<link rel="stylesheet" href="{{ asset('css/riwayatPemesanan.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

@section('content')
    <div class="container">
        <div class="sub-title">
            <h1>Riwayat Pemesanan</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-body shadow-lg">
                    @if ($riwayat->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x"></i>
                            <h5>Belum ada pesanan</h5>
                            <p class="text-muted">Anda belum melakukan pemesanan apapun.</p>
                            <a href="{{ route('homepage') }}" class="btn btn-primary">
                                Mulai Belanja
                            </a>
                        </div>
                    @else
                        @php
                            $groupedOrders = $riwayat->groupBy('order_id');
                        @endphp
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
                                    @foreach ($groupedOrders as $orderId => $orderGroup)
                                        @php
                                            $order = $orderGroup->first();
                                            $allItems = $orderGroup->flatMap(function ($item) {
                                                return $item['items'] ?? [];
                                            });
                                            $totalPrice = $orderGroup->sum('total_harga');
                                        @endphp
                                        <tr class="text-justify">
                                            <td>{{ $orderId }}</td>
                                            <td>{{ date('d M Y H:i', strtotime($order['created_at'])) }}</td>
                                            <td class="order-items-cell">
                                                @foreach ($allItems as $item)
                                                    <div class="mb-2">
                                                        <strong>{{ optional($item['paket'])['nama'] ?? (optional($item['produk'])['nama'] ?? 'N/A') }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            Jumlah: {{ $item['jumlah'] ?? 0 }}
                                                        </small>
                                                    </div>
                                                    @if (!$loop->last)
                                                        <hr class="my-1">
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td class="text-center">
                                                <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                                            </td>
                                            <td class="text-center">
                                                @include('user.pemesanan.partials.status-badge', [
                                                    'status' => $order['status'],
                                                ])
                                            </td>
                                            <td class="text-center">
                                                {{ $order['status_pemesanan'] ?? 'Belum selesai' }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detailModal{{ $orderId }}">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </button>
                                                    @if ($order['status'] !== 'paid')
                                                        <a href="{{ route('pemesanan.status', ['orderId' => $orderId]) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fas fa-credit-card"></i> Bayar
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @foreach ($groupedOrders as $orderId => $orderGroup)
                            @php
                                $order = $orderGroup->first();
                                $allItems = $orderGroup->flatMap(function ($item) {
                                    return $item['items'] ?? [];
                                });
                                $totalPrice = $orderGroup->sum('total_harga');
                            @endphp
                            @include('user.pemesanan.partials.order-detail-modal', [
                                'orderId' => $orderId,
                                'order' => $order,
                                'allItems' => $allItems,
                                'totalPrice' => $totalPrice,
                            ])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('user.homepage.footer')

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endsection
