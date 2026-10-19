@extends('layouts.app')

@section('title', 'Galeri Foto & Media')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 70px;
    }

    .hero-galeri {
        background: linear-gradient(135deg, #198754 0%, #04543a 100%);
        color: white;
        padding: 90px 0 60px;
        position: relative;
        text-align: center;
        margin-bottom: 40px;
    }

    .hero-galeri h1 {
        font-size: 3.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .galeri-card {
        background: white;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .galeri-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 35px rgba(0,0,0,0.12);
    }

    .galeri-img-wrapper {
        height: 220px;
        overflow: hidden;
        position: relative;
        background: #0f1d10;
        cursor: pointer;
    }

    .galeri-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .galeri-card:hover .galeri-img-wrapper img {
        transform: scale(1.08);
    }

    .galeri-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 29, 16, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        color: white;
        font-size: 1.8rem;
    }

    .galeri-card:hover .galeri-overlay {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-galeri">
    <div class="container">
        <h1>Galeri & Media Nagari</h1>
        <p class="lead text-light mb-0">Dokumentasi Kegiatan, Keindahan Alam, dan Ragam Budaya Nagari Koto Malintang</p>
    </div>
</section>

<div class="container mb-5">
    @if ($galeriList->count() > 0)
        <div class="row g-4">
            @foreach ($galeriList as $g)
                @php
                    $imgUrl = asset('image/bg.jpg');
                    if ($g->gambar) {
                        if (file_exists(public_path($g->gambar))) {
                            $imgUrl = asset($g->gambar);
                        } elseif (file_exists(public_path('image/' . $g->gambar))) {
                            $imgUrl = asset('image/' . $g->gambar);
                        } else {
                            $imgUrl = asset($g->gambar);
                        }
                    }
                @endphp
                <div class="col-sm-6 col-lg-4">
                    <div class="galeri-card">
                        <div class="galeri-img-wrapper" onclick="openPublicLightbox('{{ $imgUrl }}', '{{ addslashes($g->caption ?: 'Dokumentasi Nagari Koto Malintang') }}')">
                            <img src="{{ $imgUrl }}" alt="{{ $g->caption ?? 'Galeri Foto' }}" loading="lazy" />
                            <div class="galeri-overlay">
                                <i class="bi bi-arrows-fullscreen"></i>
                            </div>
                            @if ($g->kabaNagari)
                                <span class="position-absolute top-0 start-0 m-2 badge bg-success shadow-sm">
                                    <i class="bi bi-newspaper me-1"></i> {{ Str::limit($g->kabaNagari->judul, 25) }}
                                </span>
                            @endif
                        </div>

                        <div class="p-4 d-flex flex-column flex-grow-1 justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-2" style="line-height: 1.4;">
                                    {{ $g->caption ?: 'Dokumentasi Nagari Koto Malintang' }}
                                </h6>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-3 text-muted small">
                                <span><i class="bi bi-calendar3 me-1 text-success"></i>{{ $g->created_at ? $g->created_at->translatedFormat('d F Y') : '-' }}</span>
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="openPublicLightbox('{{ $imgUrl }}', '{{ addslashes($g->caption ?: 'Dokumentasi Nagari Koto Malintang') }}')">
                                    Lihat <i class="bi bi-eye ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $galeriList->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="p-4 rounded-circle bg-white shadow-sm d-inline-block mb-3 border">
                <i class="bi bi-images fs-1 text-success"></i>
            </div>
            <h5 class="fw-bold text-dark">Belum ada dokumentasi galeri</h5>
            <p class="text-muted">Dokumentasi foto dan publikasi nagari akan segera diperbarui.</p>
        </div>
    @endif
</div>

<!-- PUBLIC LIGHTBOX MODAL -->
<div class="modal fade" id="publicLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 bg-dark text-white overflow-hidden shadow-lg">
            <div class="modal-body p-0 position-relative text-center bg-black">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <img id="publicLightboxImg" src="#" alt="Lightbox" class="w-100" style="max-height: 85vh; object-fit: contain;" />
                <div class="p-3 bg-dark bg-opacity-90 text-start">
                    <h6 class="fw-bold text-white mb-0" id="publicLightboxCaption"></h6>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPublicLightbox(src, caption) {
    document.getElementById('publicLightboxImg').src = src;
    document.getElementById('publicLightboxCaption').textContent = caption;
    const modal = new bootstrap.Modal(document.getElementById('publicLightboxModal'));
    modal.show();
}
</script>
@endpush
