@extends('layouts.admin.navbar')

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @section('content')
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1 fw-bold">Selamat Datang Kembali!👋</h2>
                    <p class="mb-0 text-white-50">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Pendapatan Bulan Ini -->
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <h6 class="text-white-50 mb-2">Pendapatan Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($currentMonthRevenue, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Total Pesanan -->
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div>
                            <h6 class="text-white-50 mb-2">Total Pesanan</h6>
                            <h4 class="mb-0 fw-bold">{{ $totalOrders }}</h4>
                            @if($unprocessedOrders > 0)
                                <small class="text-warning">
                                    {{ $unprocessedOrders }} Pesanan Belum Dikerjakan, Silahkan cek di
                                    <a href="{{ route('transaksi.index') }}" class="text-decoration-none fw-bold text-white bg-secondary rounded px-2 py-1 d-inline-block mt-1">
                                        Cek di sini
                                    </a>
                                </small>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- Total Produk -->
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h6 class="text-white-50 mb-2">Total Produk</h6>
                            <h4 class="mb-0 fw-bold">{{ $totalProduk }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Total Paket -->
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-gift"></i>
                        </div>
                        <div>
                            <h6 class="text-white-50 mb-2">Total Paket</h6>
                            <h4 class="mb-0 fw-bold">{{ $totalPaket }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart & Activity -->
        <div class="row g-4">
            <!-- Revenue Chart -->
            <div class="col-lg-8">
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-semibold mb-0">Analisis Pendapatan</h5>
                        <select class="form-select form-select-sm w-auto" id="chartPeriod">
                            <option value="7">7 Hari Terakhir</option>
                            <option value="30">30 Hari Terakhir</option>
                            <option value="90">3 Bulan Terakhir</option>
                        </select>
                    </div>
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-4">
                <div class="recent-orders-card">
                    <h5 class="fw-semibold mb-4">Aktivitas Terkini</h5>
                    <div class="activity-list">
                        @foreach ($recentOrders as $order)
                            <div class="activity-item">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle activity-icon bg-primary text-white me-3">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold">Pesanan #{{ $order->order_id }}</h6>
                                        <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
       @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($chartData['data']),
                    fill: false,
                    borderColor: '#926c15', // Warna disesuaikan dengan tema
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting untuk kontrol ukuran
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { size: 14 },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return ' Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e5e7eb' },
                        ticks: {
                            color: '#6b7280',
                            font: { size: 12 },
                            callback: function(value) {
                                return 'Rp' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6b7280',
                            font: { size: 12 }
                        }
                    }
                }
            }
        });

        // Handle period change (tetap sama)
        document.getElementById('chartPeriod').addEventListener('change', function() {
            const days = this.value;
            fetch(`/admin/dashboard/revenue-data?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    revenueChart.data.labels = data.labels;
                    revenueChart.data.datasets[0].data = data.data;
                    revenueChart.update();
                });
        });
    });
</script>
@endpush
    </script>
    @endpush
