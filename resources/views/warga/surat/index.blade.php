@extends('layouts.app')

@section('title', 'Riwayat Surat Saya')

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
            <h3 class="fw-bold text-dark mb-1">Riwayat Permohonan Surat Saya</h3>
            <p class="text-muted mb-0">Seluruh pengajuan surat keterangan dan pengantar resmi Nagari Koto Malintang</p>
        </div>
        <a href="{{ route('warga.surat.create') }}" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Surat Baru
        </a>
    </div>

    <div class="card-panel">
        @if ($suratList->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>NOMOR PENGAJUAN</th>
                            <th>JENIS SURAT</th>
                            <th>TANGGAL PENGAJUAN</th>
                            <th>STATUS & ALASAN</th>
                            <th>KETERANGAN</th>
                            <th>TANGGAL DIPROSES</th>
                            <th>TANGGAL SELESAI</th>
                            <th class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suratList as $surat)
                            @php
                                $isMenunggu = $surat->status === 'menunggu_admin';
                                $isDiproses = $surat->status === 'menunggu_wali_nagari';
                                $isDisetujui = $surat->status === 'disetujui';
                                $isDitolak = $surat->status === 'ditolak';
                            @endphp
                            <tr>
                                <!-- Nomor Pengajuan -->
                                <td>
                                    <strong class="text-primary font-monospace">#KM-{{ str_pad($surat->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                    @if ($surat->nomor_surat)
                                        <small class="d-block text-success fw-semibold font-monospace" style="font-size: 0.78rem;">
                                            {{ $surat->nomor_surat }}
                                        </small>
                                    @endif
                                </td>

                                <!-- Jenis Surat -->
                                <td>
                                    <strong class="text-dark d-block">{{ $surat->nama_jenis_surat }}</strong>
                                    <small class="text-muted">Pemohon: {{ $surat->nama_pemohon }}</small>
                                </td>

                                <!-- Tanggal Pengajuan -->
                                <td>
                                    <small class="text-muted d-block">{{ $surat->created_at->format('d/m/Y') }}</small>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $surat->created_at->format('H:i') }} WIB</small>
                                </td>

                                <!-- Status & Alasan Penolakan -->
                                <td>
                                    @if ($isMenunggu)
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Menunggu</span>
                                    @elseif ($isDiproses)
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-1">Diproses</span>
                                    @elseif ($isDisetujui)
                                        <span class="badge bg-success rounded-pill px-3 py-1">Selesai (Disetujui)</span>
                                    @elseif ($isDitolak)
                                        <span class="badge bg-danger rounded-pill px-3 py-1">Ditolak</span>
                                        <div class="mt-1 p-2 rounded bg-danger bg-opacity-10 border border-danger border-opacity-25" style="max-width: 240px;">
                                            <strong class="text-danger d-block small" style="font-size: 0.75rem;"><i class="bi bi-exclamation-circle me-1"></i>Alasan Ditolak:</strong>
                                            <small class="text-danger d-block" style="font-size: 0.75rem;">
                                                {{ $surat->catatan_admin ?: $surat->catatan_wali ?: 'Persyaratan berkas belum lengkap.' }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-1">{{ $surat->status_label }}</span>
                                    @endif
                                </td>

                                <!-- Keterangan / Keperluan -->
                                <td>
                                    <small class="text-muted d-block" style="max-width: 180px;">{{ Str::limit($surat->keperluan, 45) }}</small>
                                </td>

                                <!-- Tanggal Diproses -->
                                <td>
                                    @if (!$isMenunggu)
                                        <small class="text-dark d-block">{{ $surat->updated_at->format('d/m/Y') }}</small>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $surat->updated_at->format('H:i') }} WIB</small>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- Tanggal Selesai -->
                                <td>
                                    @if ($surat->disetujui_wali_at)
                                        <small class="text-success fw-semibold d-block">{{ $surat->disetujui_wali_at->format('d/m/Y') }}</small>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $surat->disetujui_wali_at->format('H:i') }} WIB</small>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('warga.surat.show', $surat->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                        @if ($isDisetujui)
                                            <a href="{{ route('warga.surat.print', $surat->id) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 ms-1 shadow-sm">
                                                <i class="bi bi-printer me-1"></i> Cetak
                                            </a>
                                        @endif
                                    </div>
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
                <i class="bi bi-file-earmark-x fs-1 d-block mb-3 text-secondary"></i>
                <h5 class="fw-bold text-dark">Belum Ada Permohonan Surat</h5>
                <p class="small">Silakan ajukan surat keterangan atau pengantar yang Anda butuhkan secara online.</p>
                <a href="{{ route('warga.surat.create') }}" class="btn btn-success rounded-pill px-4 py-2 mt-2 shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajukan Surat Sekarang
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
