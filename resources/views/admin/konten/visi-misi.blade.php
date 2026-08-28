@extends('admin.layouts.admin')

@section('title', 'Kelola Visi & Misi')
@section('page-title', 'Manajemen Visi & Misi Nagari')

@section('content')
<div class="card-custom">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-eye-fill text-success me-2"></i>Pengaturan Visi & Misi</h5>
    <p class="text-muted small mb-4">Ubah isi pernyataan Visi dan Misi Nagari Koto Malintang.</p>

    <form action="{{ route('admin.konten.visi-misi.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="form-label fw-bold small">Pernyataan Visi Nagari <span class="text-danger">*</span></label>
            <textarea name="isi" rows="3" class="form-control fs-6" required>{{ old('isi', $visi->isi ?? '') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold small">Daftar Misi Nagari (Format: Judul : Deskripsi, 1 per baris) <span class="text-danger">*</span></label>
            @php
                $misiText = "";
                if ($visi && $visi->misis) {
                    foreach ($visi->misis as $m) {
                        $misiText .= $m->judul . ' : ' . $m->deskripsi . "\n";
                    }
                }
            @endphp
            <textarea name="misi_raw" rows="7" class="form-control font-monospace" style="font-size: 0.9rem;" required>{{ old('misi_raw', trim($misiText)) }}</textarea>
            <small class="text-muted">Contoh: Meningkatkan Kualitas SDM : Melalui pendidikan, pelatihan, dan kapasitas masyarakat.</small>
        </div>

        <button type="submit" class="btn btn-success rounded-pill px-5 fw-semibold shadow">
            <i class="bi bi-save me-1"></i> Simpan Visi & Misi
        </button>
    </form>
</div>
@endsection
