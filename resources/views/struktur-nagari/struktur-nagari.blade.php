@extends('layouts.app')

@section('title', 'Struktur Nagari')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 70px;
    }

    .hero-struktur {
        background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), url("{{ asset('image/bg2.jpg') }}");
        background-size: cover;
        background-position: center;
        min-height: 45vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        margin-bottom: 40px;
    }

    .org-box {
        background: white;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        text-align: center;
    }

    .org-box:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }

    .org-box.walinagari {
        background: linear-gradient(135deg, #198754, #04543a);
        color: white;
    }

    .org-box.sekretaris {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
    }

    .org-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin: 0 auto 16px;
        background: rgba(255,255,255,0.2);
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-struktur">
    <div class="container py-5">
        <h1 class="display-4 fw-bold mb-2">Struktur Nagari Koto Malintang</h1>
        <p class="lead mb-0 text-white-50">Bagan Susunan Kepengurusan dan Tata Kerja Pemerintahan Nagari</p>
    </div>
</section>

<div class="container mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark mb-2">Bagan Tata Kerja Organisasi</h2>
        <p class="text-muted">Struktur pembagian tugas dan fungsi di lingkungan Pemerintah Nagari Koto Malintang</p>
        <div class="mx-auto" style="width: 50px; height: 3px; background-color: #198754; border-radius: 2px;"></div>
    </div>

    @if ($strukturNagari->count() > 0)
        <div class="row g-4 justify-content-center">
            @foreach ($strukturNagari as $item)
                @php
                    $isWali = str_contains(strtolower($item->nama), 'wali nagari') && !str_contains(strtolower($item->nama), 'jorong');
                    $isSekretaris = str_contains(strtolower($item->nama), 'sekretaris');
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="org-box {{ $isWali ? 'walinagari' : ($isSekretaris ? 'sekretaris' : '') }}">
                        @if (!empty($item->foto_bagan))
                            <div class="mb-3">
                                <img src="{{ asset($item->foto_bagan) }}" alt="{{ $item->nama }}" class="img-fluid rounded-3" style="max-height: 140px; object-fit: cover;" />
                            </div>
                        @else
                            <div class="org-icon">
                                @if ($isWali)
                                    <i class="bi bi-person-fill-check"></i>
                                @elseif ($isSekretaris)
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                @elseif (str_contains(strtolower($item->nama), 'kaur'))
                                    <i class="bi bi-briefcase-fill text-success"></i>
                                @elseif (str_contains(strtolower($item->nama), 'jorong'))
                                    <i class="bi bi-geo-alt-fill text-warning"></i>
                                @else
                                    <i class="bi bi-person-badge-fill text-primary"></i>
                                @endif
                            </div>
                        @endif

                        <span class="badge {{ $isWali || $isSekretaris ? 'bg-light text-dark' : 'bg-success bg-opacity-10 text-success' }} mb-2 px-3 py-1">
                            Jabatan Struktural
                        </span>

                        <h4 class="fw-bold fs-5 mb-2 {{ $isWali || $isSekretaris ? 'text-white' : 'text-dark' }}">
                            {{ $item->nama }}
                        </h4>

                        <p class="small mb-0 {{ $isWali || $isSekretaris ? 'text-white text-opacity-75' : 'text-muted' }}">
                            {{ $item->keterangan }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('perangkat-nagari') }}" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                <i class="bi bi-people me-2"></i> Lihat Pejabat & Perangkat Nagari Lengkap
            </a>
        </div>
    @else
        <div class="alert alert-info text-center py-4">
            Data Struktur Organisasi Nagari belum tersedia.
        </div>
    @endif
</div>
@endsection
