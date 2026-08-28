@extends('admin.layouts.admin')

@section('title', 'Manajemen Arsip Surat')
@section('page-title', 'Arsip Surat Resmi & Cadangan Data')

@section('content')
<!-- STATISTIK RINGKAS ARSIP -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Surat Terarsip</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($totalArsip) }}</h3>
                    <small class="text-muted">Surat resmi terstempel digital</small>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                    <i class="bi bi-archive-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Arsip Bulan Ini</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1">{{ number_format($arsipBulanIni) }}</h3>
                    <small class="text-muted">{{ date('F Y') }}</small>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Metode Penyimpanan</span>
                    <h5 class="fw-bold text-dark mb-0 mt-1">Hybrid Archive</h5>
                    <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Server Lokal + Backup GDrive</small>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ACTION & BACKUP TOOLBAR -->
<div class="card-custom mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-hdd-stack-fill text-success me-2"></i>Penyimpanan & Cadangan Arsip</h5>
            <p class="text-muted small mb-0">Kelola berkas digital di server lokal nagari dan sinkronkan cadangan ke Google Drive</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Tombol Export CSV / Excel -->
            <a href="{{ route('admin.arsip.export-csv', request()->query()) }}" class="btn btn-outline-success rounded-pill px-3 fw-semibold">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Unduh Rekap Excel/CSV
            </a>

            <!-- Tombol Backup JSON -->
            <a href="{{ route('admin.arsip.backup-json') }}" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-database-down me-1"></i> Unduh Backup Digital (.JSON)
            </a>

            <!-- Tombol Buka Google Drive -->
            <a href="{{ $gdriveFolderUrl }}" target="_blank" class="btn btn-warning rounded-pill px-3 fw-semibold text-dark">
                <i class="bi bi-google me-1"></i> Buka Google Drive Nagari
            </a>
        </div>
    </div>
</div>

<!-- FILTER & PENCARIAN -->
<div class="card-custom mb-4">
    <form method="GET" action="{{ route('admin.arsip.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Jenis Surat</label>
                <select name="jenis_surat" class="form-select form-select-sm">
                    <option value="">-- Semua 7 Jenis Surat --</option>
                    @foreach ($daftarSurat as $k => $v)
                        <option value="{{ $k }}" {{ request('jenis_surat') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">-- Semua Bulan --</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">-- Semua Tahun --</option>
                    @for ($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Cari Data</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="No. Surat / Nama / NIK..." value="{{ request('q') }}" />
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-success btn-sm rounded-pill w-100 fw-semibold">
                    <i class="bi bi-filter"></i> Filter
                </button>
                @if (request()->hasAny(['jenis_surat', 'bulan', 'tahun', 'q']))
                    <a href="{{ route('admin.arsip.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-2" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- TABEL ARSIP RESMI -->
<div class="card-custom">
    @if ($arsipList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>NO. SURAT RESMI</th>
                        <th>JENIS SURAT</th>
                        <th>PEMOHON & NIK</th>
                        <th>ALAMAT JORONG</th>
                        <th>TGL DISETUJUI WALI</th>
                        <th>STEMPEL DIGITAL</th>
                        <th class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($arsipList as $item)
                        <tr>
                            <td>
                                <strong class="text-success d-block font-monospace">{{ $item->nomor_surat }}</strong>
                                <small class="text-muted">ID: #{{ $item->id }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $item->nama_jenis_surat }}</span>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $item->nama_pemohon }}</strong>
                                <small class="text-muted">NIK: {{ $item->nik_pemohon ?: '-' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($item->alamat, 35) }}</small>
                            </td>
                            <td>
                                <small class="text-dark fw-semibold d-block">{{ $item->disetujui_wali_at ? $item->disetujui_wali_at->translatedFormat('d M Y') : '-' }}</small>
                                <small class="text-muted">{{ $item->disetujui_wali_at ? $item->disetujui_wali_at->format('H:i') . ' WIB' : '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-patch-check-fill"></i> Terstempel Sah
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('warga.surat.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" target="_blank" title="Lihat Dokumen">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('warga.surat.print', $item->id) }}" class="btn btn-sm btn-success rounded-pill px-3" target="_blank" title="Cetak Surat Resmi">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $arsipList->links() }}
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-archive fs-1 d-block mb-2 text-secondary opacity-50"></i>
            <h6 class="fw-bold">Belum Ada Berkas Surat di Arsip</h6>
            <p class="small mb-0">Surat yang telah diverifikasi admin dan disetujui Wali Nagari akan otomatis masuk ke sini.</p>
        </div>
    @endif
</div>
@endsection
