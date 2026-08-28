@extends('layouts.app')

@section('title', 'UMKM Milik Saya')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .umkm-card-warga {
        background: white;
        border-radius: 18px;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
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

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Daftar UMKM Milik Saya</h3>
            <p class="text-muted mb-0">Kelola informasi usaha Anda agar dipromosikan di halaman publik UMKM Nagari</p>
        </div>
        <a href="{{ route('warga.umkm.create') }}" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow">
            <i class="bi bi-plus-circle me-1"></i> Tambah UMKM Baru
        </a>
    </div>

    @if ($umkms->count() > 0)
        <div class="row g-4">
            @foreach ($umkms as $umkm)
                <div class="col-md-6">
                    <div class="umkm-card-warga">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <span class="badge bg-warning text-dark mb-2">{{ $umkm->kategori }}</span>
                                <h4 class="fw-bold text-dark fs-5 mb-1">{{ $umkm->nama_usaha }}</h4>
                                <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $umkm->pemilik }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('warga.umkm.edit', $umkm->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('warga.umkm.destroy', $umkm->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus UMKM ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                            {{ $umkm->deskripsi }}
                        </p>

                        <div class="p-3 bg-light rounded-3 small mb-3">
                            <div class="row g-2">
                                @if ($umkm->whatsapp)
                                    <div class="col-sm-6">
                                        <i class="bi bi-whatsapp text-success me-1"></i> {{ $umkm->whatsapp }}
                                    </div>
                                @endif
                                @if ($umkm->instagram)
                                    <div class="col-sm-6">
                                        <i class="bi bi-instagram text-danger me-1"></i> {{ $umkm->instagram }}
                                    </div>
                                @endif
                                @if ($umkm->jam_operasional)
                                    <div class="col-sm-6">
                                        <i class="bi bi-clock text-warning me-1"></i> {{ $umkm->jam_operasional }}
                                    </div>
                                @endif
                                @if ($umkm->tahun_berdiri)
                                    <div class="col-sm-6">
                                        <i class="bi bi-calendar me-1"></i> Est. {{ $umkm->tahun_berdiri }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Tampil di Katalog Publik
                            </span>
                            <a href="{{ route('umkm') }}" class="btn btn-sm btn-link text-success text-decoration-none">
                                Lihat di Halaman UMKM <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 border p-5">
            <i class="bi bi-shop fs-1 text-secondary mb-3 d-block"></i>
            <h5 class="fw-bold text-dark">Belum Ada UMKM yang Didaftarkan</h5>
            <p class="text-muted small">Daftarkan usaha kuliner, kerajinan, atau perdagangan Anda secara gratis untuk mempromosikan produk lokal Nagari.</p>
            <a href="{{ route('warga.umkm.create') }}" class="btn btn-success rounded-pill px-4 py-2 mt-2 shadow">
                <i class="bi bi-plus-circle me-1"></i> Daftarkan Usaha Sekarang
            </a>
        </div>
    @endif
</div>
@endsection
