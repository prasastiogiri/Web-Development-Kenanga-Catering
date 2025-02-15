<?php

namespace App\Http\Controllers\User;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Paket;
use App\Models\Produk;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PemesananController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function index(Request $request)
    {
        $paketId = $request->input('paket_id');
        $produkId = $request->input('produk_id');

        $pemesanansQuery = Pemesanan::where('user_id', Auth::id());

        if ($paketId) {
            $pemesanansQuery->where('paket_id', $paketId);
        }

        if ($produkId) {
            $pemesanansQuery->where('produk_id', $produkId);
        }

        // Group pemesanans by order_id
        $orders = $pemesanansQuery->with(['paket', 'produk'])
            ->get()
            ->groupBy('order_id')
            ->map(function ($orderItems) {
                $firstItem = $orderItems->first();
                return [
                    'order_id' => $firstItem->order_id,
                    'created_at' => $firstItem->created_at,
                    'status' => $firstItem->status,
                    'total_harga' => $orderItems->sum('total_harga'),
                    'items' => $orderItems
                ];
            });

        $paket = $paketId ? Paket::find($paketId) : null;
        $produk = $produkId ? Produk::find($produkId) : null;

        if (!$paket && !$produk) {
            return redirect()->route('pemesanan.index')->with('error', 'Paket atau Produk tidak ditemukan.');
        }

        return view('user.pemesanan.index', [
            'orders' => $orders,
            'paket' => $paket,
            'produk' => $produk,
            'pageTitle' => "Form Pemesanan",
        ]);
    }

    public function checkout(Request $request)
    {
        $keranjangItems = Auth::user()->keranjangItems;

        if ($keranjangItems->isEmpty()) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang Anda kosong');
        }

        return view('user.pemesanan.index', [
            'keranjangItems' => $keranjangItems,
            'pageTitle' => "Form Pemesanan",
        ]);
    }

   public function store(Request $request)
{
    try {
        // Mulai transaksi database
        DB::beginTransaction();

        // Validasi input dari request
        $validated = $request->validate([
            'event_date' => 'required|date|after_or_equal:today',
            'event_place' => 'required|string|max:255',
            'payment_method' => 'required|string',
        ]);

        // Ambil item keranjang pengguna yang sedang login
        $keranjangItems = Auth::user()->keranjangItems()->with(['paket', 'produk'])->get();

        // Cek jika keranjang kosong
        if ($keranjangItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja Anda kosong.',
            ], 400);
        }

        // Inisialisasi variabel untuk total harga dan item pesanan
        $totalAmount = 0;
        $orderItems = [];
        $orderId = 'ORDER-' . time() . '-' . Auth::id(); // Generate ID pesanan

        // Loop melalui item di keranjang untuk menghitung total dan menyusun detail pesanan
        foreach ($keranjangItems as $item) {
            $itemPrice = $item->paket?->harga ?? $item->produk?->harga ?? 0;
            $subtotal = $item->jumlah * $itemPrice;
            $totalAmount += $subtotal;

            $orderItems[] = [
                'id' => $item->paket_id ?? $item->produk_id,
                'price' => $itemPrice,
                'quantity' => $item->jumlah,
                'name' => $item->paket?->nama ?? $item->produk?->nama ?? 'Unknown Item',
            ];
        }

        // Data transaksi untuk gateway pembayaran
        $transactionData = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $totalAmount,
            ],
            'item_details' => $orderItems,
            'customer_details' => [
                'first_name' => Auth::user()->nama,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone ?? '',
            ],
            'enabled_payments' => [$request->payment_method],
           'callbacks' => [
                 'finish' => route('pemesanan.riwayatPemesanan'),
            ],
        ];

        // Generate Snap Token untuk pembayaran
        $snapToken = Snap::getSnapToken($transactionData);

        // Buat pesanan untuk setiap item di keranjang
        foreach ($keranjangItems as $item) {
            $itemPrice = $item->paket?->harga ?? $item->produk?->harga ?? 0;
            $subtotal = $item->jumlah * $itemPrice;

            Pemesanan::create([
                'user_id' => Auth::id(),
                'paket_id' => $item->paket_id,
                'produk_id' => $item->produk_id,
                'event_date' => $request->event_date,
                'event_place' => $request->event_place,
                'jumlah' => $item->jumlah,
                'total_harga' => $subtotal,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        }

        // Hapus semua item dari keranjang pengguna
        Auth::user()->keranjangItems()->delete();

        // Commit transaksi database
        DB::commit();

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'order_id' => $orderId,
            'redirect_url' => route('pemesanan.status', ['orderId' => $orderId]),
        ]);

    } catch (\Exception $e) {
        // Rollback transaksi jika ada error
        DB::rollback();
        \Log::error('Error dalam pembuatan pesanan: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan dalam pemrosesan pesanan: ' . $e->getMessage(),
        ], 500);
    }
}


    public function handleNotification(Request $request)
    {
        // Ambil notifikasi dari Midtrans
        $notif = new \Midtrans\Notification();

        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $statusCode = $notif->status_code;

        // Cari pemesanan berdasarkan order_id
        $pemesanan = Pemesanan::where('order_id', $orderId)->first();

        if (!$pemesanan) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update status berdasarkan status dari Midtrans
        if ($transactionStatus == 'settlement') {
            $pemesanan->status = 'paid';
        } elseif ($transactionStatus == 'pending') {
            $pemesanan->status = 'pending';
        } elseif ($transactionStatus == 'expire') {
            $pemesanan->status = 'expired';
        } elseif ($transactionStatus == 'cancel') {
            $pemesanan->status = 'cancelled';
        }

        $pemesanan->save();

        return response()->json(['message' => 'Notification handled'], 200);
    }


    private function determinePaymentStatus($transactionStatus, $paymentType, $fraudStatus = null)
        {
            switch ($transactionStatus) {
                case 'capture':
                    if ($paymentType == 'credit_card') {
                        return ($fraudStatus == 'challenge') ? 'challenge' : 'paid';
                    }
                    return 'paid';
                case 'settlement':
                    return 'paid';
                case 'pending':
                    return 'pending';
                case 'deny':
                    return 'failed';
                case 'expire':
                    return 'expired';
                case 'cancel':
                    return 'cancelled';
                default:
                    return 'pending';
            }
        }

    public function show($orderId)
    {


    }

    public function showPaymentStatus($orderId)
    {
        $orderItems = Pemesanan::where('order_id', $orderId)
            ->with(['paket', 'produk'])
            ->get();

        if ($orderItems->isEmpty()) {
            return redirect()->route('homepage')
                ->with('error', 'Pesanan tidak ditemukan');
        }

        if ($orderItems->first()->user_id !== Auth::id()) {
            return redirect()->route('homepage')
                ->with('error', 'Anda tidak memiliki akses ke pesanan ini');
        }

        return view('user.pemesanan.status', [
            'orderItems' => $orderItems,
            'orderId' => $orderId,
            'pageTitle' => "Status Pemesanan"
        ]);
    }

    public function riwayatPemesanan(Request $request)
{
    // Validate Midtrans callback parameters
    $orderId = $request->get('order_id');
    $transactionStatus = $request->get('transaction_status');
    $statusCode = $request->get('status_code');

    // Strict server-side verification
    if ($orderId && $transactionStatus) {
        try {
            // Verify transaction status directly with Midtrans
            $midtransStatus = \Midtrans\Transaction::status($orderId);

            // Comprehensive validation
            $isValidTransaction =
            $midtransStatus->transaction_status === $transactionStatus &&
            $midtransStatus->status_code === $statusCode &&
            $midtransStatus->gross_amount > 0;

            // Update only if transaction is genuinely verified
            if ($isValidTransaction) {
                $status = $this->determinePaymentStatus(
                    $midtransStatus->transaction_status,
                    $midtransStatus->payment_type
                );

                Pemesanan::where('order_id', $orderId)
                    ->update(['status' => $status]);
            } else {
                // Log potential fraud attempt
                Log::warning("Potential unauthorized payment attempt: {$orderId}");
            }
        } catch (\Exception $e) {
            Log::error("Midtrans verification failed: " . $e->getMessage());
        }
    }

    // Fetch authenticated user's order history
    $riwayat = Pemesanan::where('user_id', auth()->id())
        ->with(['produk', 'paket'])
        ->get();

    return view('user.pemesanan.riwayatPemesanan', [
        'riwayat' => $riwayat,
        'pageTitle' => "Riwayat Pemesanan"
    ]);
}

    private function verifyMidtransStatus($orderId)
    {
        // Use Midtrans API to verify the actual transaction status
        $midtrans = new \Midtrans\Transaction();
        $status = $midtrans->status($orderId);

        return $status->transaction_status;
    }
    public function printPDF($orderId)
    {
        // Cek status pembayaran terlebih dahulu
        $orderItems = Pemesanan::where('order_id', $orderId)->firstOrFail();

        if ($orderItems->status !== 'paid') {
            return redirect()->back()->with('error', 'PDF invoice hanya dapat diakses setelah pembayaran selesai');
        }

        // Jika status paid, lanjutkan generate PDF
        $orderItems = Pemesanan::where('order_id', $orderId)
            ->with(['paket', 'produk'])
            ->get();

        $logoPath = public_path('images/transparent.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView('user.pemesanan.pdf.invoice', compact('orderItems', 'orderId','logoBase64'));

        return $pdf->stream("Kenanga-Catering-{$orderId}.pdf");
    }

}
