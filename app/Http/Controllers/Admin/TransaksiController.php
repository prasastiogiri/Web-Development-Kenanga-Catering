<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
class TransaksiController extends Controller
{
    /**
     * Display a listing of paid transactions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $transactions = Pemesanan::where('status', 'paid')
           
            ->with(['user', 'produk', 'paket'])
            ->latest()
            ->get();

        $pageTitle = 'Transaksi';
        return view('admin.transaksi.index', [
            'transactions' => $transactions
        ],compact('pageTitle'));
    }
    /**
     * Show detailed information for a specific transaction.
     *
     * @param string $orderId
     * @return \Illuminate\View\View
     */
    public function show($orderId)
    {
        $transaction = Pemesanan::where('order_id', $orderId)
            ->with(['user', 'produk', 'paket'])
            ->firstOrFail();

        return view('admin.transaksi.show', [
            'transaction' => $transaction,
            'pageTitle' => 'Detail Transaksi'
        ]);
    }

public function updateStatus(Request $request)
{
    $orderId = $request->input('order_id');

    $transactions = Pemesanan::where('order_id', $orderId)->get();

    if ($transactions->isEmpty()) {
        return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
    }

    $newStatus = $transactions[0]->status_pemesanan === 'Selesai' ? null : 'Selesai';

    foreach ($transactions as $transaction) {
        $transaction->status_pemesanan = $newStatus;
        $transaction->save();
    }

    return response()->json([
        'message' => $newStatus ? 'Status diperbarui menjadi Selesai' : 'Status diperbarui menjadi Belum Selesai'
    ]);
}


}
