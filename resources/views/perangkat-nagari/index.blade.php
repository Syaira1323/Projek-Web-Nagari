@extends('layouts.app')

@section('title', 'Perangkat Nagari')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 70px;
    }

    /* === HERO SECTION === */
    .hero-perangkat {
        background: linear-gradient(135deg, #1e7226 0%, #249065 100%);
        position: relative;
        overflow: hidden;
        margin-bottom: 50px;
    }

    .hero-perangkat::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("{{ asset('image/forest2.jpg') }}") center/cover;
        opacity: 0.15;
        z-index: 1;
    }

    .hero-overlay {
        position: relative;
        z-index: 2;
        padding: 80px 0 50px;
    }

    .hero-title {
        font-size: 3.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.2;
        color: white;
    }

    .hero-subtitle {
        font-size: 1.15rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        color: white;
    }

    .hero-stats {
        display: flex;
        gap: 2rem;
        margin-top: 2rem;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        color: #ffc107;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: white;
        opacity: 0.85;
        margin: 0;
    }

    /* === PERANGKAT CARDS === */
    .perangkat-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: row;
        align-items: stretch;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
    }

    .perangkat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 35px rgba(0,0,0,0.12);
    }

    .position-badge {
        background: #28a745;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .profile-image {
        flex: 0 0 140px;
        width: 140px;
        overflow: hidden;
        background: #e8f5e9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        transition: transform 0.3s ease;
    }

    .perangkat-card:hover .profile-image img {
        transform: scale(1.08);
    }

    .card-content {
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex: 1 1 auto;
    }

    .perangkat-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .perangkat-role {
        color: #16a34a;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 12px;
    }

    .perangkat-info {
        font-size: 0.825rem;
        color: #6b7280;
        margin-bottom: 4px;
    }

    @media (max-width: 576px) {
        .perangkat-card {
            flex-direction: column;
        }
        .profile-image {
            width: 100%;
            height: 180px;
        }
    }
</style>
@endpush

@section('content')
<!-- HERO SECTION -->
<section class="hero-perangkat">
    <div class="hero-overlay">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h1 class="hero-title">Perangkat Nagari Koto Malintang</h1>
                    <p class="hero-subtitle">
                        Aparatur Pemerintah Nagari yang berdedikasi, transparan, dan siap melayani masyarakat dengan sepenuh hati.
                    </p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number">{{ $perangkatNagaris->count() }}</div>
                            <div class="stat-label">Total Perangkat</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $perangkatNagaris->where('jenis_kelamin', 'Laki-laki')->count() }}</div>
                            <div class="stat-label">Laki-laki</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $perangkatNagaris->where('jenis_kelamin', 'Perempuan')->count() }}</div>
                            <div class="stat-label">Perempuan</div>
                        </div>
                    </div>
                </div>

                @php
                    $wali = $perangkatNagaris->firstWhere('jabatan', 'Wali Nagari');
                @endphp
                @if ($wali)
                    <div class="col-lg-5 text-center">
                        <div class="card p-4 rounded-4 shadow-lg border-0 text-dark" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(5px);">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success text-white shadow" style="width: 70px; height: 70px; font-size: 2rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="badge bg-success text-white rounded-pill px-3 py-1 mx-auto mb-2 small fw-bold">PIMPINAN NAGARI</span>
                            <h4 class="fw-bold mb-1">{{ $wali->nama }}</h4>
                            <p class="text-success fw-semibold mb-2">{{ $wali->jabatan }}</p>
                            <small class="text-muted">Kecamatan Tanjung Raya, Kabupaten Agam</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- DAFTAR KARTU PERANGKAT NAGARI -->
<section class="container mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark mb-2">Susunan Aparatur Nagari</h2>
        <p class="text-muted">Daftar pejabat dan pengelola urusan pemerintahan Nagari Koto Malintang</p>
        <div class="mx-auto" style="width: 50px; height: 3px; background-color: #198754; border-radius: 2px;"></div>
    </div>

    <div class="row g-4">
        @foreach ($perangkatNagaris as $perangkat)
            <div class="col-md-6 col-lg-6">
                <div class="perangkat-card">
                    <!-- Foto / Avatar -->
                    <div class="profile-image">
                        @php
                            $fotoPath = null;
                            if ($perangkat->foto) {
                                if (file_exists(public_path($perangkat->foto))) {
                                    $fotoPath = asset($perangkat->foto);
                                } elseif (file_exists(public_path('image/' . $perangkat->foto))) {
                                    $fotoPath = asset('image/' . $perangkat->foto);
                                }
                            }
                        @endphp
                        @if ($fotoPath)
                            <img src="{{ $fotoPath }}" alt="{{ $perangkat->nama }}" />
                        @else
                            <div class="text-success text-center p-3">
                                <i class="bi bi-person-circle fs-1 d-block mb-1"></i>
                                <span class="badge bg-success bg-opacity-10 text-success small">{{ $perangkat->jenis_kelamin ?? 'Aparatur' }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Konten -->
                    <div class="card-content">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <span class="position-badge">{{ $perangkat->jabatan }}</span>
                        </div>
                        <h4 class="perangkat-name mt-2">{{ $perangkat->nama }}</h4>
                        <div class="perangkat-role">{{ $perangkat->jabatan }}</div>

                        <div class="perangkat-info">
                            <i class="bi bi-geo-alt text-danger me-1"></i>
                            <span>{{ $perangkat->tempat_lahir ?: '-' }}, {{ $perangkat->tanggal_lahir ? $perangkat->tanggal_lahir->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="perangkat-info">
                            <i class="bi bi-mortarboard text-primary me-1"></i>
                            <span>Pendidikan: {{ $perangkat->pendidikan_terakhir ?: '-' }}</span>
                        </div>
                        @if ($perangkat->nip && $perangkat->nip != '-')
                            <div class="perangkat-info">
                                <i class="bi bi-card-heading text-warning me-1"></i>
                                <span>NIP: {{ $perangkat->nip }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection