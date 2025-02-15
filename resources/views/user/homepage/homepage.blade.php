@extends('layouts.user.navbar')

<link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />


@section('content')
    <div class="container-fluid p-0">
        <div class="hero-section">
            <h1 class="title">
                Selamat Datang<br>
                di <span class="italic-title">Kenanga Catering</span>
            </h1>
        </div>
    </div>
    <div class="container-fluid p-0">
        <div class="section-0">
            <h1 class="sub-title">Tentang Kami</h1>
            <div class="about-container mt-5">
                <div class="about-image-container">
                    <img src="{{ asset('images/aboutus.png') }}" alt="Kenanga Catering" class="tentang-kami-img">
                </div>
                <div class="about-content">
                    <div class="about-text">
                        <p class="indent">
                            Kenanga Catering adalah usaha katering yang berlokasi di Mojokerto dan telah beroperasi lebih dari
                            10 tahun. Sejak didirikan, kami berkomitmen untuk menghadirkan hidangan yang lezat, berkualitas, dan
                            sesuai selera pelanggan. Dengan pengalaman lebih dari satu dekade, kami telah melayani berbagai
                            acara mulai dari pernikahan, ulang tahun, hingga acara kantor dan keluarga.
                        </p>
                        <p class="indent">
                            Kami mengutamakan bahan baku segar dan proses masak yang higienis untuk menjaga kualitas setiap
                            hidangan yang kami sajikan. Selain itu, tim profesional kami siap membantu memastikan setiap acara
                            berjalan lancar dengan layanan yang ramah dan tepat waktu.
                        </p>
                        <p class="indent">
                            Kenanga Catering bukan hanya tentang makanan, tetapi juga tentang menciptakan momen istimewa yang
                            tak terlupakan. Kami selalu berusaha memberikan yang terbaik untuk memenuhi harapan pelanggan dengan
                            berbagai pilihan menu yang bisa disesuaikan sesuai kebutuhan acara.
                        </p>
                        <p class="indent">
                            Terima kasih telah mempercayakan momen berharga Anda kepada Kenanga Catering. Kami siap melayani dan
                            menjadi bagian dari kebahagiaan Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid p-0">
        <div class="section-1">
            <h1 class="sub-title">Menu Pilihan Kami</h1>
            <div class="menu-container mt-5">
                <div id="packageCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        @foreach ($items->chunk(3) as $index => $chunk)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="row justify-content-center g-4">
                                    @foreach ($chunk as $item)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="package-card card">
                                                <div class="card-img-wrapper">
                                                    <img src="{{ asset('storage/' . $item->foto) }}"
                                                         class="card-img-top"
                                                         alt="{{ $item->nama }}">
                                                </div>
                                                <div class="card-body">
                                                    <h5 class="card-title" data-bs-toggle="tooltip"
                                                        title="{{ $item->nama }}">
                                                        {{ Str::limit($item->nama, 50, '...') }}
                                                    </h5>
                                                    <p class="card-text" data-bs-toggle="tooltip"
                                                        title="{{ $item->deskripsi }}">
                                                        {{ Str::limit($item->deskripsi, 150, '...') }}
                                                    </p>
                                                    <a href="{{ route('produkUser.index') }}"
                                                        class="btn btn-primary pesan-sekarang"
                                                        data-login="{{ Auth::check() ? 'true' : 'false' }}"
                                                        data-login-url="{{ route('user.login') }}">
                                                        <span>Pesan Sekarang</span>
                                                        <i class="fas fa-arrow-right ms-2"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#packageCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#packageCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid p-0">
        <div class="section-0">
            <h1 class="sub-title">Testimoni</h1>
            <div class="container sub-title mt-5">
                <iframe class="testimoni-iframe"
                    src="https://www.youtube.com/embed/sfqm8hTfdXQ?si=HxvR08RIN4KV3ugy&amp;controls=0"
                    title="YouTube video player"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
    @include('user.homepage.footer')

    <script src="{{ asset('js/homepage.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
