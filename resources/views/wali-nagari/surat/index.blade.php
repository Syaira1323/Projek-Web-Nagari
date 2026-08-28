@extends('layouts.app')

@section('title', 'Layanan Stempel Surat Wali Nagari')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .card-panel {
        background: white;
        border-radius: 20px;
        border: 1px solid #e9ecef;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
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
            <h3 class="fw-bold text-dark mb-1">Persetujuan & Stempel Surat Wali Nagari</h3>
            <p class="text-muted mb-0">Bubuhkan stempel digital resmi dan tanda tangan Wali Nagari pada permohonan surat masyarakat</p>
        </div>
        <a href="{{ route('wali-nagari.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- FILTER TABS -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('wali-nagari.surat.index', ['status' => 'menunggu_wali_nagari']) }}" class="btn btn-sm {{ $status === 'menunggu_wali_nagari' ? 'btn-warning text-dark fw-bold' : 'btn-light border' }} rounded-pill px-4 py-2">
            <i class="bi bi-hourglass-split me-1"></i> Menunggu Stempel ({{ $counts['antrean'] }})
        </a>
        <a href="{{ route('wali-nagari.surat.index', ['status' => 'disetujui']) }}" class="btn btn-sm {{ $status === 'disetujui' ? 'btn-success fw-bold' : 'btn-light border' }} rounded-pill px-4 py-2">
            <i class="bi bi-check-circle me-1"></i> Telah Distempel ({{ $counts['disetujui'] }})
        </a>
        <a href="{{ route('wali-nagari.surat.index', ['status' => 'semua']) }}" class="btn btn-sm {{ $status === 'semua' ? 'btn-dark fw-bold' : 'btn-light border' }} rounded-pill px-4 py-2">
            Semua Arsip ({{ $counts['total'] }})
        </a>
    </div>

    <div class="card-panel">
        @if ($suratList->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>NO</th>
                            <th>PEMOHON / NIK</th>
                            <th>JENIS SURAT</th>
                            <th>TANGGAL MASUK</th>
                            <th>STATUS</th>
                            <th class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suratList as $index => $surat)
                            <tr>
                                <td>{{ $suratList->firstItem() + $index }}</td>
                                <td>
                                    <strong class="text-dark d-block">{{ $surat->nama_pemohon }}</strong>
                                    <small class="text-muted">NIK: {{ $surat->nik_pemohon }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $surat->nama_jenis_surat }}</span>
                                    <small class="text-success">{{ $surat->nomor_surat ?: 'Nomor terbit saat distempel' }}</small>
                                </td>
                                <td><small class="text-muted">{{ $surat->created_at->translatedFormat('d M Y, H:i') }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $surat->status_badge_color }} rounded-pill px-3 py-1 small">
                                        {{ $surat->status_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('wali-nagari.surat.show', $surat->id) }}" class="btn btn-sm {{ $surat->status === 'menunggu_wali_nagari' ? 'btn-warning text-dark fw-bold' : 'btn-outline-success' }} rounded-pill px-3">
                                        @if ($surat->status === 'menunggu_wali_nagari')
                                            <i class="bi bi-pen me-1"></i> Periksa & Beri Stempel
                                        @else
                                            <i class="bi bi-eye me-1"></i> Lihat Dokumen
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $suratList->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                Tidak ada permohonan surat dalam status ini.
            </div>
        @endif
    </div>
</div>
@endsection
