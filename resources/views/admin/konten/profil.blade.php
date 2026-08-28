@extends('admin.layouts.admin')

@section('title', 'Kelola Profil Nagari')
@section('page-title', 'Manajemen Profil, Gambar & Statistik Nagari')

@section('content')
<div class="card-custom">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pencil-square text-success me-2"></i>Formulir Data Profil Nagari</h5>
    <p class="text-muted small mb-4">Perubahan pada data ini akan langsung tersimpan di MySQL dan tampil di halaman publik serta beranda website nagari.</p>

    <form action="{{ route('admin.konten.profil.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold small">Nama Nagari <span class="text-danger">*</span></label>
                <input type="text" name="nama_nagari" class="form-control" value="{{ old('nama_nagari', $profil->nama_nagari) }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Kecamatan <span class="text-danger">*</span></label>
                <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $profil->kecamatan) }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Kabupaten <span class="text-danger">*</span></label>
                <input type="text" name="kabupaten" class="form-control" value="{{ old('kabupaten', $profil->kabupaten) }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Provinsi <span class="text-danger">*</span></label>
                <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $profil->provinsi) }}" required />
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold small">Jumlah Penduduk (Jiwa) <span class="text-danger">*</span></label>
                <input type="number" name="jumlah_penduduk" class="form-control" value="{{ old('jumlah_penduduk', $profil->jumlah_penduduk) }}" required />
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Jumlah Jorong <span class="text-danger">*</span></label>
                <input type="number" name="jumlah_jorong" class="form-control" value="{{ old('jumlah_jorong', $profil->jumlah_jorong) }}" required />
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Luas Wilayah (km²) <span class="text-danger">*</span></label>
                <input type="number" step="0.1" name="luas_wilayah" class="form-control" value="{{ old('luas_wilayah', $profil->luas_wilayah) }}" required />
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold small">Alamat Kantor Wali Nagari</label>
                <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $profil->alamat) }}" />
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Email Resmi</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $profil->email) }}" />
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Nomor Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $profil->telepon) }}" />
            </div>
        </div>

        <!-- UPLOAD GAMBAR PROFIL -->
        <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
            <div class="col-md-6">
                <label class="form-label fw-bold small"><i class="bi bi-image text-success me-1"></i> Foto Utama Nagari / Banner</label>
                @if ($profil->foto_utama)
                    <div class="mb-2">
                        <img src="{{ asset($profil->foto_utama) }}" alt="Foto Utama" class="img-thumbnail rounded-3" style="height: 120px; object-fit: cover;" />
                    </div>
                @endif
                <input type="file" name="foto_utama" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp" />
                <small class="text-muted">Format: JPG, PNG, WEBP (Maks 4MB)</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small"><i class="bi bi-shield-check text-primary me-1"></i> Logo Resmi Nagari</label>
                @if ($profil->logo)
                    <div class="mb-2">
                        <img src="{{ asset($profil->logo) }}" alt="Logo" class="img-thumbnail rounded-3" style="height: 80px; object-fit: contain;" />
                    </div>
                @endif
                <input type="file" name="logo" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp" />
                <small class="text-muted">Format: PNG transparan disukai</small>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold small">Deskripsi Profil Nagari <span class="text-danger">*</span></label>
            <textarea name="deskripsi" rows="6" class="form-control" required>{{ old('deskripsi', $profil->deskripsi) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success rounded-pill px-5 fw-semibold shadow">
            <i class="bi bi-save me-1"></i> Simpan Perubahan Profil & Gambar
        </button>
    </form>
</div>
@endsection
