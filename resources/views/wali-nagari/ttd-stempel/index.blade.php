@extends('layouts.app')

@section('title', 'Tanda Tangan & Stempel Resmi')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .wali-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .wali-header {
        background: linear-gradient(135deg, #0d5c2e 0%, #1a8a4a 100%);
        color: white;
        padding: 28px 32px;
    }

    .preview-canvas {
        background: repeating-conic-gradient(#f1f5f9 0% 25%, #ffffff 0% 50%) 50% / 20px 20px;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 950px;">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="wali-card mb-4">
        <div class="wali-header">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Pimpinan Nagari</span>
            <h3 class="fw-bold mb-1 text-white">Kelola Tanda Tangan & Stempel Resmi Digital</h3>
            <p class="text-white-50 mb-0 small">Berkas TTD dan Stempel ini akan disematkan secara otomatis pada setiap surat keterangan warga yang Anda setujui.</p>
        </div>

        <div class="p-4 p-md-5">
            <div class="row g-4">
                <!-- 1. TANDA TANGAN WALI NAGARI -->
                <div class="col-lg-6">
                    <div class="card border rounded-4 p-4 h-100 bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi bi-pen-fill text-success me-2"></i>Tanda Tangan Wali Nagari
                            </h5>
                            @if ($ttdPath)
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold">Aktif</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Belum Diunggah</span>
                            @endif
                        </div>

                        <!-- Canvas Preview TTD -->
                        <div class="preview-canvas">
                            @if (!empty($ttdPath) && file_exists(public_path($ttdPath)))
                                <img src="{{ asset($ttdPath) }}" alt="Tanda Tangan Wali" style="max-height: 140px; max-width: 90%; object-fit: contain;" />
                            @else
                                <div class="text-center text-muted">
                                    <i class="bi bi-pen fs-1 d-block mb-1 text-secondary"></i>
                                    <small>Belum ada file TTD (Gunakan format PNG transparan)</small>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('wali-nagari.ttd-stempel.update-ttd') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Unggah / Ganti File Tanda Tangan <span class="text-danger">*</span></label>
                                <input type="file" name="tanda_tangan" class="form-control" accept=".png,.jpg,.jpeg,.webp" required />
                                <small class="text-muted" style="font-size: 0.78rem;">Disarankan PNG transparan (tanpa background putih) agar hasil cetak surat sangat rapi.</small>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold text-dark">Nama Wali Nagari</label>
                                    <input type="text" name="nama_wali" class="form-control form-control-sm" value="{{ old('nama_wali', $namaWali) }}" />
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-dark">Gelar Adat</label>
                                    <input type="text" name="gelar_wali" class="form-control form-control-sm" value="{{ old('gelar_wali', $gelarWali) }}" />
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2">
                                @if ($ttdPath)
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" onclick="document.getElementById('formHapusTtd').submit();">
                                        <i class="bi bi-trash"></i> Hapus TTD
                                    </button>
                                @else
                                    <div></div>
                                @endif
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan TTD
                                </button>
                            </div>
                        </form>

                        @if ($ttdPath)
                            <form id="formHapusTtd" action="{{ route('wali-nagari.ttd-stempel.delete-ttd') }}" method="POST" class="d-none" onsubmit="return confirm('Hapus tanda tangan wali nagari?');">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>

                <!-- 2. STEMPEL RESMI NAGARI -->
                <div class="col-lg-6">
                    <div class="card border rounded-4 p-4 h-100 bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi bi-stamp text-primary me-2"></i>Stempel Resmi Nagari
                            </h5>
                            @if ($stempelPath)
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold">Aktif</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Belum Diunggah</span>
                            @endif
                        </div>

                        <!-- Canvas Preview Stempel -->
                        <div class="preview-canvas">
                            @if (!empty($stempelPath) && file_exists(public_path($stempelPath)))
                                <img src="{{ asset($stempelPath) }}" alt="Stempel Resmi Nagari" style="max-height: 140px; max-width: 90%; object-fit: contain;" />
                            @else
                                <div class="text-center text-muted">
                                    <i class="bi bi-stamp fs-1 d-block mb-1 text-secondary"></i>
                                    <small>Belum ada file stempel (Gunakan format PNG transparan)</small>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('wali-nagari.ttd-stempel.update-stempel') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-dark">Unggah / Ganti File Stempel Nagari <span class="text-danger">*</span></label>
                                <input type="file" name="stempel" class="form-control" accept=".png,.jpg,.jpeg,.webp" required />
                                <small class="text-muted" style="font-size: 0.78rem;">Format: PNG transparan (Maksimal 3MB).</small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2">
                                @if ($stempelPath)
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" onclick="document.getElementById('formHapusStempel').submit();">
                                        <i class="bi bi-trash"></i> Hapus Stempel
                                    </button>
                                @else
                                    <div></div>
                                @endif
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan Stempel
                                </button>
                            </div>
                        </form>

                        @if ($stempelPath)
                            <form id="formHapusStempel" action="{{ route('wali-nagari.ttd-stempel.delete-stempel') }}" method="POST" class="d-none" onsubmit="return confirm('Hapus stempel resmi nagari?');">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
