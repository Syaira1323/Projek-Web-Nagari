@extends('layouts.app')

@section('title', 'UMKM Nagari')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 70px;
    }

    .hero-umkm {
        background: linear-gradient(135deg, #198754 0%, #04543a 100%);
        color: white;
        padding: 90px 0 60px;
        position: relative;
        text-align: center;
        margin-bottom: 40px;
    }

    .hero-umkm h1 {
        font-size: 3.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .umkm-card {
        background: white;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .umkm-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 35px rgba(0,0,0,0.12);
    }

    .umkm-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-umkm">
    <div class="container">
        <h1>UMKM Nagari Koto Malintang</h1>
        <p class="lead text-light mb-0">Pemberdayaan Usaha Mikro, Kecil, dan Menengah Potensi Lokal Nagari</p>
    </div>
</section>

<div class="container mb-5">
    @if ($umkms->count() > 0)
        <div class="row g-4">
            @foreach ($umkms as $umkm)
                <div class="col-md-6 col-lg-6">
                    <div class="umkm-card">
                        @php
                            $umkmImg = null;
                            if ($umkm->foto) {
                                if (file_exists(public_path($umkm->foto))) {
                                    $umkmImg = asset($umkm->foto);
                                } elseif (file_exists(public_path('image/' . $umkm->foto))) {
                                    $umkmImg = asset('image/' . $umkm->foto);
                                }
                            }
                        @endphp
                        @if ($umkmImg)
                            <div class="mb-3 rounded-3 overflow-hidden" style="height: 160px;">
                                <img src="{{ $umkmImg }}" alt="{{ $umkm->nama_usaha }}" style="width: 100%; height: 100%; object-fit: cover;" />
                            </div>
                        @endif

                        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="umkm-icon">
                                    @if ($umkm->kategori == 'Kuliner')
                                        <i class="bi bi-cup-hot-fill"></i>
                                    @elseif ($umkm->kategori == 'Kerajinan')
                                        <i class="bi bi-palette-fill"></i>
                                    @else
                                        <i class="bi bi-shop"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="fw-bold text-dark fs-5 mb-1">{{ $umkm->nama_usaha }}</h4>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-tag-fill me-1"></i> {{ $umkm->kategori }}
                                    </span>
                                </div>
                            </div>
                            @if ($umkm->tahun_berdiri)
                                <span class="badge bg-light text-muted border py-2 px-3 small">
                                    Est. {{ $umkm->tahun_berdiri }}
                                </span>
                            @endif
                        </div>

                        <!-- Deskripsi -->
                        <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                            {{ $umkm->deskripsi }}
                        </p>

                        <!-- Info Detail -->
                        <div class="bg-light p-3 rounded-3 mb-4 small">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Pemilik / Pengelola:</span>
                                    <strong class="text-dark"><i class="bi bi-person-fill text-success me-1"></i>{{ $umkm->pemilik }}</strong>
                                </div>
                                @if ($umkm->jam_operasional)
                                    <div class="col-sm-6">
                                        <span class="text-muted d-block">Jam Operasional:</span>
                                        <strong class="text-dark"><i class="bi bi-clock-fill text-warning me-1"></i>{{ $umkm->jam_operasional }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Links -->
                        <div class="pt-3 border-top mt-auto d-flex flex-wrap gap-2 justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                @if ($umkm->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $umkm->whatsapp) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
                                    </a>
                                @endif

                                @if ($umkm->instagram)
                                    <a href="{{ Str::startsWith($umkm->instagram, 'http') ? $umkm->instagram : 'https://instagram.com/' . ltrim($umkm->instagram, '@') }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-instagram me-1"></i> Instagram
                                    </a>
                                @endif
                            </div>

                            @if ($umkm->google_maps_url)
                                <a href="{{ $umkm->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> Peta Lokasi
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-4">
            Data UMKM belum tersedia.
        </div>
    @endif
</div>
@endsection
