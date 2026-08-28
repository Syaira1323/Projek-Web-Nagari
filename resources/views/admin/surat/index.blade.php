@extends('admin.layouts.admin')

@section('title', 'Verifikasi Surat Masuk')
@section('page-title', 'Daftar Permohonan Surat Masuk')

@section('content')
<!-- FILTER TABS -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('admin.surat.index') }}" class="btn btn-sm {{ !$status ? 'btn-success' : 'btn-light border' }} rounded-pill px-3 fw-semibold">
        Semua ({{ $counts['semua'] }})
    </a>
    <a href="{{ route('admin.surat.index', ['status' => 'menunggu_admin']) }}" class="btn btn-sm {{ $status === 'menunggu_admin' ? 'btn-warning text-dark' : 'btn-light border' }} rounded-pill px-3 fw-semibold">
        Menunggu Verifikasi ({{ $counts['menunggu_admin'] }})
    </a>
    <a href="{{ route('admin.surat.index', ['status' => 'menunggu_wali_nagari']) }}" class="btn btn-sm {{ $status === 'menunggu_wali_nagari' ? 'btn-info text-dark' : 'btn-light border' }} rounded-pill px-3 fw-semibold">
        Di Wali Nagari ({{ $counts['menunggu_wali_nagari'] }})
    </a>
    <a href="{{ route('admin.surat.index', ['status' => 'disetujui']) }}" class="btn btn-sm {{ $status === 'disetujui' ? 'btn-success' : 'btn-light border' }} rounded-pill px-3 fw-semibold">
        Disetujui / Selesai ({{ $counts['disetujui'] }})
    </a>
    <a href="{{ route('admin.surat.index', ['status' => 'ditolak']) }}" class="btn btn-sm {{ $status === 'ditolak' ? 'btn-danger' : 'btn-light border' }} rounded-pill px-3 fw-semibold">
        Ditolak ({{ $counts['ditolak'] }})
    </a>
</div>

<div class="card-custom">
    @if ($suratList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>NO</th>
                        <th>PEMOHON / NIK</th>
                        <th>JENIS SURAT</th>
                        <th>KEPERLUAN</th>
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
                                <small class="text-success">{{ $surat->nomor_surat ?: 'Belum terbit nomor' }}</small>
                            </td>
                            <td><small class="text-muted">{{ Str::limit($surat->keperluan, 40) }}</small></td>
                            <td><small class="text-muted">{{ $surat->created_at->translatedFormat('d M Y, H:i') }}</small></td>
                            <td>
                                <span class="badge bg-{{ $surat->status_badge_color }} rounded-pill px-3 py-1 small">
                                    {{ $surat->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.surat.show', $surat->id) }}" class="btn btn-sm btn-success rounded-pill px-3">
                                    <i class="bi bi-search me-1"></i> Periksa
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
@endsection
