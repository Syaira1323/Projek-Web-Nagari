@extends('layouts.app')

@section('title', 'Sejarah Nagari')

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
        padding-top: 70px;
    }

    /* Hero */
    .hero-sejarah {
        position: relative;
        min-height: 45vh;
        background: url("{{ asset('image/forest2.jpg') }}") center/cover no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        margin-bottom: 40px;
    }

    .hero-sejarah::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(20,83,45,.8), rgba(20,83,45,.65));
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .section-title {
        font-weight: 800;
        color: #14532d;
        letter-spacing: .3px;
    }

    .lead-muted {
        color: #4b5563;
        font-size: 1.05rem;
        line-height: 1.8;
    }

    .card.hover-lift {
        transition: transform .3s ease, box-shadow .3s ease;
        border-radius: 16px;
    }

    .card.hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,.1);
    }

    .accordion-button:not(.collapsed) {
        color: #198754;
        background-color: #e8f5e9;
        font-weight: bold;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }

    .accordion-item {
        border-radius: 12px !important;
        overflow: hidden;
        margin-bottom: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
</style>
@endpush

@section('content')
<!-- Hero -->
<section class="hero-sejarah">
    <div class="hero-content container py-5">
        <h1 class="display-4 fw-bold mb-3">Sejarah Nagari Koto Malintang</h1>
        <p class="lead mb-0">Perjalanan singkat, asal-usul, dan warisan budaya yang tetap lestari</p>
    </div>
</section>

<div class="container mb-5">
    @php
        $sejarahSingkat = $sejarah->where('tipe', 'singkat')->first();
        $sejarahPeriodes = $sejarah->where('tipe', 'periode')->sortBy('urutan');
    @endphp

    <!-- Sejarah Singkat -->
    @if ($sejarahSingkat)
        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm hover-lift">
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4 align-items-center">
                            @if (!empty($sejarahSingkat->gambar))
                                <div class="col-md-5">
                                    <img src="{{ asset($sejarahSingkat->gambar) }}" alt="{{ $sejarahSingkat->judul }}" class="img-fluid rounded-4 shadow-sm" />
                                </div>
                            @endif
                            <div class="{{ !empty($sejarahSingkat->gambar) ? 'col-md-7' : 'col-12' }}">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 text-uppercase fw-bold">Sekilas Pandang</span>
                                </div>
                                <h2 class="h3 section-title mb-3">{{ $sejarahSingkat->judul }}</h2>
                                <div class="lead-muted mb-3" style="white-space: pre-line;">
                                    {{ $sejarahSingkat->isi }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Periode Sejarah (Accordion) -->
    @if ($sejarahPeriodes && $sejarahPeriodes->count() > 0)
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="h4 section-title mb-3"><i class="bi bi-hourglass-split me-2 text-success"></i>Periode Sejarah Nagari</h2>
                <div class="accordion" id="sejarahAccordion">
                    @foreach ($sejarahPeriodes as $index => $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $item->id }}">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $item->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $item->id }}">
                                    <i class="bi bi-clock-history me-2 text-success"></i>
                                    <strong>{{ $item->judul }}</strong>
                                    @if ($item->periode)
                                        <span class="badge bg-secondary ms-2 small">{{ $item->periode }}</span>
                                    @endif
                                </button>
                            </h2>
                            <div id="collapse{{ $item->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $item->id }}" data-bs-parent="#sejarahAccordion">
                                <div class="accordion-body text-secondary lh-lg p-4" style="font-size: 1rem;">
                                    @if (!empty($item->gambar))
                                        <div class="mb-3">
                                            <img src="{{ asset($item->gambar) }}" alt="{{ $item->judul }}" class="img-thumbnail rounded-3" style="max-height: 200px;" />
                                        </div>
                                    @endif
                                    <div style="white-space: pre-line;">{{ $item->isi }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
