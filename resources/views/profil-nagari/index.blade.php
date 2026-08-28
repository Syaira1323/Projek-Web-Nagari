@extends('layouts.app')

@section('title', 'Profil Nagari')

@push('styles')
<style>
    body {
        background-color: #0b2e22;
        color: white;
        padding-top: 70px;
    }

    /* Hero */
    .hero-profil {
        background: url("{{ !empty($profil->foto_utama) ? asset($profil->foto_utama) : asset('image/forest1.jpg') }}") no-repeat center center / cover;
        position: relative;
        padding: 50px 0 30px;
        margin-bottom: 20px;
    }

    .hero-overlay {
        background-color: rgba(11, 46, 34, 0.78);
        padding: 30px 0;
    }

    /* Profil card */
    .profil-card {
        background-color: #102c1e !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    }

    .profil-image {
        object-fit: cover;
        height: 100%;
        width: 100%;
        min-height: 400px;
    }

    .profil-content {
        background-color: #1e3b2c;
        padding: 2.5rem;
    }

    .profil-content h5 {
        color: #81c784;
        letter-spacing: 1.5px;
        font-weight: 700;
    }

    .profil-content h2 {
        font-weight: bold;
        color: white;
        margin-bottom: 1.25rem;
    }

    .profil-content .uraian-text {
        color: #e0e6ed;
        font-size: 1.02rem;
        line-height: 1.8;
        white-space: pre-line;
        text-align: justify;
    }

    /* Stats */
    .stats-section {
        background: linear-gradient(135deg, #1e3b2c, #0b2e22);
        padding: 50px 0;
        margin-bottom: 40px;
    }

    .stat-item {
        text-align: center;
        padding: 1.75rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-6px);
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    .stat-icon {
        font-size: 2.5rem;
        color: #4caf50;
        margin-bottom: 0.5rem;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: bold;
        color: #4caf50;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #cfd8dc;
        font-size: 0.95rem;
        font-weight: 500;
    }

    /* Section Card Dark */
    .section-card-dark {
        background: linear-gradient(135deg, #1e3b2c, #102c1e);
        border-radius: 20px;
        padding: 2.25rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 40px;
    }

    .kegiatan-badge {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(76, 175, 80, 0.4);
        color: #e8f5e9;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
    }

    .kegiatan-badge:hover {
        background: rgba(76, 175, 80, 0.2);
        transform: translateX(4px);
    }

    /* Feature cards */
    .feature-cards {
        background: #0b2e22;
        padding: 20px 0 60px;
    }

    .feature-card {
        background: linear-gradient(135deg, #1e3b2c, #102c1e);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
    }

    .feature-icon {
        font-size: 3rem;
        color: #4caf50;
        margin-bottom: 1rem;
    }

    .feature-title {
        color: white;
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .feature-desc {
        color: #cfd8dc;
        font-size: 0.9rem;
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<!-- HERO PROFIL -->
<header class="hero-profil">
    <div class="hero-overlay">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="d-md-flex profil-card">
                        <!-- Gambar Samping -->
                        <div class="col-md-4 p-0">
                            @if (!empty($profil->foto_utama))
                                <img src="{{ asset($profil->foto_utama) }}" alt="{{ $profil->nama_nagari }}" class="profil-image" />
                            @else
                                <img src="{{ asset('image/forest2.jpg') }}" alt="Nagari Koto Malintang" class="profil-image" />
                            @endif
                        </div>
                        <!-- Konten Utama -->
                        <div class="col-md-8 profil-content">
                            <h5>PROFIL NAGARI</h5>
                            <h2>{{ $profil->nama_nagari ?? 'Nagari Koto Malintang' }}</h2>
                            
                            <div class="uraian-text mb-4">
                                {{ $adatBudaya->uraian ?? ($profil->deskripsi ?? 'Nagari Koto Malintang terletak di Kecamatan Tanjung Raya, Kabupaten Agam, Sumatera Barat.') }}
                            </div>

                            <div class="pt-3 border-top border-secondary">
                                <div class="row g-2 small">
                                    <div class="col-6 col-sm-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt-fill text-success me-2 fs-5"></i>
                                            <span class="text-light">Kec. {{ $profil->kecamatan ?? 'Tanjung Raya' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-people-fill text-success me-2 fs-5"></i>
                                            <span class="text-light">{{ number_format($profil->jumlah_penduduk ?? 2500, 0, ',', '.') }} Jiwa</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-house-fill text-success me-2 fs-5"></i>
                                            <span class="text-light">{{ $profil->jumlah_jorong ?? 8 }} Jorong</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-shield-check text-success me-2 fs-5"></i>
                                            <span class="text-light">{{ $adatBudaya->jumlah_suku ?? 7 }} Suku Kaum</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- STATS SECTION -->
<section class="stats-section">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-number">{{ number_format($profil->jumlah_penduduk ?? 2500, 0, ',', '.') }}</div>
                    <div class="stat-label">Jumlah Penduduk (Jiwa)</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <div class="stat-number">{{ $profil->jumlah_jorong ?? 8 }}</div>
                    <div class="stat-label">Jorong Administratif</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-map-fill"></i></div>
                    <div class="stat-number">{{ $profil->luas_wilayah ?? 45 }}</div>
                    <div class="stat-label">Luas Wilayah (km²)</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                    <div class="stat-number">{{ $adatBudaya->jumlah_suku ?? 7 }}</div>
                    <div class="stat-label">Suku / Kaum Adat</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SUKU KAUM & KEGIATAN ADAT SECTION -->
@if ($adatBudaya)
<div class="container mb-5">
    <div class="section-card-dark">
        <div class="row g-4 align-items-center">
            <!-- Jumlah Suku -->
            <div class="col-md-4">
                <div class="p-4 rounded-3 text-center h-100" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <i class="bi bi-people-fill text-warning fs-1 mb-2 d-block"></i>
                    <h3 class="fw-bold text-warning display-6 mb-1">{{ $adatBudaya->jumlah_suku ?? 7 }} Suku</h3>
                    <p class="small text-light mb-0">Dipimpin oleh Ninik Mamak dengan prinsip adat Minangkabau <em>"Duduak samo randah, tagak samo tinggi"</em>.</p>
                </div>
            </div>

            <!-- Kegiatan Adat -->
            <div class="col-md-8">
                <h5 class="text-success fw-bold mb-3"><i class="bi bi-calendar-event me-2"></i>Tradisi & Kegiatan Sepanjang Adat:</h5>
                @php
                    $kegiatan = is_array($adatBudaya->kegiatan_adat) 
                        ? $adatBudaya->kegiatan_adat 
                        : json_decode($adatBudaya->kegiatan_adat, true);
                @endphp
                @if ($kegiatan && count($kegiatan) > 0)
                    <div class="row g-2">
                        @foreach ($kegiatan as $item)
                            <div class="col-sm-6">
                                <div class="kegiatan-badge">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>{{ $item }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- KEUNGGULAN NAGARI SECTION -->
<section class="feature-cards">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-white mb-2">Keunggulan Nagari</h2>
            <p class="text-white text-opacity-75">Hal yang Menjadikan Nagari Koto Malintang Istimewa & Berkelanjutan</p>
            <div class="mx-auto" style="width: 50px; height: 3px; background-color: #4caf50; border-radius: 2px;"></div>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-tree-fill"></i></div>
                    <div class="feature-title">Alam Asri</div>
                    <div class="feature-desc">Dikelilingi perbukitan hijau, pohon binuang raksasa, dan danau yang mempesona.</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="feature-title">Masyarakat Ramah</div>
                    <div class="feature-desc">Penduduk yang hangat, menjunjung tinggi kekeluargaan dan semangat gotong royong.</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-house-heart-fill"></i></div>
                    <div class="feature-title">Budaya Kuat</div>
                    <div class="feature-desc">Falsafah ABS-SBK dan tatanan adat salingka nagari yang lestari turun-temurun.</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="feature-title">Nagari Mandiri</div>
                    <div class="feature-desc">Pertumbuhan potensi UMKM dan pelayanan digital terdepan untuk kesejahteraan bersama.</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
