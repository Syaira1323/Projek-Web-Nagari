@extends('admin.layouts.admin')

@section('title', 'Kelola Adat & Budaya')
@section('page-title', 'Manajemen Adat, Budaya & Gambar Dokumentasi')

@section('content')
<div class="card-custom">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-feather text-success me-2"></i>Formulir Data Adat & Budaya Nagari</h5>
    <p class="text-muted small mb-4">Pengaturan informasi tatanan adat, suku/kaum, tradisi kegiatan, dan foto dokumentasi nagari.</p>

    <form action="{{ route('admin.konten.adat.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold small">Jumlah Suku / Kaum Adat <span class="text-danger">*</span></label>
                <input type="number" name="jumlah_suku" class="form-control" value="{{ old('jumlah_suku', $adat->jumlah_suku ?? 7) }}" required />
            </div>
            <div class="col-md-8">
                <label class="form-label fw-bold small">Daftar Tradisi / Kegiatan Adat (1 per baris) <span class="text-danger">*</span></label>
                @php
                    $kegiatan = is_array($adat->kegiatan_adat) ? $adat->kegiatan_adat : json_decode($adat->kegiatan_adat, true);
                    $kegiatanText = $kegiatan ? implode("\n", $kegiatan) : "Wirid Kepayungan\nBatagak Pangulu\nMangaji Tamaik\nPesta Budaya Danau Maninjau";
                @endphp
                <textarea name="kegiatan_adat_raw" rows="4" class="form-control" placeholder="Tuliskan 1 nama kegiatan per baris" required>{{ old('kegiatan_adat_raw', $kegiatanText) }}</textarea>
                <small class="text-muted">Setiap baris baru akan dijadikan satu item kegiatan.</small>
            </div>
        </div>

        <!-- UPLOAD GAMBAR DOKUMENTASI ADAT -->
        <div class="mb-3 p-3 bg-light rounded-3 border">
            <label class="form-label fw-bold small"><i class="bi bi-image text-success me-1"></i> Foto Dokumentasi Tradisi & Adat Nagari</label>
            @if ($adat->gambar)
                <div class="mb-2">
                    <img src="{{ asset($adat->gambar) }}" alt="Foto Adat" class="img-thumbnail rounded-3" style="max-height: 150px;" />
                </div>
            @endif
            <input type="file" name="gambar" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp" />
            <small class="text-muted">Unggah foto upacara adat, batagak pangulu, atau prosesi adat nagari (Format JPG/PNG/WEBP, Maks 4MB).</small>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold small">Uraian Falsafah & Tatanan Adat Nagari <span class="text-danger">*</span></label>
            <textarea name="uraian" rows="6" class="form-control" required>{{ old('uraian', $adat->uraian) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success rounded-pill px-5 fw-semibold shadow">
            <i class="bi bi-save me-1"></i> Simpan Perubahan Adat & Budaya
        </button>
    </form>
</div>
@endsection
