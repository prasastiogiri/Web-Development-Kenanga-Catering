<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Laporan Penjualan';

        // Get filter parameters
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfYear();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        $paymentMethod = $request->input('payment_method');

        // Base query with date filters
        $baseQuery = Pemesanan::where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Apply payment method filter if selected
        if ($paymentMethod) {
            $baseQuery->where('payment_method', $paymentMethod);
        }

        // Get monthly sales data
        $monthlySales = clone $baseQuery;
        $monthlySales = $monthlySales->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_harga) as total_sales')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get payment method statistics
        $paymentMethods = clone $baseQuery;
        $paymentMethods = $paymentMethods->select(
            'payment_method',
            DB::raw('COUNT(DISTINCT order_id) as total') // Changed to count distinct order_id
        )
            ->groupBy('payment_method')
            ->get();

        // Get top selling products
        $topProducts = clone $baseQuery;
        $topProducts = $topProducts->whereNotNull('produk_id')
            ->with('produk')
            ->select(
                'produk_id',
                DB::raw('SUM(jumlah) as total_sales'),
                DB::raw('SUM(total_harga) as total_revenue')
            )
            ->groupBy('produk_id')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // Get top selling packages
        $topPackages = clone $baseQuery;
        $topPackages = $topPackages->whereNotNull('paket_id')
            ->with('paket')
            ->select(
                'paket_id',
                DB::raw('SUM(jumlah) as total_sales'),
                DB::raw('SUM(total_harga) as total_revenue')
            )
            ->groupBy('paket_id')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // Get total revenue
        $totalRevenue = clone $baseQuery;
        $totalRevenue = $totalRevenue->sum('total_harga');

        // Get total orders
        $totalOrders = clone $baseQuery;
        $totalOrders = $totalOrders->count(DB::raw('DISTINCT order_id'));

        // Get all available payment methods for filter
        $availablePaymentMethods = Pemesanan::distinct()->pluck('payment_method');

        return view('admin.laporan.index', compact(
            'monthlySales',
            'paymentMethods',
            'topProducts',
            'topPackages',
            'totalRevenue',
            'totalOrders',
            'pageTitle',
            'startDate',
            'endDate',
            'paymentMethod',
            'availablePaymentMethods'
        ));
    }


