@extends('layouts.app')

@section('title', 'Dashboard Warga')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #102c1e 0%, #1e3b2c 100%);
        border-radius: 20px;
        color: white;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(16, 44, 30, 0.15);
    }

    .stat-card-warga {
        background: white;
        border-radius: 18px;
        padding: 22px;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .stat-card-warga:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .stat-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .card-panel {
        background: white;
        border-radius: 18px;
        border: 1px solid #e9ecef;
        padding: 24px;
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

    <!-- Header Dashboard -->
    <div class="dashboard-header">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Portal Warga Nagari</span>
                <h2 class="fw-bold mb-1">Halo, {{ $user->name }}!</h2>
                <p class="text-white-50 mb-0">NIK: <span class="text-warning fw-semibold">{{ $user->nik }}</span> &bull; Jorong: {{ $user->address }}</p>
            </div>
            <div class="col-md-4 text-md-end d-flex flex-column flex-md-row justify-content-md-end gap-2">
                <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalUbahPasswordWarga">
                    <i class="bi bi-key-fill me-1 text-warning"></i> Ubah Password
                </button>
                <a href="{{ route('warga.surat.create') }}" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow">
                    <i class="bi bi-plus-circle me-1"></i> Ajukan Surat Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card-warga">
                <div class="stat-icon-wrap bg-success bg-opacity-10 text-success">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $totalSurat }}</h3>
                    <small class="text-muted">Total Surat Diajukan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-warga">
                <div class="stat-icon-wrap bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $suratMenunggu }}</h3>
                    <small class="text-muted">Proses Verifikasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-warga">
                <div class="stat-icon-wrap bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $suratSelesai }}</h3>
                    <small class="text-muted">Selesai / Stempel</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-warga">
                <div class="stat-icon-wrap bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $totalUmkm }}</h3>
                    <small class="text-muted">UMKM Terdaftar</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Utama: Riwayat Surat & UMKM Saya -->
    <div class="row g-4 mb-4">
        <!-- Riwayat Surat -->
        <div class="col-lg-8">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-success me-2"></i>Permohonan Surat Terbaru</h5>
                    <a href="{{ route('warga.surat.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Lihat Semua</a>
                </div>

                @if ($suratTerbaru->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>JENIS SURAT</th>
                                    <th>TANGGAL</th>
                                    <th>STATUS</th>
                                    <th class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suratTerbaru as $surat)
                                    <tr>
                                        <td>
                                            <strong class="text-dark d-block">{{ $surat->nama_jenis_surat }}</strong>
                                            <small class="text-muted">{{ $surat->nomor_surat ?: 'Nomor akan terbit saat disetujui' }}</small>
                                        </td>
                                        <td><small class="text-muted">{{ $surat->created_at->format('d/m/Y') }}</small></td>
                                        <td>
                                            <span class="badge bg-{{ $surat->status_badge_color }} rounded-pill px-3 py-1 small">
                                                {{ $surat->status_label }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('warga.surat.show', $surat->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        Belum ada permohonan surat yang diajukan.
                        <div class="mt-2">
                            <a href="{{ route('warga.surat.create') }}" class="btn btn-sm btn-success rounded-pill px-3">Ajukan Surat Pertama</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- UMKM Saya & Quick Link -->
        <div class="col-lg-4">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-shop text-success me-2"></i>UMKM Milik Saya</h5>
                    <a href="{{ route('warga.umkm.create') }}" class="btn btn-sm btn-outline-success rounded-pill px-2">
                        <i class="bi bi-plus"></i> Tambah
                    </a>
                </div>

                @if ($umkmSaya->count() > 0)
                    <div class="list-group list-group-flush mb-3">
                        @foreach ($umkmSaya as $umkm)
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $umkm->nama_usaha }}</h6>
                                        <span class="badge bg-warning text-dark small mb-1">{{ $umkm->kategori }}</span>
                                        <small class="text-muted d-block">{{ Str::limit($umkm->deskripsi, 60) }}</small>
                                    </div>
                                    <a href="{{ route('warga.umkm.edit', $umkm->id) }}" class="btn btn-sm btn-light border rounded-circle" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('warga.umkm.index') }}" class="btn btn-sm btn-light border w-100 rounded-pill">Kelola Semua UMKM</a>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-bag-plus fs-1 d-block mb-2 text-secondary"></i>
                        Punya usaha mikro atau kerajinan di Koto Malintang? Daftarkan usaha Anda agar tampil di website nagari!
                        <div class="mt-3">
                            <a href="{{ route('warga.umkm.create') }}" class="btn btn-sm btn-success rounded-pill px-3">
                                <i class="bi bi-plus-circle me-1"></i> Daftarkan UMKM Saya
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MODAL UBAH PASSWORD WARGA -->
<div class="modal fade" id="modalUbahPasswordWarga" tabindex="-1" aria-labelledby="modalUbahPasswordWargaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold" id="modalUbahPasswordWargaLabel">
                        <i class="bi bi-shield-lock text-warning me-2"></i>Ubah Password Akun Saya
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
