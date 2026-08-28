@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    /* === HERO SECTION === */
    .hero-home {
        background: url("{{ asset('image/bg2.jpg') }}") no-repeat center center / cover;
        height: 100vh;
        position: relative;
        color: white;
    }

    .hero-overlay {
        background-color: rgba(0, 0, 0, 0.55);
        height: 100%;
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 20px;
    }

    .hero-title-main {
        font-size: 4rem;
        font-weight: 800;
        letter-spacing: 2px;
        text-shadow: 0 4px 15px rgba(0,0,0,0.6);
        margin-bottom: 10px;
    }

    .hero-subtitle-main {
        font-size: 1.4rem;
        font-weight: 500;
        color: #e2e8f0;
        text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        margin-bottom: 30px;
    }

    /* === STATS CARDS === */
    .stats-card {
        background: linear-gradient(135deg, #198754, #20c997);
        color: white;
        border-radius: 18px;
        padding: 2rem;
        text-align: center;
        transition: transform 0.3s ease;
        box-shadow: 0 10px 30px rgba(25, 135, 84, 0.2);
    }

    .stats-card:hover {
        transform: translateY(-8px);
    }

    .stats-number {
        font-size: 2.8rem;
        font-weight: 800;
    }

    /* === RECOMMENDATIONS & NEWS CARDS === */
    .news-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .news-card:hover {
        transform: scale(1.03);
        box-shadow: 0 16px 35px rgba(0,0,0,0.12);
    }

    .news-card-img {
        height: 190px;
        background: linear-gradient(135deg, #1e3b2c, #0b2e22);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
    }

    .news-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    /* === GALLERY === */
    .gallery-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 14px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .gallery-img:hover {
        transform: scale(1.04);
    }
</style>
@endpush

@section('content')
<!-- HERO SECTION -->
<header class="hero-home">
    <div class="hero-overlay">
        <span class="badge bg-warning text-dark px-3 py-2 text-uppercase fw-bold mb-3 shadow">
            Selamat Datang di Website Resmi
        </span>
        <h1 class="hero-title-main">KOTO MALINTANG</h1>
        <p class="hero-subtitle-main">Kecamatan Tanjung Raya, Kabupaten Agam, Sumatera Barat</p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="{{ route('profil-nagari') }}" class="btn btn-success btn-lg rounded-pill px-4 shadow">
                <i class="bi bi-info-circle me-1"></i> Profil Nagari
            </a>
            <a href="{{ route('kaba-nagari') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 shadow">
                <i class="bi bi-newspaper me-1"></i> Kaba Terkini
            </a>
            <a href="{{ route('objek-wisata') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 shadow">
                <i class="bi bi-compass me-1"></i> Objek Wisata
            </a>
        </div>
    </div>
</header>

<!-- STATISTIK RINGKAS -->
<section class="container py-5 my-3">
    <div class="row g-4 text-center">
        <div class="col-6 col-lg-3">
            <div class="stats-card">
                <i class="bi bi-people-fill fs-2 mb-2 d-block"></i>
                <div class="stats-number">{{ number_format($profil->jumlah_penduduk ?? 2500, 0, ',', '.') }}</div>
                <div class="small fw-semibold">Jumlah Penduduk (Jiwa)</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stats-card">
                <i class="bi bi-geo-alt-fill fs-2 mb-2 d-block"></i>
                <div class="stats-number">{{ $profil->jumlah_jorong ?? 8 }}</div>
                <div class="small fw-semibold">Wilayah Jorong</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stats-card">
                <i class="bi bi-tree-fill fs-2 mb-2 d-block"></i>
                <div class="stats-number">{{ $profil->luas_wilayah ?? 45 }}</div>
                <div class="small fw-semibold">Luas Wilayah (km²)</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stats-card">
                <i class="bi bi-person-vcard-fill fs-2 mb-2 d-block"></i>
                <div class="stats-number">{{ $totalPerangkat ?? 13 }}</div>
                <div class="small fw-semibold">Perangkat Nagari</div>
            </div>
        </div>
    </div>
</section>

<!-- KABA NAGARI TERBARU -->
<section class="container py-4 mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark text-uppercase mb-2">Kaba Nagari Terkini</h2>
        <p class="text-muted">Informasi, agenda kegiatan, dan berita seputar Nagari Koto Malintang</p>
        <div class="mx-auto" style="width: 50px; height: 3px; background-color: #198754; border-radius: 2px;"></div>
    </div>

    @if ($kabaTerbaru && $kabaTerbaru->count() > 0)
        <div class="row g-4">
            @foreach ($kabaTerbaru as $kaba)
                <div class="col-md-4">
                    <div class="news-card">
                        <div class="news-card-img">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <div class="news-card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $kaba->kategori }}</span>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $kaba->tanggal ? \Carbon\Carbon::parse($kaba->tanggal)->translatedFormat('d M Y') : '' }}</small>
                            </div>
                            <h5 class="fw-bold text-dark mb-2" style="font-size: 1.05rem; line-height: 1.4;">
                                {{ Str::limit($kaba->judul, 70) }}
                            </h5>
                            <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                                {{ Str::limit($kaba->ringkasan, 110) }}
                            </p>
                            <a href="{{ route('kaba-nagari') }}" class="btn btn-sm btn-outline-success rounded-pill mt-auto fw-semibold">
                                Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<!-- PESONA POTENSI NAGARI (GALERI & WISATA) -->
<section class="container py-4 mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark text-uppercase mb-2">Pesona & Wisata Nagari</h2>
        <p class="text-muted">Eksotisme Danau Maninjau dan Kekayaan Alam Nagari Koto Malintang</p>
        <div class="mx-auto" style="width: 50px; height: 3px; background-color: #198754; border-radius: 2px;"></div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <img src="{{ asset('image/forest1.jpg') }}" alt="Hutan & Alam" class="gallery-img" />
                <div class="p-3 text-center">
                    <h6 class="fw-bold text-dark mb-1">Kayu Gadang & Hutan Asri</h6>
                    <small class="text-muted">Penerima Kalpataru Nasional</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <img src="{{ asset('image/forest2.jpg') }}" alt="Panorama Maninjau" class="gallery-img" />
                <div class="p-3 text-center">
                    <h6 class="fw-bold text-dark mb-1">Panorama Danau Maninjau</h6>
                    <small class="text-muted">Kecamatan Tanjung Raya, Agam</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <img src="{{ asset('image/bg2.jpg') }}" alt="Koto Malintang" class="gallery-img" />
                <div class="p-3 text-center">
                    <h6 class="fw-bold text-dark mb-1">Taman Wisata Muko-Muko</h6>
                    <small class="text-muted">Destinasi Favorit Keluarga</small>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
