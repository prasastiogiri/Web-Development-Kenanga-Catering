<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- External CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/transparent.png') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar-admin.css') }}">
    @stack('styles')
</head>

<body>
    <button class="mobile-navbar-toggle d-md-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand text-center">
            <img src="{{ asset('images/transparent.png') }}" alt="logo" class="me-2"
                    style="width: 100px; height: 100px; object-fit: contain;">
            <h5 class=" mb-0 fw-semibold" style="color: #926c15;">
                Kenanga Catering
            </h5>
        </div>

        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-home me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin-produk.index') ? 'active' : '' }}"
                        href="{{ route('admin-produk.index') }}">
                        <i class="fa-solid fa-box-open me-2"></i>
                        Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin-paket.index') ? 'active' : '' }}"
                        href="{{ route('admin-paket.index') }}">
                        <i class="fa-solid fa-gift me-2"></i>
                        Paket
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('transaksi.index') ? 'active' : '' }}"
                        href="{{ route('transaksi.index') }}">
                        <i class="fa-solid fa-cash-register me-2"></i>
                        Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}"
                        href="{{ route('admin.laporan') }}">
                        <i class="fa-solid fa-chart-bar me-2"></i>
                        Laporan
                    </a>
                </li>
                <li class="nav-item mt-auto">
                    <a href="#" onclick="confirmLogout(event)" class="nav-link text-danger">
                        <i class="fa-solid fa-sign-out-alt me-2"></i>
                        Logout
                    </a>
                </li>
            </ul>


        </div>
    </nav>

    <!-- Main content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
       function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('show');

        // Ganti icon toggle
        const toggleBtn = document.querySelector('.mobile-navbar-toggle');
        if (sidebar.classList.contains('show')) {
            toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        }
    }

    // Auto-close sidebar pada mobile saat mengklik link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 768px)').matches) {
                toggleSidebar();
            }
        });
    });

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Logout',
            text: 'Anda yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#926c15',
            cancelButtonColor: '#cbd5e0',
            confirmButtonText: 'Ya, Logout'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
    </script>

    @stack('scripts')
</body>
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>
</html>
