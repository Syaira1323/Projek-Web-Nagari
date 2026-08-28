@extends('layouts.app')

@section('title', 'Adat & Budaya Nagari')

@push('styles')
<style>
    body {
        background-color: #0b2e22;
        color: white;
        padding-top: 70px;
    }

    .hero-adat {
        background: url("{{ !empty($adatBudaya->gambar) ? asset($adatBudaya->gambar) : asset('image/forest1.jpg') }}") no-repeat center center / cover;
        position: relative;
        padding: 60px 0;
        margin-bottom: 40px;
    }

    .hero-overlay {
        background-color: rgba(11, 46, 34, 0.8);
        padding: 40px 0;
    }

    .section-card-dark {
        background: linear-gradient(135deg, #1e3b2c, #102c1e);
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .kegiatan-badge {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(76, 175, 80, 0.4);
        color: #e8f5e9;
        padding: 14px 18px;
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
</style>
@endpush

@section('content')
<!-- HERO ADAT & BUDAYA -->
<header class="hero-adat">
    <div class="hero-overlay">
        <div class="container text-center">
            <span class="badge bg-warning text-dark px-3 py-2 text-uppercase fw-bold mb-2">Adat Basandi Syarak, Syarak Basandi Kitabullah</span>
            <h1 class="display-4 fw-bold text-white mb-2">Adat & Budaya Nagari</h1>
            <p class="lead text-light mb-0">Tatanan Kehidupan Berkelarasan dan Warisan Leluhur Nagari Koto Malintang</p>
        </div>
    </div>
</header>

<div class="container mb-5">
    @if ($adatBudaya)
        <!-- Falsafah & Uraian Adat -->
        <div class="section-card-dark">
            <div class="row g-4 align-items-center">
                @if (!empty($adatBudaya->gambar))
                    <div class="col-md-5">
                        <img src="{{ asset($adatBudaya->gambar) }}" alt="Adat Budaya" class="img-fluid rounded-4 shadow border border-success border-opacity-25" />
                    </div>
                @endif
                <div class="{{ !empty($adatBudaya->gambar) ? 'col-md-7' : 'col-12' }}">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white shadow" style="width: 50px; height: 50px; font-size: 1.4rem;">
                            <i class="bi bi-feather"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold text-white mb-0">Tatanan Adat & Kehidupan Bermasyarakat</h3>
                            <small class="text-success fw-bold">Syara' Mangato Adat Mamakai</small>
                        </div>
                    </div>

                    <div class="text-light lh-lg" style="white-space: pre-line; font-size: 1.02rem; color: #e2e8f0 !important;">
                        {{ $adatBudaya->uraian }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Jumlah Suku -->
            <div class="col-lg-4">
                <div class="section-card-dark text-center h-100 mb-0">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-20 text-warning" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-people-fill text-warning"></i>
                    </div>
                    <h2 class="fw-bold text-warning display-5 mb-1">{{ $adatBudaya->jumlah_suku ?? 7 }}</h2>
                    <h5 class="fw-bold text-white mb-3">Suku / Kaum Adat</h5>
                    <p class="text-light small mb-0 opacity-75">
                        Tercatat Suku Tanjuang, Sikumbang, Malayu, dan lainnya yang dipimpin ninik mamak kepala kaum tanpa berpindah ke kaum lain.
                    </p>
                </div>
            </div>

            <!-- Kegiatan Adat -->
            <div class="col-lg-8">
                <div class="section-card-dark h-100 mb-0">
                    <h4 class="fw-bold text-success mb-3"><i class="bi bi-calendar-check me-2"></i>Daftar Kegiatan & Tradisi Adat Nagari:</h4>
                    @php
                        $kegiatan = is_array($adatBudaya->kegiatan_adat) 
                            ? $adatBudaya->kegiatan_adat 
                            : json_decode($adatBudaya->kegiatan_adat, true);
                    @endphp

                    @if ($kegiatan && count($kegiatan) > 0)
                        <div class="row g-3">
                            @foreach ($kegiatan as $item)
                                <div class="col-sm-6">
                                    <div class="kegiatan-badge">
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        <span class="fw-semibold">{{ $item }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-4 bg-dark text-white">
            Data Adat dan Budaya belum tersedia.
        </div>
    @endif
</div>
@endsection