public function exportData(Request $request)
{
    // Get filter parameters
    $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfYear();
    $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();
    $paymentMethod = $request->input('payment_method');
    $exportType = $request->input('export_type', 'semua');

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();

    // Set default styles for headers
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];

    // Set default styles for data cells
    $dataStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];

    if ($exportType === 'semua' || $exportType === 'produk') {
        $worksheet = $exportType === 'semua' ? $spreadsheet->createSheet() : $spreadsheet->getActiveSheet();
        $worksheet->setTitle('Laporan Produk');

        // Set column widths
        $worksheet->getColumnDimension('A')->setWidth(30);
        $worksheet->getColumnDimension('B')->setWidth(15);
        $worksheet->getColumnDimension('C')->setWidth(20);
        $worksheet->getColumnDimension('D')->setWidth(20);

        // Add title
        $worksheet->mergeCells('A1:D1');
        $worksheet->setCellValue('A1', 'LAPORAN PENJUALAN PRODUK');
        $worksheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Add period
        $worksheet->mergeCells('A2:D2');
        $worksheet->setCellValue('A2', 'Periode: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'));
        $worksheet->getStyle('A2')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Add headers
        $worksheet->setCellValue('A4', 'Nama Produk');
        $worksheet->setCellValue('B4', 'Total Penjualan');
        $worksheet->setCellValue('C4', 'Harga Satuan');
        $worksheet->setCellValue('D4', 'Total Pendapatan');
        $worksheet->getStyle('A4:D4')->applyFromArray($headerStyle);

        // Get product data
        $products = Pemesanan::where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('produk_id')
            ->when($paymentMethod, function($query) use ($paymentMethod) {
                return $query->where('payment_method', $paymentMethod);
            })
            ->with('produk')
            ->select(
                'produk_id',
                DB::raw('SUM(jumlah) as total_sales'),
                DB::raw('SUM(total_harga) as total_revenue')
            )
            ->groupBy('produk_id')
            ->get();

        $row = 5;
        $total = ['sales' => 0, 'revenue' => 0];

        foreach ($products as $product) {
            $worksheet->setCellValue('A' . $row, $product->produk->nama);
            $worksheet->setCellValue('B' . $row, $product->total_sales);
            $worksheet->setCellValue('C' . $row, 'Rp ' . number_format($product->produk->harga, 0, ',', '.'));
            $worksheet->setCellValue('D' . $row, 'Rp ' . number_format($product->total_revenue, 0, ',', '.'));

            $total['sales'] += $product->total_sales;
            $total['revenue'] += $product->total_revenue;

            $row++;
        }

        // Add totals
        $worksheet->setCellValue('A' . $row, 'TOTAL');
        $worksheet->setCellValue('B' . $row, $total['sales']);
        $worksheet->setCellValue('D' . $row, 'Rp ' . number_format($total['revenue'], 0, ',', '.'));
        $worksheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA']
            ]
        ]);

        // Apply styles to data cells
        $worksheet->getStyle('A5:D' . ($row-1))->applyFromArray($dataStyle);
    }

    if ($exportType === 'semua' || $exportType === 'paket') {
        $worksheet = $exportType === 'semua' ? $spreadsheet->createSheet() : $spreadsheet->getActiveSheet();
        $worksheet->setTitle('Laporan Paket');

        // Set column widths
        $worksheet->getColumnDimension('A')->setWidth(30);
        $worksheet->getColumnDimension('B')->setWidth(15);
        $worksheet->getColumnDimension('C')->setWidth(20);
        $worksheet->getColumnDimension('D')->setWidth(20);

        // Add title
        $worksheet->mergeCells('A1:D1');
        $worksheet->setCellValue('A1', 'LAPORAN PENJUALAN PAKET');
        $worksheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Add period
        $worksheet->mergeCells('A2:D2');
        $worksheet->setCellValue('A2', 'Periode: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'));
        $worksheet->getStyle('A2')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Add headers
        $worksheet->setCellValue('A4', 'Nama Paket');
        $worksheet->setCellValue('B4', 'Total Penjualan');
        $worksheet->setCellValue('C4', 'Harga Satuan');
        $worksheet->setCellValue('D4', 'Total Pendapatan');
        $worksheet->getStyle('A4:D4')->applyFromArray($headerStyle);

        // Get package data
        $packages = Pemesanan::where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('paket_id')
            ->when($paymentMethod, function($query) use ($paymentMethod) {
                return $query->where('payment_method', $paymentMethod);
            })
            ->with('paket')
            ->select(
                'paket_id',
                DB::raw('SUM(jumlah) as total_sales'),
                DB::raw('SUM(total_harga) as total_revenue')
            )
            ->groupBy('paket_id')
            ->get();

        $row = 5;
        $total = ['sales' => 0, 'revenue' => 0];

        foreach ($packages as $package) {
            $worksheet->setCellValue('A' . $row, $package->paket->nama);
            $worksheet->setCellValue('B' . $row, $package->total_sales);
            $worksheet->setCellValue('C' . $row, 'Rp ' . number_format($package->paket->harga, 0, ',', '.'));
            $worksheet->setCellValue('D' . $row, 'Rp ' . number_format($package->total_revenue, 0, ',', '.'));

            $total['sales'] += $package->total_sales;
            $total['revenue'] += $package->total_revenue;

            $row++;
        }

        // Add totals
        $worksheet->setCellValue('A' . $row, 'TOTAL');
        $worksheet->setCellValue('B' . $row, $total['sales']);
        $worksheet->setCellValue('D' . $row, 'Rp ' . number_format($total['revenue'], 0, ',', '.'));
        $worksheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA']
            ]
        ]);

        // Apply styles to data cells
        $worksheet->getStyle('A5:D' . ($row-1))->applyFromArray($dataStyle);
    }

    if ($exportType === 'all') {
        // Set the first sheet as active
        $spreadsheet->setActiveSheetIndex(0);
    }

    // Create Excel file
    $writer = new Xlsx($spreadsheet);
    $baseFilename = match($exportType) {
        'products' => 'Laporan_Produk',
        'packages' => 'Laporan_Paket',
        default => 'Laporan_Penjualan'
    };

    $filename = $baseFilename . '_KenangaCatering_' . date('Y-m-d') . '.xlsx';

    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
}

    public function getChartData()
    {
        // API endpoint for AJAX chart updates
        $monthlySales = Pemesanan::where('status', 'paid')
            ->where('status_pemesanan', 'Selesai')  // Added this condition
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_harga) as total_sales')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthlySales);
    }
}
