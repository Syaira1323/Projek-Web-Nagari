@extends('admin.layouts.admin')

@section('title', 'Pengaturan Nagari')
@section('page-title', 'Pengaturan Umum & Jam Pelayanan Nagari')

@section('content')
<div class="card-custom" style="max-width: 800px;">
    <h5 class="fw-bold text-dark mb-1">
        <i class="bi bi-gear-fill text-success me-2"></i>Pengaturan Informasi & Pelayanan Nagari
    </h5>
    <p class="text-muted small mb-4">Kelola nama pimpinan wali nagari dan jam operasional pelayanan masyarakat</p>

    <form action="{{ route('admin.pengaturan.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label small fw-bold text-dark">Nama Resmi Wali Nagari <span class="text-danger">*</span></label>
            <input type="text" name="nama_wali_nagari" class="form-control" value="{{ old('nama_wali_nagari', $namaWali) }}" required />
            <small class="text-muted">Nama ini akan tercantum di surat keterangan resmi nagari.</small>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Jam Pelayanan (Senin - Kamis) <span class="text-danger">*</span></label>
                <input type="text" name="jam_senin_kamis" class="form-control" value="{{ old('jam_senin_kamis', $jamSeninKamis) }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Jam Pelayanan (Jumat) <span class="text-danger">*</span></label>
                <input type="text" name="jam_jumat" class="form-control" value="{{ old('jam_jumat', $jamJumat) }}" required />
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-dark">Nomor Kontak Pelayanan Darurat</label>
            <input type="text" name="nomor_darurat" class="form-control" value="{{ old('nomor_darurat', $nomorDarurat) }}" />
        </div>

        <div class="text-end pt-3 border-top">
            <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                <i class="bi bi-check2-circle me-1"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
