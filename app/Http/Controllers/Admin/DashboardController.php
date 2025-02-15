<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Produk;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pageTitle = 'Dashboard';
        $totalPaket = Paket::count();
        $totalProduk = Produk::count();

        // Get orders statistics - menghitung berdasarkan order_id unik
        $totalOrders = Pemesanan::where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')
            ->distinct('order_id')
            ->count('order_id');

        // Get revenue for current month
        $currentMonthRevenue = Pemesanan::where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_harga');

        // Get recent orders - grouped by order_id
        $recentOrders = Pemesanan::with(['produk', 'paket'])
            ->select('order_id', DB::raw('MAX(id) as id'), DB::raw('MAX(created_at) as created_at'))
            ->where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')
            ->groupBy('order_id')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get top selling products
        $topProducts = Pemesanan::where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')
            ->whereNotNull('produk_id')
            ->with('produk')
            ->select('produk_id', DB::raw('COUNT(DISTINCT order_id) as total_sales'))
            ->groupBy('produk_id')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // Get top selling packages
        $topPackages = Pemesanan::where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')
            ->whereNotNull('paket_id')
            ->with('paket')
            ->select('paket_id', DB::raw('COUNT(DISTINCT order_id) as total_sales'))
            ->groupBy('paket_id')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();
            $revenueData = Pemesanan::getRevenue(7);

            // Format data for chart
            $chartData = [
                'labels' => $revenueData->pluck('date')->map(function($date) {
                    return Carbon::parse($date)->format('d M');
                }),
                'data' => $revenueData->pluck('total')
            ];
            $unprocessedOrders = Pemesanan::whereNull('status_pemesanan')
        ->where('status', 'paid')
        ->distinct('order_id')
        ->count('order_id');
        return view('admin.dashboard', compact(
            'totalPaket',
            'totalProduk',
            'totalOrders',
            'currentMonthRevenue',
            'recentOrders',
            'topProducts',
            'topPackages',
            'pageTitle','chartData','unprocessedOrders'
        ));
    }
    // App\Models\Pemesanan.php

        public function scopeGetRevenue($query, $days = 7)
        {
            return $query->where('status', 'paid')
                ->where('status_pemesanan', 'Selesai')
                ->where('created_at', '>=', now()->subDays($days))
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_harga) as total')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        public function getRevenueData(Request $request)
        {
            $days = $request->get('days', 7);
            $revenueData = Pemesanan::getRevenue($days);

            return response()->json([
                'labels' => $revenueData->pluck('date')->map(function($date) {
                    return Carbon::parse($date)->format('d M');
                }),
                'data' => $revenueData->pluck('total')
            ]);
        }
    }

