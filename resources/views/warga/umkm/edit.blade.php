@extends('layouts.app')

@section('title', 'Edit UMKM: ' . $umkm->nama_usaha)

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .form-panel {
        background: white;
        border-radius: 20px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        padding: 36px;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #374151;
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.92rem;
        border: 1.5px solid #e5e7eb;
    }

    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 800px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Edit Informasi UMKM</h3>
            <p class="text-muted mb-0">{{ $umkm->nama_usaha }}</p>
        </div>
        <a href="{{ route('warga.umkm.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="form-panel">
        <form action="{{ route('warga.umkm.update', $umkm->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label for="nama_usaha" class="form-label">Nama Usaha / Merk Dagang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_usaha" id="nama_usaha" class="form-control" value="{{ old('nama_usaha', $umkm->nama_usaha) }}" required />
                </div>
                <div class="col-md-4">
                    <label for="kategori" class="form-label">Kategori Usaha <span class="text-danger">*</span></label>
                    <select name="kategori" id="kategori" class="form-select" required>
                        <option value="Kuliner" {{ old('kategori', $umkm->kategori) == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                        <option value="Kerajinan" {{ old('kategori', $umkm->kategori) == 'Kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                        <option value="Pertanian" {{ old('kategori', $umkm->kategori) == 'Pertanian' ? 'selected' : '' }}>Pertanian</option>
                        <option value="Jasa" {{ old('kategori', $umkm->kategori) == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                        <option value="Perdagangan" {{ old('kategori', $umkm->kategori) == 'Perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="pemilik" class="form-label">Nama Pemilik / Pengelola <span class="text-danger">*</span></label>
                    <input type="text" name="pemilik" id="pemilik" class="form-control" value="{{ old('pemilik', $umkm->pemilik) }}" required />
                </div>
                <div class="col-md-3">
                    <label for="tahun_berdiri" class="form-label">Tahun Berdiri</label>
                    <input type="text" name="tahun_berdiri" id="tahun_berdiri" class="form-control" value="{{ old('tahun_berdiri', $umkm->tahun_berdiri) }}" />
                </div>
                <div class="col-md-3">
                    <label for="jam_operasional" class="form-label">Jam Operasional</label>
                    <input type="text" name="jam_operasional" id="jam_operasional" class="form-control" value="{{ old('jam_operasional', $umkm->jam_operasional) }}" />
                </div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Usaha & Produk Unggulan <span class="text-danger">*</span></label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control" required>{{ old('deskripsi', $umkm->deskripsi) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Upload Foto Produk / Tempat Usaha (Opsional)</label>
                <input type="file" name="foto" id="foto" class="form-control" accept="image/*" />
                @if ($umkm->foto)
                    <small class="text-muted d-block mt-1">Foto saat ini: <a href="{{ asset($umkm->foto) }}" target="_blank">Lihat Foto</a></small>
                @endif
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="whatsapp" class="form-label">Nomor WhatsApp Usaha</label>
                    <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp', $umkm->whatsapp) }}" />
                </div>
                <div class="col-md-4">
                    <label for="instagram" class="form-label">Akun Instagram</label>
                    <input type="text" name="instagram" id="instagram" class="form-control" value="{{ old('instagram', $umkm->instagram) }}" />
                </div>
                <div class="col-md-4">
                    <label for="google_maps_url" class="form-label">Link Google Maps (Opsional)</label>
                    <input type="url" name="google_maps_url" id="google_maps_url" class="form-control" value="{{ old('google_maps_url', $umkm->google_maps_url) }}" />
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <a href="{{ route('warga.umkm.index') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
