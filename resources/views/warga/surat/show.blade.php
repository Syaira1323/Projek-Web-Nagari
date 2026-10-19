@extends('layouts.app')

@section('title', 'Detail Status Permohonan Surat')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .detail-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .status-tracker {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 30px;
    }

    .status-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
        position: relative;
        z-index: 2;
    }

    .status-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        background: #e2e8f0;
        color: #64748b;
        margin-bottom: 8px;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .status-circle.active {
        background: #198754;
        color: white;
    }

    .status-circle.done {
        background: #198754;
        color: white;
    }

    .status-circle.rejected {
        background: #dc3545;
        color: white;
    }

    .surat-preview-paper {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        font-family: 'Times New Roman', Times, serif;
        font-size: 1.05rem;
        line-height: 1.8;
    }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 950px;">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="detail-card mb-4">
        <div class="p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-{{ $surat->status_badge_color }} rounded-pill px-3 py-1 mb-2">
                        {{ $surat->status_label }}
                    </span>
                    <h3 class="fw-bold mb-1 text-dark">{{ $surat->nama_jenis_surat }}</h3>
                    <p class="text-muted mb-0 small">
                        Nomor Pengajuan: <strong>#KM-{{ str_pad($surat->id, 5, '0', STR_PAD_LEFT) }}</strong> &bull;
                        Diajukan pada: {{ $surat->created_at->format('d M Y, H:i') }} WIB
                    </p>
                </div>
                <div>
                    @if ($surat->status === 'disetujui')
                        <div class="d-flex gap-2">
                            <a href="{{ route('warga.surat.download-pdf', $surat->id) }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh PDF Resmi
                            </a>
                            <a href="{{ route('warga.surat.print', $surat->id) }}" target="_blank" class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen
                            </a>
                        </div>
                    @else
                        <a href="{{ route('warga.surat.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                        </a>
                    @endif
                </div>
            </div>

            <!-- STATUS TRACKER -->
            <div class="status-tracker px-2">
                @php
                    $isMenungguAdmin = $surat->status === 'menunggu_admin';
                    $isMenungguWali = $surat->status === 'menunggu_wali_nagari';
                    $isDisetujui = $surat->status === 'disetujui';
                    $isDitolak = $surat->status === 'ditolak';
                @endphp

                <div class="status-step">
                    <div class="status-circle {{ $isDitolak ? 'rejected' : 'done' }}">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <span class="fw-semibold small text-dark">1. Pengajuan Dikirim</span>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $surat->created_at->format('d/m/Y') }}</small>
                </div>

                <div class="status-step">
                    <div class="status-circle {{ $isDitolak ? 'rejected' : ($isMenungguWali || $isDisetujui ? 'done' : ($isMenungguAdmin ? 'active' : '')) }}">
                        @if ($isDitolak && empty($surat->catatan_wali))
                            <i class="bi bi-x-lg"></i>
                        @elseif ($isMenungguWali || $isDisetujui)
                            <i class="bi bi-check-lg"></i>
                        @else
                            2
                        @endif
                    </div>
                    <span class="fw-semibold small text-dark">2. Verifikasi Admin</span>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $isMenungguAdmin ? 'Sedang Diproses' : ($isMenungguWali || $isDisetujui ? 'Lolos Verifikasi' : '-') }}</small>
                </div>

                <div class="status-step">
                    <div class="status-circle {{ $isDitolak && !empty($surat->catatan_wali) ? 'rejected' : ($isDisetujui ? 'done' : ($isMenungguWali ? 'active' : '')) }}">
                        @if ($isDisetujui)
                            <i class="bi bi-patch-check-fill"></i>
                        @elseif ($isDitolak && !empty($surat->catatan_wali))
                            <i class="bi bi-x-lg"></i>
                        @else
                            3
                        @endif
                    </div>
                    <span class="fw-semibold small text-dark">3. TTD & Stempel Wali Nagari</span>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $isDisetujui ? 'Selesai Berstempel' : ($isMenungguWali ? 'Menunggu TTD' : '-') }}</small>
                </div>
            </div>

            <!-- NOTIFIKASI CATATAN / PENOLAKAN -->
            @if ($isDitolak)
                <div class="alert alert-danger rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-danger mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Permohonan Surat Ditolak</h6>
                    <p class="mb-0">
                        <strong>Alasan / Catatan Penolakan:</strong> {{ $surat->catatan_admin ?: $surat->catatan_wali ?: 'Persyaratan berkas belum memenuhi ketentuan dinas.' }}
                    </p>
                </div>
            @elseif ($isDisetujui)
                <div class="alert alert-success rounded-4 p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <i class="bi bi-patch-check-fill text-success fs-5 me-2"></i>
                        <strong>Surat Resmi Telah Selesai Diterbitkan:</strong> Nomor Surat: <span class="badge bg-success">{{ $surat->nomor_surat }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('warga.surat.download-pdf', $surat->id) }}" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh PDF
                        </a>
                        <a href="{{ route('warga.surat.print', $surat->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                            <i class="bi bi-printer me-1"></i> Cetak
                        </a>
                    </div>
                </div>
            @endif

            <!-- ROW: DATA PEMOHON & BERKAS PERSYARATAN -->
            <div class="row g-4 mb-4">
                <!-- Rincian Identitas Pemohon -->
                <div class="col-md-7">
                    <div class="card border rounded-4 p-4 h-100 bg-light">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-vcard text-success me-2"></i>Rincian Identitas Pemohon</h6>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted" style="width: 35%;">Nama Lengkap</td>
                                <td class="fw-bold text-dark">: {{ $surat->nama_pemohon }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIK</td>
                                <td class="text-dark">: {{ $surat->nik_pemohon ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tempat, Tgl Lahir</td>
                                <td class="text-dark">: {{ $surat->tempat_lahir }}, {{ $surat->tanggal_lahir ? $surat->tanggal_lahir->format('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin</td>
                                <td class="text-dark">: {{ $surat->jenis_kelamin }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Agama / Pekerjaan</td>
                                <td class="text-dark">: {{ $surat->agama }} / {{ $surat->pekerjaan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat Domisili</td>
                                <td class="text-dark">: {{ $surat->alamat }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keperluan</td>
                                <td class="text-dark fw-semibold">: {{ $surat->keperluan }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Berkas Persyaratan Terlampir -->
                <div class="col-md-5">
                    <div class="card border rounded-4 p-4 h-100 bg-white">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="bi bi-paperclip text-success me-2"></i>Berkas Persyaratan ({{ $surat->berkasPersyaratans->count() }})
                        </h6>

                        @if ($surat->berkasPersyaratans->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($surat->berkasPersyaratans as $berkas)
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate me-2">
                                            <strong class="d-block small text-dark">{{ $berkas->label_jenis }}</strong>
                                            <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">
                                                {{ $berkas->nama_file_asli }} ({{ $berkas->formatted_size }})
                                            </small>
                                        </div>
                                        <a href="{{ route('warga.berkas.download', $berkas->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                            <i class="bi bi-download me-1"></i> Unduh
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted small">
                                <i class="bi bi-file-earmark-x fs-2 d-block mb-1 text-secondary"></i>
                                Tidak ada berkas file yang dilampirkan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PRATINJAU DRAF REDAKSI SURAT -->
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-richtext text-success me-2"></i>Pratinjau Redaksi Naskah Dinas</h6>
            <div class="surat-preview-paper">
                <div class="text-center mb-3">
                    <h6 class="fw-bold mb-0 text-uppercase" style="text-decoration: underline;">{{ $surat->nama_jenis_surat }}</h6>
                    <small class="text-muted">Nomor: {{ $surat->nomor_surat ?: '470 / ... / WN-KM / ' . date('Y') }}</small>
                </div>
                <div style="white-space: pre-wrap;">{{ $surat->isi_surat }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
