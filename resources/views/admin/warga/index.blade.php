@extends('admin.layouts.admin')

@section('title', 'Data Warga Nagari')
@section('page-title', 'Manajemen Data Warga Terdaftar')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-custom mb-0 d-flex align-items-center gap-3">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 fs-3">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalWarga }}</h3>
                <small class="text-muted">Total Warga Terdaftar di Sistem</small>
            </div>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Akun Masyarakat / Warga</h5>
            <small class="text-muted">Kelola dan pantau data identitas warga pemohon surat</small>
        </div>
        <form method="GET" action="{{ route('admin.warga.index') }}" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="Cari nama / NIK / jorong..." value="{{ request('search') }}" style="width: 260px;" />
            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                <i class="bi bi-search"></i>
            </button>
            @if (request('search'))
                <a href="{{ route('admin.warga.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>

    @if ($wargaList->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>NAMA LENGKAP / USERNAME</th>
                        <th>NIK</th>
                        <th>WHATSAPP / HP</th>
                        <th>JORONG / ALAMAT</th>
                        <th>TOTAL PERMOHONAN</th>
                        <th>TERDAFTAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($wargaList as $warga)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($warga->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong class="text-dark d-block">{{ $warga->name }}</strong>
                                        <small class="text-muted">{{ $warga->username }} &bull; {{ $warga->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($warga->nik)
                                    <span class="badge bg-light text-dark border">{{ $warga->nik }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $warga->phone) }}" target="_blank" class="text-success text-decoration-none small fw-semibold">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $warga->phone ?: '-' }}
                                </a>
                            </td>
                            <td><small class="text-muted">{{ Str::limit($warga->address ?: '-', 35) }}</small></td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">
                                    {{ $warga->surat_permohonans_count }} Surat
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $warga->created_at->format('d/m/Y') }}</small></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $wargaList->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
            <h6 class="fw-bold text-dark">Data Warga Tidak Ditemukan</h6>
            <p class="small mb-0">Belum ada warga yang terdaftar atau data pencarian tidak sesuai.</p>
        </div>
    @endif
</div>
@endsection
