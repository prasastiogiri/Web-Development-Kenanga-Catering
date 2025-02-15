@extends('layouts.admin.navbar')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="mb-5"
                style="font-size: clamp(2rem, 4vw, 3.125rem);
                font-weight: 700;
                color: #9a7726;
                margin-bottom: 2rem;
                text-align: left;">
                Laporan Penjualan
            </h2>
            <div class="d-flex align-items-center gap-3">
                <div class="btn-group export-dropdown">
                    <button type="button" class="btn btn-outline-primary rounded px-4 dropdown-toggle"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-cloud-download me-2"></i>Export Data
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="exportData('semua')"><i
                                    class="bi bi-database me-2"></i>Semua Data</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData('produk')"><i
                                    class="bi bi-box-seam me-2"></i>Produk</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData('paket')"><i
                                    class="bi bi-gift me-2"></i>Paket</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Filter Section -->

        <div class="filter-section mb-5">
            <form id="filterForm" method="GET" class="row g-4">
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted mb-2">Tanggal Mulai</label>
                    <input type="date" class="form-control shadow-sm" name="start_date"
                        value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted mb-2">Tanggal Berakhir</label>
                    <input type="date" class="form-control shadow-sm" name="end_date"
                        value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted mb-2">Metode Pembayaran</label>
                    <div class="select-wrapper">
                        <select class="form-select shadow-sm" name="payment_method">
                            <option value="">Semua Metode Pembayaran</option>
                            @php
                                $paymentMethodLabels = [
                                    'bca_va' => 'BCA Virtual Account',
                                    'mandiri_va' => 'Mandiri Virtual Account',
                                    'bni_va' => 'BNI Virtual Account',
                                    'bri_va' => 'BRI Virtual Account',
                                    'permata_va' => 'Permata Virtual Account',
                                    'gopay' => 'GoPay',
                                    'dana' => 'DANA',
                                    'ovo' => 'OVO',
                                    'shopeepay' => 'ShopeePay',
                                ];
                            @endphp

                            @foreach ($availablePaymentMethods as $method)
                                <option value="{{ $method }}"
                                    {{ request('payment_method') == $method ? 'selected' : '' }}>
                                    {{ $paymentMethodLabels[$method] ?? str_replace('_', ' ', ucwords($method, '_')) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
                        <i class="bi bi-funnel me-2"></i>Terapkan Filter
                    </button>
                    <button type="button" class="btn btn-outline-secondary shadow-sm" onclick="resetFilters()">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4 mb-5">
            @foreach ([['icon' => 'bi-wallet2', 'color' => 'white', 'title' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.')], ['icon' => 'bi-cart-check', 'color' => 'black', 'title' => 'Total Pesanan', 'value' => number_format($totalOrders, 0, ',', '.')]] as $stat)
                <div class="col-12 col-md-6">
                    <div class="stats-card h-100 shadow-sm bg-white p-2 rounded">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="stats-icon me-4">
                                <i class="bi {{ $stat['icon'] }}"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-2">{{ $stat['title'] }}</h6>
                                <h2 class="mb-0 fw-bold">{{ $stat['value'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-lg-6">
                <div class="chart-card h-100 shadow-sm bg-white p-2 rounded">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Penjualan Bulanan</h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="chart-card h-100 shadow-sm bg-white p-2 rounded">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Distribusi Metode Pembayaran</h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products & Packages -->
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="chart-card h-100 shadow-sm bg-white p-2 rounded">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Produk Terlaris</h5>
                        <i class="bi bi-tag text-primary fs-5"></i>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Total Terjual</th>
                                        <th class="text-end">Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topProducts as $product)
                                        <tr>
                                            <td>{{ $product->produk->nama }}</td>
                                            <td class="text-center">{{ number_format($product->total_sales) }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="chart-card h-100 shadow-sm bg-white p-2 rounded">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Paket Terlaris</h5>
                        <i class="bi bi-box-seam text-primary fs-5"></i>
                    </div>
                    <div class="card-body p-0 bg-transparent">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table">
                                <thead>
                                    <tr>
                                        <th>Nama Paket</th>
                                        <th class="text-center">Total Terjual</th>
                                        <th class="text-end">Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topPackages as $package)
                                        <tr>
                                            <td>{{ $package->paket->nama }}</td>
                                            <td class="text-center">{{ number_format($package->total_sales) }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($package->total_revenue, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @push('scripts')
        <!-- Tambahkan CDN Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Monthly Sales Chart (Bar Chart)
                const salesCtx = document.getElementById('monthlySalesChart').getContext('2d');
                new Chart(salesCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(
                            $monthlySales->pluck('month')->map(function ($m) {
                                $indonesianMonths = [
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember',
                                ];
                                return $indonesianMonths[$m] ?? date('F', mktime(0, 0, 0, $m, 1));
                            }),
                        ) !!},
                        datasets: [{
                            label: 'Total Penjualan',
                            data: {!! json_encode($monthlySales->pluck('total_sales')) !!},
                            backgroundColor: 'rgba(102, 126, 234, 0.7)',
                            borderColor: '#667eea',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(102, 126, 234, 0.9)'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value),
                                    color: '#4a5568'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#4a5568'
                                }
                            }
                        }
                    }
                });

                const paymentCtx = document.getElementById('paymentMethodsChart').getContext('2d');

                const paymentMethodLabels = {
                    'bca_va': 'BCA Virtual Account',
                    'mandiri_va': 'Mandiri Virtual Account',
                    'bni_va': 'BNI Virtual Account',
                    'bri_va': 'BRI Virtual Account',
                    'permata_va': 'Permata Virtual Account',
                    'gopay': 'GoPay',
                    'dana': 'DANA',
                    'ovo': 'OVO',
                    'shopeepay': 'ShopeePay'
                };

                // Transform labels
                const labels = {!! json_encode($paymentMethods->pluck('payment_method')) !!}.map(method =>
                    paymentMethodLabels[method] || method.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
                );

                new Chart(paymentCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: {!! json_encode($paymentMethods->pluck('total')) !!},
                            backgroundColor: [
                                '#667eea',
                                '#43e97b',
                                '#764ba2',
                                '#38f9d7',
                                '#a0aec0'
                            ],
                            borderColor: [
                                '#5563c1',
                                '#38d16b',
                                '#653d91',
                                '#2dd1b8',
                                '#8e9aab'
                            ],
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(102, 126, 234, 0.9)'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#2d3748',
                                bodyColor: '#4a5568',
                                borderColor: '#e2e8f0',
                                borderWidth: 1,
                                padding: 15
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    color: '#4a5568',
                                    stepSize: 1
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#4a5568'
                                }
                            }
                        }
                    }
                });

                flatpickr('input[type="date"]', {
                    dateFormat: "Y-m-d",
                    theme: "material_blue"
                });
            });

            // Fungsi export data dengan SweetAlert
            function exportData(type) {
                Swal.fire({
                    title: 'Export Data',
                    text: `Anda akan mengekspor ${type} data, lanjutkan?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#926c15',
                    cancelButtonColor: '#cbd5e0',
                    confirmButtonText: 'Ya, Export!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('filterForm');
                        const formData = new FormData(form);
                        formData.append('export_type', type);
                        const params = new URLSearchParams(formData).toString();
                        window.location.href = '{{ route('admin.laporan.export') }}?' + params;
                    }
                });
            }
            // Function to reset filters
            function resetFilters() {
                // Get the filter form
                const form = document.getElementById('filterForm');

                // Reset all inputs to their default values
                const inputs = form.querySelectorAll('input[type="date"], select');
                inputs.forEach(input => {
                    if (input.type === 'date') {
                        // For date inputs, reset to default dates
                        if (input.name === 'start_date') {
                            input.value = '{{ $startDate->format('Y-m-d') }}';
                        } else if (input.name === 'end_date') {
                            input.value = '{{ $endDate->format('Y-m-d') }}';
                        }
                    } else if (input.tagName === 'SELECT') {
                        // For select inputs, set to empty/default option
                        input.value = '';
                    }
                });

                // Submit the form to refresh the page with reset filters
                form.submit();
            }
        </script>
    @endpush
@endsection
