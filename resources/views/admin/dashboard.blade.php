@extends('admin.layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Ringkasan Dashboard Nagari')

@section('content')
<!-- STATS ROW -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card-custom mb-0 p-4 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Surat Masuk</span>
                <i class="bi bi-hourglass-split text-warning fs-4"></i>
            </div>
            <h3 class="fw-bold text-dark mb-0">{{ $suratPending }}</h3>
            <small class="text-warning fw-semibold">Menunggu Verifikasi</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-custom mb-0 p-4 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Di Wali Nagari</span>
                <i class="bi bi-pen-fill text-info fs-4"></i>
            </div>
            <h3 class="fw-bold text-dark mb-0">{{ $suratDiWali }}</h3>
            <small class="text-info fw-semibold">Siap Dibubuhi Stempel</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-custom mb-0 p-4 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Surat Selesai</span>
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
            </div>
            <h3 class="fw-bold text-dark mb-0">{{ $suratSelesai }}</h3>
            <small class="text-success fw-semibold">Telah Berstempel Resmi</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-custom mb-0 p-4 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Akun Warga</span>
                <i class="bi bi-people-fill text-primary fs-4"></i>
            </div>
            <h3 class="fw-bold text-dark mb-0">{{ $totalWarga }}</h3>
            <small class="text-primary fw-semibold">Masyarakat Terdaftar</small>
        </div>
    </div>
</div>

<!-- SURAT MASUK TERBARU & QUICK ACTIONS -->
<div class="row g-4 mb-4">
    <!-- Tabel Surat Masuk -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-inbox-fill text-success me-2"></i>Permohonan Surat Terbaru</h5>
                <a href="{{ route('admin.surat.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Lihat Semua</a>
            </div>

            @if ($suratMasukTerbaru->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th>PEMOHON / NIK</th>
                                <th>JENIS SURAT</th>
                                <th>STATUS</th>
                                <th class="text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suratMasukTerbaru as $surat)
                                <tr>
                                    <td>
                                        <strong class="text-dark d-block">{{ $surat->nama_pemohon }}</strong>
                                        <small class="text-muted">NIK: {{ $surat->nik_pemohon }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark d-block">{{ $surat->nama_jenis_surat }}</span>
                                        <small class="text-muted">{{ $surat->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $surat->status_badge_color }} rounded-pill px-3 py-1 small">
                                            {{ $surat->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.surat.show', $surat->id) }}" class="btn btn-sm btn-success rounded-pill px-3">
                                            Periksa <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-all fs-1 text-success d-block mb-2"></i>
                    Belum ada antrean permohonan surat baru.
                </div>
            @endif
        </div>
    </div>

    <!-- Ringkasan Konten CMS Website -->
    <div class="col-lg-4">
        <div class="card-custom">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-sliders text-success me-2"></i>Status Data CMS</h5>

            <div class="list-group list-group-flush mb-3">
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="text-muted"><i class="bi bi-newspaper me-2 text-primary"></i>Kaba Nagari (Berita)</span>
                    <span class="badge bg-primary rounded-pill">{{ $totalKaba }} Post</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="text-muted"><i class="bi bi-shop me-2 text-warning"></i>Katalog UMKM</span>
                    <span class="badge bg-warning text-dark rounded-pill">{{ $totalUmkm }} Usaha</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="text-muted"><i class="bi bi-compass me-2 text-success"></i>Objek Wisata</span>
                    <span class="badge bg-success rounded-pill">{{ $totalWisata }} Lokasi</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="text-muted"><i class="bi bi-people me-2 text-info"></i>Perangkat Nagari</span>
                    <span class="badge bg-info text-dark rounded-pill">{{ $totalPerangkat }} Pejabat</span>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('admin.konten.profil') }}" class="btn btn-light border btn-sm text-start">
                    <i class="bi bi-pencil-square text-success me-2"></i> Edit Profil Nagari & Statistik
                </a>
                <a href="{{ route('admin.konten.kaba') }}" class="btn btn-light border btn-sm text-start">
                    <i class="bi bi-plus-circle text-primary me-2"></i> Buat Kaba Nagari Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
