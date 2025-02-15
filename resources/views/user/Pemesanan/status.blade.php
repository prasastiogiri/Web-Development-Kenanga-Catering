@extends('layouts.user.navbar')

<link rel="stylesheet" href="{{ asset('css/pembayaran.css') }}">

@section('content')
    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card border-0 shadow-lg">
                    <!-- Order Header -->
                    <div class="card-header text-white p-4 border-0">
                        <div class="d-flex align-items-center">
                            <div>
                                <h4 class="mb-1">Order #{{ $orderId }}</h4>
                                <p class="mb-0 opacity-75">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($orderItems->first()->created_at)->format('d F Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @if ($orderItems->isNotEmpty())
                            <!-- Status Badge -->
                            <div class="mb-4">
                                <div
                                    class="alert alert-{{ getStatusColor($orderItems->first()->status) }} d-flex align-items-center border-0 shadow-sm">
                                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                    <div>
                                        <span class="text-muted">Status Pembayaran:</span>
                                        <strong class="ms-2">{{ ucfirst($orderItems->first()->status) }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Details Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        <i class="bi bi-info-square me-2"></i>Detail Pesanan
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="detail-item">
                                                <span class="text-muted d-block mb-1">Tanggal Acara</span>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-calendar-event me-2 text-primary"></i>
                                                    <strong>{{ \Carbon\Carbon::parse($orderItems->first()->event_date)->format('d F Y') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="detail-item">
                                                <span class="text-muted d-block mb-1">Tempat Acara</span>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-geo-alt me-2 text-primary"></i>
                                                    <strong>{{ $orderItems->first()->event_place }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="detail-item">
                                                <span class="text-muted d-block mb-1">Metode Pembayaran</span>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-credit-card me-2 text-primary"></i>
                                                    <strong>{{ formatPaymentMethod($orderItems->first()->payment_method) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ordered Items Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        <i class="bi bi-bag-check me-2"></i>Item yang Dipesan
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th scope="col">Item</th>
                                                    <th scope="col" class="text-center">Jumlah</th>
                                                    <th scope="col" class="text-end">Harga</th>
                                                    <th scope="col" class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalOrder = 0; @endphp
                                                @foreach ($orderItems as $pemesanan)
                                                    <tr>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-box me-2 text-primary"></i>
                                                                {{ $pemesanan->paket ? $pemesanan->paket->nama : $pemesanan->produk->nama }}
                                                            </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <span class="badge bg-light text-dark">
                                                                {{ $pemesanan->jumlah }} Porsi
                                                            </span>
                                                        </td>
                                                        <td class="text-end align-middle">
                                                            Rp{{ number_format($pemesanan->total_harga / $pemesanan->jumlah, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-end align-middle">
                                                            <strong>Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                    @php $totalOrder += $pemesanan->total_harga; @endphp
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <td colspan="3" class="text-end">
                                                        <strong>Total Pesanan:</strong>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong class="text-primary fs-5">
                                                            Rp{{ number_format($totalOrder, 0, ',', '.') }}
                                                        </strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="text-center">
                                @if ($orderItems->first()->status === 'pending')
                                    <button class="btn btn-primary" id="pay-button">
                                        <i class="bi bi-wallet2 me-2"></i>Lanjutkan Pembayaran
                                    </button>
                                @endif
                                <a href="{{ route('homepage') }}" class="btn btn-secondary">
                                    <i class="bi bi-house me-2"></i>Kembali ke Beranda
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>Pesanan tidak ditemukan.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.addEventListener('click', function() {
                    window.snap.pay('{{ $orderItems->first()->snap_token }}', {
                        onSuccess: function(result) {
                            Swal.fire({
                                title: 'Sukses',
                                text: 'Pembayaran berhasil!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = '/riwayat-pemesanan';
                            });
                        },
                        onPending: function(result) {
                            Swal.fire({
                                title: 'Pending',
                                text: 'Pembayaran sedang diproses',
                                icon: 'info'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        onError: function(result) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Pembayaran gagal',
                                icon: 'error'
                            });
                        },
                        onClose: function() {
                            Swal.fire({
                                title: 'Perhatian',
                                text: 'Anda menutup popup pembayaran sebelum menyelesaikan transaksi',
                                icon: 'warning'
                            }).then(() => {
                                // Refresh halaman atau redirect ke status pembayaran
                                window.location.reload();
                            });
                        }
                    });
                });
            }
        });
    </script>
@endsection
