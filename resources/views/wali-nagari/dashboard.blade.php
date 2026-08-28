@extends('layouts.app')

@section('title', 'Dashboard Wali Nagari')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .wali-hero-banner {
        background: linear-gradient(135deg, #0d5c2e 0%, #1a8a4a 50%, #20c997 100%);
        border-radius: 24px;
        color: white;
        padding: 36px 32px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(13, 92, 46, 0.2);
    }

    .card-stat-wali {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        height: 100%;
        transition: all 0.25s ease;
    }

    .card-stat-wali:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }

    .panel-box {
        background: white;
        border-radius: 20px;
        border: 1px solid #e9ecef;
        padding: 26px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        height: 100%;
    }
</style>
@endpush

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Hero Banner Wali Nagari -->
    <div class="wali-hero-banner">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Panel Pimpinan Eksekutif</span>
                <h2 class="fw-bold mb-1">Pemerintah Nagari Koto Malintang</h2>
                <p class="text-white text-opacity-85 mb-0">
                    Selamat bertugas, <strong>{{ auth()->user()->name }}</strong>. Pantau data statistik nagari & pembubuhan stempel resmi digital permohonan surat masyarakat.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end d-flex flex-column gap-2 align-items-lg-end">
                <a href="{{ route('wali-nagari.surat.index') }}" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold text-dark shadow">
                    <i class="bi bi-pen-fill me-1"></i> Periksa Surat Masuk ({{ $suratMenungguStempel }})
                </a>
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalUbahPasswordWali">
                    <i class="bi bi-key-fill me-1 text-warning"></i> Ubah Password Wali Nagari
                </button>
            </div>
        </div>
    </div>

    <!-- ROW 1: STATISTIK UTAMA -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card-stat-wali border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Menunggu Stempel</span>
                    <i class="bi bi-stamp text-warning fs-3"></i>
                </div>
                <h2 class="fw-bold text-dark mb-0">{{ $suratMenungguStempel }}</h2>
                <small class="text-warning fw-semibold">Perlu TTD & Stempel</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-stat-wali border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Surat Terstempel</span>
                    <i class="bi bi-patch-check-fill text-success fs-3"></i>
                </div>
                <h2 class="fw-bold text-dark mb-0">{{ $suratSelesai }}</h2>
                <small class="text-success fw-semibold">Disetujui & Diterbitkan</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-stat-wali border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Warga Nagari</span>
                    <i class="bi bi-people-fill text-primary fs-3"></i>
                </div>
                <h2 class="fw-bold text-dark mb-0">{{ number_format($profil->jumlah_penduduk ?? 2500, 0, ',', '.') }}</h2>
                <small class="text-primary fw-semibold">{{ $profil->jumlah_jorong ?? 8 }} Jorong Administratif</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-stat-wali border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Potensi UMKM</span>
                    <i class="bi bi-shop text-danger fs-3"></i>
                </div>
                <h2 class="fw-bold text-dark mb-0">{{ $totalUmkm }}</h2>
                <small class="text-danger fw-semibold">Pelaku Usaha Terdaftar</small>
            </div>
        </div>
    </div>

    <!-- ROW 2: ANTREAN STEMPEL & STATISTIK 7 JENIS SURAT -->
    <div class="row g-4 mb-4">
        <!-- Antrean Surat Siap Stempel -->
        <div class="col-lg-7">
            <div class="panel-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-hourglass-top text-warning me-2"></i>Antrean Surat Menunggu Stempel
                    </h5>
                    <a href="{{ route('wali-nagari.surat.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Lihat Semua</a>
                </div>

                @if ($suratAntrean->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr class="small text-muted text-uppercase">
                                    <th>PEMOHON / NIK</th>
                                    <th>JENIS SURAT</th>
                                    <th>TANGGAL</th>
                                    <th class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suratAntrean as $surat)
                                    <tr>
                                        <td>
                                            <strong class="text-dark d-block">{{ $surat->nama_pemohon }}</strong>
                                            <small class="text-muted">NIK: {{ $surat->nik_pemohon }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark d-block">{{ $surat->nama_jenis_surat }}</span>
                                            <small class="text-muted">{{ Str::limit($surat->keperluan, 30) }}</small>
                                        </td>
                                        <td><small class="text-muted">{{ $surat->created_at->format('d/m/Y') }}</small></td>
                                        <td class="text-end">
                                            <a href="{{ route('wali-nagari.surat.show', $surat->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold text-dark shadow-sm">
                                                <i class="bi bi-pen me-1"></i> Beri Stempel
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle-fill fs-1 text-success d-block mb-2"></i>
                        <h6 class="fw-bold text-dark">Tidak Ada Antrean Stempel</h6>
                        <p class="small mb-0">Semua permohonan surat yang masuk telah diproses dan distempel.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistik 7 Jenis Surat -->
        <div class="col-lg-5">
            <div class="panel-box">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-bar-chart-line-fill text-success me-2"></i>Statistik 7 Layanan Surat
                </h5>

                <div class="list-group list-group-flush">
                    @foreach ($statistikJenisSurat as $key => $stat)
                        <div class="list-group-item px-0 py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small text-dark">{{ $stat['nama'] }} ({{ $stat['singkatan'] }})</span>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $stat['disetujui'] }} / {{ $stat['total'] }} Selesai</span>
                            </div>
                            @php
                                $percent = $stat['total'] > 0 ? round(($stat['disetujui'] / $stat['total']) * 100) : 0;
                            @endphp
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
    </div>
</div>

<!-- MODAL UBAH PASSWORD WALI NAGARI -->
<div class="modal fade" id="modalUbahPasswordWali" tabindex="-1" aria-labelledby="modalUbahPasswordWaliLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold" id="modalUbahPasswordWaliLabel">
                        <i class="bi bi-shield-lock text-warning me-2"></i>Ubah Password Akun Wali Nagari
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Password Lama Saat Ini <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" placeholder="Masukkan password lama Anda" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru" required />
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">Simpan Password Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
