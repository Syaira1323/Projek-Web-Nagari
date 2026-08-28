@extends('layouts.app')

@section('title', 'Visi & Misi')

@push('styles')
<style>
    body {
        background-image: url("{{ asset('image/forest2.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        min-height: 100vh;
        padding-top: 70px;
    }

    /* === MAIN CONTENT STYLING === */
    .main-content {
        background: linear-gradient(135deg, rgba(0,0,0,0.85), rgba(0,0,0,0.7));
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        margin-top: 30px;
        margin-bottom: 50px;
        border: 1px solid rgba(255,255,255,0.1);
        position: relative;
        overflow: hidden;
    }

    .main-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(40, 167, 69, 0.1), transparent);
        animation: shimmer 3s infinite;
        pointer-events: none;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* === VISI MISI CARDS === */
    .vision-mission-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 3rem;
        margin-top: 2rem;
    }

    .vision-card, .mission-card {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.15), rgba(32, 201, 151, 0.15));
        border-radius: 20px;
        padding: 2.5rem;
        border: 2px solid rgba(40, 167, 69, 0.3);
        position: relative;
        transition: all 0.4s ease;
        overflow: hidden;
    }

    .vision-card:hover, .mission-card:hover {
        transform: translateY(-8px);
        border-color: rgba(40, 167, 69, 0.6);
        box-shadow: 0 20px 40px rgba(40, 167, 69, 0.25);
    }

    .card-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin: 0 auto 1.5rem;
        transition: all 0.3s ease;
    }

    .vision-card:hover .card-icon, .mission-card:hover .card-icon {
        transform: rotateY(180deg) scale(1.1);
    }

    .card-title {
        color: #28a745;
        font-size: 2rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .card-content {
        color: white;
        font-size: 1.1rem;
        line-height: 1.8;
        position: relative;
        z-index: 2;
    }

    /* === MISSION LIST STYLING === */
    .mission-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mission-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        padding: 1.2rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        border-left: 4px solid #28a745;
        transition: all 0.3s ease;
    }

    .mission-item:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateX(10px);
    }

    .mission-number {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(40,167,69,0.3);
    }

    .mission-text {
        flex: 1;
        color: white;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 1.5rem;
            margin-top: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="main-content">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 text-white mb-2 fw-bold">
                <i class="bi bi-eye-fill text-success me-3"></i>
                VISI & MISI
            </h1>
            <p class="lead text-light">Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam</p>
            <div class="mt-3">
                <div style="width: 100px; height: 4px; background: linear-gradient(90deg, #28a745, #20c997); margin: 0 auto; border-radius: 2px;"></div>
            </div>
        </div>

        @if ($visi)
            <div class="vision-mission-container">
                <!-- VISI & MAKNA VISI -->
                <div class="vision-card">
                    <div class="card-icon">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <h2 class="card-title">{{ $visi->judul ?? 'VISI' }}</h2>
                    <div class="card-content">
                        <p class="text-center mb-4" style="font-size: 1.35rem; font-weight: 600; color: #20c997; line-height: 1.6;">
                            "{{ $visi->isi }}"
                        </p>
                        
                        <!-- Makna Visi -->
                        @if ($visi->maknaVisis && $visi->maknaVisis->count() > 0)
                            <div class="mt-5 pt-4 border-top border-secondary">
                                <h4 class="text-success mb-3 fw-bold"><i class="bi bi-stars me-2"></i>Makna Visi:</h4>
                                <div class="row g-4">
                                    @foreach ($visi->maknaVisis as $makna)
                                        @php
                                            $iconMap = [
                                                'arrow-up' => 'bi-arrow-up-circle-fill',
                                                'shield' => 'bi-shield-fill-check',
                                                'heart' => 'bi-heart-fill',
                                                'book' => 'bi-book-fill',
                                            ];
                                            $iconClass = $iconMap[$makna->icon] ?? 'bi-check-circle-fill';
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.05);">
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="bi {{ $iconClass }} text-success fs-5 me-2"></i>
                                                    <strong class="text-warning fs-5">{{ $makna->istilah }}:</strong>
                                                </div>
                                                <div class="text-light ms-4">{{ $makna->deskripsi }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- MISI -->
                @if ($visi->misis && $visi->misis->count() > 0)
                    <div class="mission-card">
                        <div class="card-icon">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <h2 class="card-title">MISI</h2>
                        <div class="card-content">
                            <ul class="mission-list">
                                @foreach ($visi->misis as $misi)
                                    <li class="mission-item">
                                        <div class="mission-number">{{ $misi->nomor }}</div>
                                        <div class="mission-text">
                                            <strong class="text-success fs-5 d-block mb-1">{{ $misi->judul }}</strong>
                                            <span class="text-light">{{ $misi->deskripsi }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="alert alert-info text-center py-4 text-white bg-dark">
                Data Visi dan Misi belum tersedia.
            </div>
        @endif
    </div>
</div>
@endsection
