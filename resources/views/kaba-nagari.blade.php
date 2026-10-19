@extends('layouts.app')

@section('title', 'Kaba Nagari')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 70px;
    }

    .hero-kaba {
        background: linear-gradient(135deg, #198754 0%, #04543a 100%);
        color: white;
        padding: 90px 0 60px;
        position: relative;
        text-align: center;
        margin-bottom: 40px;
    }

    .hero-kaba h1 {
        font-size: 3.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .kaba-card {
        background: white;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .kaba-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 35px rgba(0,0,0,0.12);
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-kaba">
    <div class="container">
        <h1>Kaba Nagari</h1>
        <p class="lead text-light mb-0">Warta Terkini, Kabar Nagari, dan Informasi Publik Koto Malintang</p>
    </div>
</section>

<div class="container mb-5">
    @if ($kabaNagaris->count() > 0)
        <div class="row g-4">
            @foreach ($kabaNagaris as $kaba)
                <div class="col-md-6 col-lg-4">
                    <div class="kaba-card">
                        @php
                            $kabaImg = null;
                            if ($kaba->gambar) {
                                if (file_exists(public_path($kaba->gambar))) {
                                    $kabaImg = asset($kaba->gambar);
                                } elseif (file_exists(public_path('image/' . $kaba->gambar))) {
                                    $kabaImg = asset('image/' . $kaba->gambar);
                                }
                            }
                        @endphp
                        @if ($kabaImg)
                            <div class="mb-3 rounded-3 overflow-hidden" style="height: 180px;">
                                <img src="{{ $kabaImg }}" alt="{{ $kaba->judul }}" style="width: 100%; height: 100%; object-fit: cover;" />
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2">
                                <i class="bi bi-tag-fill me-1"></i> {{ $kaba->kategori }}
                            </span>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1 text-warning"></i>
                                {{ $kaba->tanggal ? \Carbon\Carbon::parse($kaba->tanggal)->translatedFormat('d M Y') : '-' }}
                            </small>
                        </div>

                        <h4 class="fw-bold text-dark fs-5 mb-3" style="line-height: 1.4;">
                            {{ $kaba->judul }}
                        </h4>

                        <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                            {{ Str::limit($kaba->ringkasan, 130) }}
                        </p>

                        <div class="pt-3 border-top mt-auto">
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill w-100 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalKaba{{ $kaba->id }}">
                                Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Detail Berita -->
                <div class="modal fade" id="modalKaba{{ $kaba->id }}" tabindex="-1" aria-labelledby="labelKaba{{ $kaba->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header bg-success text-white p-4">
                                <div>
                                    <span class="badge bg-warning text-dark mb-2">{{ $kaba->kategori }}</span>
                                    <h4 class="modal-title fw-bold" id="labelKaba{{ $kaba->id }}">{{ $kaba->judul }}</h4>
                                    <div class="small text-white text-opacity-75 mt-1">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $kaba->tanggal ? \Carbon\Carbon::parse($kaba->tanggal)->translatedFormat('d F Y') : '-' }}
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                @if ($kabaImg)
                                    <div class="mb-3 rounded-3 overflow-hidden shadow-sm" style="max-height: 300px;">
                                        <img src="{{ $kabaImg }}" alt="{{ $kaba->judul }}" class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                @endif
                                <div class="p-3 bg-light rounded-3 mb-4 border-start border-4 border-success fst-italic text-secondary">
                                    {{ $kaba->ringkasan }}
                                </div>
                                <div class="text-dark lh-lg" style="white-space: pre-line; font-size: 1.05rem;">
                                    {{ $kaba->isi }}
                                </div>

                                @if ($kaba->gambarKabaNagaris && $kaba->gambarKabaNagaris->count() > 0)
                                    <div class="mt-4 pt-3 border-top">
                                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-images text-success me-2"></i>Foto Dokumentasi Kegiatan:</h6>
                                        <div class="row g-2">
                                            @foreach ($kaba->gambarKabaNagaris as $foto)
                                                @php
                                                    $fUrl = asset($foto->gambar);
                                                    if (file_exists(public_path($foto->gambar))) {
                                                        $fUrl = asset($foto->gambar);
                                                    } elseif (file_exists(public_path('image/' . $foto->gambar))) {
                                                        $fUrl = asset('image/' . $foto->gambar);
                                                    }
                                                @endphp
                                                <div class="col-4 col-md-3">
                                                    <a href="{{ $fUrl }}" target="_blank" class="d-block rounded-3 overflow-hidden border shadow-sm" style="height: 80px;">
                                                        <img src="{{ $fUrl }}" alt="{{ $foto->caption ?? 'Foto' }}" style="width:100%; height:100%; object-fit:cover;" />
                                                    </a>
                                                    @if ($foto->caption)
                                                        <small class="text-muted d-block text-truncate mt-1" style="font-size:0.7rem;">{{ $foto->caption }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-4">
            Belum ada kaba nagari yang dipublikasikan.
        </div>
    @endif
</div>
@endsection
