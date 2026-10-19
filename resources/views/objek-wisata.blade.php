@extends('layouts.app')

@section('title', 'Objek Wisata')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 70px;
    }

    .hero-wisata {
        background: linear-gradient(135deg, #198754 0%, #04543a 100%);
        color: white;
        padding: 90px 0 60px;
        position: relative;
        text-align: center;
        margin-bottom: 40px;
    }

    .hero-wisata h1 {
        font-size: 3.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .wisata-card {
        background: white;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .wisata-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 35px rgba(0,0,0,0.14);
    }

    .wisata-img-wrapper {
        height: 220px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #1e3b2c, #0b2e22);
    }

    .wisata-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .wisata-card:hover .wisata-img-wrapper img {
        transform: scale(1.08);
    }

    .wisata-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.95);
        color: #198754;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 6px 14px;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-wisata">
    <div class="container">
        <h1>Destinasi Wisata Nagari</h1>
        <p class="lead text-light mb-0">Pesona Keindahan Alam, Budaya, dan Religi di Nagari Koto Malintang</p>
    </div>
</section>

<div class="container mb-5">
    @if ($objekWisatas->count() > 0)
        <div class="row g-4">
            @foreach ($objekWisatas as $wisata)
                <div class="col-md-6 col-lg-4">
                    <div class="wisata-card">
                        <!-- Gambar -->
                        <div class="wisata-img-wrapper">
                            @php
                                $wisataImg = null;
                                if ($wisata->gambar) {
                                    if (file_exists(public_path($wisata->gambar))) {
                                        $wisataImg = asset($wisata->gambar);
                                    } elseif (file_exists(public_path('image/' . $wisata->gambar))) {
                                        $wisataImg = asset('image/' . $wisata->gambar);
                                    }
                                }
                            @endphp
                            @if ($wisataImg)
                                <img src="{{ $wisataImg }}" alt="{{ $wisata->nama }}" />
                            @else
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white text-opacity-75 p-3 text-center">
                                    <i class="bi bi-tree fs-1 mb-1"></i>
                                    <span class="small fw-semibold">{{ $wisata->nama }}</span>
                                </div>
                            @endif
                            <span class="wisata-badge">
                                <i class="bi bi-tag-fill me-1"></i> {{ $wisata->kategori ?? 'Wisata Alam' }}
                            </span>
                        </div>

                        <!-- Konten Card -->
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <h4 class="fw-bold text-dark fs-5 mb-2">{{ $wisata->nama }}</h4>
                            <div class="d-flex align-items-center text-muted small mb-3">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <span>{{ $wisata->lokasi ?? 'Nagari Koto Malintang' }}</span>
                            </div>

                            <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                                {{ Str::limit($wisata->deskripsi, 160, '...') }}
                            </p>

                            <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalWisata{{ $wisata->id }}">
                                    <i class="bi bi-info-circle me-1"></i> Detail Wisata
                                </button>
                                @if ($wisata->google_maps_url)
                                    <a href="{{ $wisata->google_maps_url }}" target="_blank" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-map me-1"></i> Peta
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="modal fade" id="modalWisata{{ $wisata->id }}" tabindex="-1" aria-labelledby="label{{ $wisata->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header bg-success text-white p-4">
                                <div>
                                    <span class="badge bg-warning text-dark mb-2">{{ $wisata->kategori }}</span>
                                    <h4 class="modal-title fw-bold" id="label{{ $wisata->id }}">{{ $wisata->nama }}</h4>
                                    <small class="text-white text-opacity-75"><i class="bi bi-geo-alt-fill me-1"></i>{{ $wisata->lokasi }}</small>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                @if ($wisataImg)
                                    <div class="mb-3 rounded-3 overflow-hidden shadow-sm" style="max-height: 280px;">
                                        <img src="{{ $wisataImg }}" alt="{{ $wisata->nama }}" class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                @endif
                                <h6 class="fw-bold text-dark mb-2">Deskripsi Lengkap:</h6>
                                <p class="text-muted lh-base" style="white-space: pre-line;">{{ $wisata->deskripsi }}</p>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                @if ($wisata->google_maps_url)
                                    <a href="{{ $wisata->google_maps_url }}" target="_blank" class="btn btn-warning fw-semibold rounded-pill px-4">
                                        <i class="bi bi-geo-alt-fill me-1"></i> Buka di Google Maps
                                    </a>
                                @endif
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-4">
            Data Objek Wisata belum tersedia.
        </div>
    @endif
</div>
@endsection
