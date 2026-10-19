@extends('admin.layouts.admin')

@section('title', 'Dashboard Administrator')
@section('page-title', 'Dashboard Administrator')

@section('content')
<!-- STATS ROW 1: SURAT & WARGA -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between text-warning">
                <i class="bi bi-envelope-exclamation-fill fs-4"></i>
            </div>
            <div class="num text-warning">{{ $suratPending }}</div>
            <div class="lbl">Surat Perlu Verifikasi</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between text-primary">
                <i class="bi bi-send-check-fill fs-4"></i>
            </div>
            <div class="num text-primary">{{ $suratDiWali }}</div>
            <div class="lbl">Di Meja Wali Nagari</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between text-success">
                <i class="bi bi-archive-fill fs-4"></i>
            </div>
            <div class="num text-success">{{ $suratSelesai }}</div>
            <div class="lbl">Surat Selesai / Arsip</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between text-purple" style="color: #7c3aed;">
                <i class="bi bi-people-fill fs-4" style="color: #7c3aed;"></i>
            </div>
            <div class="num" style="color: #7c3aed;">{{ $totalWarga }}</div>
            <div class="lbl">Total Pengguna Warga</div>
        </div>
    </div>
</div>

<!-- SURAT MASUK TERBARU & STATUS DATA CMS -->
<div class="row g-4 mb-4">
    <!-- Tabel Permohonan Surat Terbaru -->
    <div class="col-lg-8">
        <div class="admin-panel">
            <div class="panel-header">
                <h6><i class="bi bi-inbox-fill text-success me-2"></i>Permohonan Surat Terbaru Memerlukan Penomoran</h6>
                <a href="{{ route('admin.surat.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    Kelola Surat
                </a>
            </div>
            <div class="p-0 table-responsive">
                @if ($suratMasukTerbaru->count() > 0)
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pemohon / NIK</th>
                                <th>Jenis Surat</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
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
                                        <span class="text-muted">{{ $surat->created_at->translatedFormat('d M Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $surat->status_badge_color }} rounded-pill px-3 py-1 small">
                                            {{ $surat->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.surat.show', $surat->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                            Periksa <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check2-all fs-1 text-success d-block mb-2"></i>
                        Belum ada antrean permohonan surat baru yang perlu diverifikasi.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ringkasan Status Konten CMS Website -->
    <div class="col-lg-4">
        <div class="admin-panel">
            <div class="panel-header">
                <h6><i class="bi bi-sliders text-success me-2"></i>Status Data CMS Nagari</h6>
            </div>
            <div class="panel-body">
                <div class="list-group list-group-flush mb-3">
                    <a href="{{ route('admin.konten.kaba') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted"><i class="bi bi-newspaper me-2 text-primary"></i>Kaba Nagari (Berita)</span>
                        <span class="badge bg-primary rounded-pill">{{ $totalKaba }} Post</span>
                    </a>
                    <a href="{{ route('admin.konten.umkm') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted"><i class="bi bi-shop me-2 text-warning"></i>Katalog UMKM</span>
                        <span class="badge bg-warning text-dark rounded-pill">{{ $totalUmkm }} Usaha</span>
                    </a>
                    <a href="{{ route('admin.konten.wisata') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted"><i class="bi bi-compass me-2 text-success"></i>Objek Wisata</span>
                        <span class="badge bg-success rounded-pill">{{ $totalWisata }} Lokasi</span>
                    </a>
                    <a href="{{ route('admin.konten.perangkat') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted"><i class="bi bi-people me-2 text-info"></i>Perangkat Nagari</span>
                        <span class="badge bg-info text-dark rounded-pill">{{ $totalPerangkat }} Pejabat</span>
                    </a>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.konten.profil') }}" class="btn btn-light border btn-sm text-start">
                        <i class="bi bi-pencil-square text-success me-2"></i> Edit Profil Nagari & Statistik
                    </a>
                    <a href="{{ route('admin.konten.kaba') }}" class="btn btn-light border btn-sm text-start">
                        <i class="bi bi-plus-circle text-primary me-2"></i> Tulis Berita Kaba Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
