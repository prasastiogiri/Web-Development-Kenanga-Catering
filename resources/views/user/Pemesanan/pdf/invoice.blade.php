<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }} - Invoice {{ $orderId }}</title>
    <link rel="icon" href="{{ asset('images/transparent.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');

        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #926c15;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header-left img {
            max-width: 150px;
        }

        .header-right {
            text-align: right;
        }

        .invoice-title {
            color: #926c15;
            margin: 0;
            font-size: 24px;
        }

        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .order-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 3px rgba(0,0,0,0.05);
        }

        table thead {
            background-color: #926c15;
            color: white;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .total-row {
            font-weight: bold;
            background-color: #e9ecef !important;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 12px;
        }

        @media print {
            body {
                background-color: white;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="header-left">
                <img src="{{ $logoBase64 }}" alt="Logo" />
            </div>
            <div class="header-right">
                <h1 class="invoice-title">INVOICE</h1>
                <p>Order ID: {{ $orderId }}</p>
                <p class="badge py-2 bg-success">Status: {{ strtoupper($orderItems->first()->status) }}</p>
            </div>
        </div>
        <div class="order-info">
            @php
                $firstOrder = $orderItems->first();
            @endphp
            <div>
                <p><strong>Nama Pelanggan:</strong> {{ Auth::user()->nama }}</p>
                <p><strong>Tanggal Pesanan:</strong> {{ $firstOrder->created_at->format('d M Y ') }}</p>
            </div>
            <div>
                <p><strong>Tanggal Event:</strong> {{ $firstOrder->event_date ? \Carbon\Carbon::parse($firstOrder->event_date)->format('d M Y') : '-' }}</p>
                <p><strong>Lokasi Event:</strong> {{ $firstOrder->event_place ?? '-' }}</p>
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
                    {{ $paymentMethods[$firstOrder['payment_method']] ?? $firstOrder['payment_method'] ?? '-' }}
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAmount = 0;
                @endphp
                @foreach ($orderItems as $item)
                    @php
                        $itemName = $item->paket?->nama ?? ($item->produk?->nama ?? 'Unknown Item');
                        $itemPrice = $item->paket?->harga ?? ($item->produk?->harga ?? 0);
                        $subtotal = $item->jumlah * $itemPrice;
                        $totalAmount += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $itemName }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp {{ number_format($itemPrice, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="3" style="text-align:right;">Total Harga:</th>
                    <th>Rp {{ number_format($totalAmount, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda</p>
            <p>{{ config('app.name') }} - Invoice Generated on {{ now()->timezone('GMT+7')->format('d M Y H:i') }}</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
