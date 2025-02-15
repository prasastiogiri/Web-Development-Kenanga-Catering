
<div class="modal fade" id="detailModal{{ $orderId }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $orderId }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $orderId }}">Detail Pemesanan</h5>
                <button type="button" class="btn-close text-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Pesanan</h6>
                        <p><strong>Order ID:</strong> {{ $orderId }}</p>
                        <p><strong>Tanggal Pesan:</strong> {{ date('d M Y H:i', strtotime($order['created_at'])) }}</p>
                        <p><strong>Event Date:</strong> {{ $order['event_date'] ?? '-' }}</p>
                        <p><strong>Event Place:</strong> {{ $order['event_place'] ?? '-' }}</p>
                        <p><strong>Payment Method:</strong>
                            @php
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
                            @endphp
                            {{ $paymentMethods[$order['payment_method']] ?? $order['payment_method'] ?? '-' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Status</h6>
                        <p><strong>Status Pembayaran:</strong>
                            @include('user.pemesanan.partials.status-badge', [
                                'status' => $order['status'],
                            ])
                        </p>
                        <p><strong>Status Pemesanan:</strong> {{ $order['status_pemesanan'] ?? 'Belum selesai' }}</p>
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
                            @foreach ($allItems as $item)
                                <tr>
                                    <td>{{ optional($item['paket'])['nama'] ?? optional($item['produk'])['nama'] ?? '-' }}</td>
                                    <td>{{ $item['jumlah'] ?? 0 }}</td>
                                    <td>Rp {{ number_format($item['paket']['harga'] ?? $item['produk']['harga'] ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format(($item['jumlah'] * ($item['paket']['harga'] ?? $item['produk']['harga'] ?? 0)), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Harga:</th>
                                <th>Rp {{ number_format($totalPrice, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button> @if($order['status'] === 'paid')
                <a href="{{ route('pemesanan.print-pdf', ['orderId' => $orderId]) }}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print"></i> Download PDF
                </a>@endif
            </div>

        </div>
    </div>
</div>

