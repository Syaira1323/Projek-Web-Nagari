@extends('admin.layouts.admin')

@section('title', 'Kelola Visi & Misi')
@section('page-title', 'Manajemen Visi & Misi Nagari')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-eye-fill text-success me-2"></i>Kelola Visi & Misi Nagari
        </h4>
        <p class="text-muted small mb-0">
            Perbarui visi utama, makna filosofis, dan butir-butir misi pembangunan Nagari Koto Malintang
        </p>
    </div>
    <a href="{{ route('visi-misi') }}" target="_blank" class="btn btn-outline-success rounded-pill px-4 fw-semibold">
        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Halaman Publik
    </a>
</div>

<form action="{{ route('admin.konten.visi-misi.update') }}" method="POST">
    @csrf
    @method('PUT')

    <!-- BAGIAN 1: PERNYATAAN VISI UTAMA -->
    <div class="card-custom mb-4">
        <h6 class="fw-bold text-dark mb-3">
            <i class="bi bi-stars text-success me-2"></i>1. Pernyataan Visi Utama Nagari
        </h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-dark">Judul / Label Visi <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control" value="{{ old('judul', $visi->judul ?? 'VISI') }}" required />
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-bold text-dark">Teks Pernyataan Visi Nagari <span class="text-danger">*</span></label>
                <textarea name="isi" rows="3" class="form-control" required placeholder="Contoh: Terwujudnya Nagari Koto Malintang yang Maju, Mandiri, dan Sejahtera...">{{ old('isi', $visi->isi ?? '') }}</textarea>
                <small class="text-muted" style="font-size: 0.75rem;">Pernyataan visi utama yang tampil di bagian atas halaman website publik.</small>
            </div>
        </div>
    </div>

    <!-- BAGIAN 2: MAKNA FILOSOFIS VISI -->
    <div class="card-custom mb-4">
        <h6 class="fw-bold text-dark mb-2">
            <i class="bi bi-lightbulb-fill text-warning me-2"></i>2. Makna Kata Kunci Visi
        </h6>
        <p class="text-muted small mb-3">
            Tuliskan kata kunci makna visi beserta penjelasannya. Format: <code>Istilah : Penjelasan : Icon</code> (1 per baris).<br>
            <span class="badge bg-light text-dark border mt-1">Pilihan Icon: <code>arrow-up</code>, <code>shield</code>, <code>heart</code>, <code>book</code></span>
        </p>
        @php
            $maknaText = "";
            if ($visi && $visi->maknaVisis && $visi->maknaVisis->count() > 0) {
                foreach ($visi->maknaVisis as $mv) {
                    $maknaText .= $mv->istilah . ' : ' . $mv->deskripsi . ' : ' . ($mv->icon ?: 'arrow-up') . "\n";
                }
            } else {
                $maknaText = "Maju : Berkembang dalam segala aspek kehidupan : arrow-up\nMandiri : Mampu berdikari dan tidak bergantung : shield\nSejahtera : Kehidupan yang layak dan bahagia : heart\nReligius : Berdasarkan nilai-nilai agama dan adat : book";
            }
        @endphp
        <textarea name="makna_raw" rows="5" class="form-control font-monospace" style="font-size: 0.88rem;">{{ old('makna_raw', trim($maknaText)) }}</textarea>
        <small class="text-muted" style="font-size: 0.75rem;">Contoh: <code>Maju : Berkembang dalam segala aspek kehidupan : arrow-up</code></small>
    </div>

    <!-- BAGIAN 3: DAFTAR BUTIR MISI -->
    <div class="card-custom mb-4">
        <h6 class="fw-bold text-dark mb-2">
            <i class="bi bi-list-check text-success me-2"></i>3. Butir-Butir Misi Pembangunan Nagari
        </h6>
        <p class="text-muted small mb-3">
            Tuliskan daftar misi nagari. Format: <code>Judul Misi : Uraian Deskripsi Misi</code> (1 per baris). Sistem otomatis menomori setiap baris.
        </p>
        @php
            $misiText = "";
            if ($visi && $visi->misis && $visi->misis->count() > 0) {
                foreach ($visi->misis as $m) {
                    $misiText .= $m->judul . ' : ' . $m->deskripsi . "\n";
                }
            } else {
                $misiText = "Meningkatkan Kualitas Sumber Daya Manusia : Melalui pendidikan, pelatihan, dan pengembangan kapasitas masyarakat\nMengembangkan Ekonomi Kerakyatan : Memberdayakan UMKM dan potensi ekonomi lokal untuk kesejahteraan bersama\nMemperkuat Infrastruktur Nagari : Membangun dan memelihara sarana prasarana untuk mendukung aktivitas masyarakat\nMelestarikan Budaya dan Adat Istiadat : Menjaga dan mengembangkan warisan budaya Minangkabau untuk generasi mendatang\nMeningkatkan Pelayanan Publik : Memberikan pelayanan yang prima, transparan, dan akuntabel kepada masyarakat\nMenjaga Kelestarian Lingkungan : Mengelola sumber daya alam secara berkelanjutan untuk masa depan yang lebih baik";
            }
        @endphp
        <textarea name="misi_raw" rows="8" class="form-control font-monospace" style="font-size: 0.88rem;" required>{{ old('misi_raw', trim($misiText)) }}</textarea>
        <small class="text-muted" style="font-size: 0.75rem;">Contoh: <code>Meningkatkan Pelayanan Publik : Memberikan pelayanan yang prima, transparan, dan akuntabel</code></small>
    </div>

    <div class="d-flex justify-content-end mb-5">
        <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-semibold shadow-sm">
            <i class="bi bi-save me-1"></i> Simpan Perubahan Visi & Misi
        </button>
    </div>
</form>
@endsection
