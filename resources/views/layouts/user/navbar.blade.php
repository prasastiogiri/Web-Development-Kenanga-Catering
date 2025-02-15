{{-- layouts/user/navbar.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link rel="icon" href="{{ asset('images/transparent.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.css">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-E8j8XAGOOI6Wk5HU"></script>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg fixed-top shadow">
            <!-- Navigation Content -->
            <div class="container-fluid">
                <img src="{{ asset('images/transparent.png') }}" alt="logo" class="logo">
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <!-- Navbar Content -->
                    <div class="offcanvas-header">
                        <a href="{{ route('homepage') }}">
                            <img src="{{ asset('images/transparent.png') }}" alt="logo" class="logo offcanvas-title" id="offcanvasNavbarLabel">
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>

                    <div class="offcanvas-body">
                        <!-- Navigation Links -->
                        <ul class="navbar-nav justify-content-center flex-grow-1 ps-5">
                            <li class="nav-item">
                                <a class="nav-link mx-lg-2 {{ request()->routeIs('homepage') ? 'active' : '' }}" href="{{ route('homepage') }}">Beranda</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mx-lg-2 {{ request()->routeIs('produkUser.index') ? 'active' : '' }}" href="{{ route('produkUser.index') }}">Produk</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mx-lg-2 {{ request()->routeIs('paketUser.index') ? 'active' : '' }}" href="{{ route('paketUser.index') }}">Paket</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Shopping Cart -->
                <div class="cart">
                    <!-- Cart Content -->
                    @include('layouts.user.partials.cart')
                </div>

                <!-- Login/User Menu -->
                <div class="login-button">
                    @guest
                        <a href="{{ url('login') }}" class="text-light text-decoration-none">Login</a>
                    @endguest

                    @auth
                        <div class="dropdown-center">
                            <!-- User Menu Content -->
                            @include('layouts.user.partials.user-menu')
                        </div>
                    @endauth
                    <button class="navbar-toggler pe-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script src="{{ asset('js/navbar.js') }}"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</body>
</html>
