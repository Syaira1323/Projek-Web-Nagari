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
                    <span class="text-muted small fw-bold text-uppercase">Status Google Drive</span>
                    <h5 class="fw-bold {{ $isConnected ? 'text-success' : 'text-warning' }} mb-0 mt-1">
                        {{ $isConnected ? 'Terhubung' : 'Belum Terhubung' }}
                    </h5>
                    <small class="text-muted">{{ $isConnected ? $connectedEmail : 'Penyimpanan server lokal' }}</small>
                </div>
                <div class="rounded-circle {{ $isConnected ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }} d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                    <i class="bi {{ $isConnected ? 'bi-cloud-check-fill' : 'bi-cloud-slash-fill' }}"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KONEKSI PENYIMPANAN GOOGLE DRIVE & TOOLBAR CADANGAN -->
<div class="card-custom mb-4">
    <div class="p-3 bg-light rounded-3 mb-3 border">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle {{ $isConnected ? 'bg-success text-white' : 'bg-warning text-dark' }} d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.3rem;">
                    <i class="bi {{ $isConnected ? 'bi-google' : 'bi-cloud-slash' }}"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold text-dark mb-0">Penyimpanan Google Drive</h6>
                        @if ($isConnected)
                            <span class="badge bg-success rounded-pill px-2 py-1 small">
                                <i class="bi bi-check-circle-fill me-1"></i> Google Drive Terhubung
                            </span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Google Drive Belum Terhubung
                            </span>
                        @endif
                    </div>
                    <p class="small text-muted mb-0 mt-1">
                        @if ($isConnected)
                            Akun Aktif: <strong>{{ $connectedEmail }}</strong> — Surat selesai otomatis terarsip ke folder <code>Layanan Surat Nagari Koto Malintang / Surat Selesai / {{ date('Y') }}</code>
                        @else
                            Arsip surat saat ini tersimpan di server lokal. Hubungkan akun Google Drive resmi Nagari untuk sinkronisasi otomatis.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Kontrol Aksi Google Drive -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if ($isConnected)
                    <form action="{{ route('admin.gdrive.test') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold" title="Uji Koneksi Google Drive">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Tes Koneksi
                        </button>
                    </form>
                    <form action="{{ route('admin.gdrive.disconnect') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan koneksi Google Drive?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold" title="Putus Koneksi Google Drive">
                            <i class="bi bi-x-circle me-1"></i> Putuskan Koneksi
                        </button>
                    </form>
                    <a href="{{ $gdriveFolderUrl }}" target="_blank" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-dark">
                        <i class="bi bi-google me-1"></i> Buka Google Drive
                    </a>
                @else
                    @if ($authUrl)
                        <a href="{{ $authUrl }}" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-google me-1"></i> Hubungkan Google Drive
                        </a>
                    @else
                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalOAuthCredentials">
                            <i class="bi bi-key-fill me-1"></i> Atur Kredensial OAuth
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- TOOLBAR UNDUH REKAP & CADANGAN -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2">
        <div>
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-arrow-down-fill text-success me-2"></i>Ekspor & Cadangan Data</h6>
            <small class="text-muted">Unduh rekapitulasi data surat selesai dalam format tabel Excel/CSV atau backup JSON lengkap</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Tombol Export CSV / Excel -->
            <a href="{{ route('admin.arsip.export-csv', request()->query()) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Unduh Rekap Excel/CSV
            </a>

            <!-- Tombol Backup JSON -->
            <a href="{{ route('admin.arsip.backup-json') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-database-down me-1"></i> Unduh Backup Digital (.JSON)
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
                        <th>STATUS GOOGLE DRIVE</th>
                        <th class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($arsipList as $item)
                        @php
                            $ds = $item->data_spesifik ?? [];
                            if (!is_array($ds)) {
                                $ds = json_decode((string)$ds, true) ?? [];
                            }
                            $arsipGdrive = $ds['arsip_gdrive'] ?? null;
                        @endphp
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
                                <small class="text-muted">{{ Str::limit($item->alamat, 30) }}</small>
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
                            <td>
                                @if (!empty($arsipGdrive['gdrive_pdf_id']))
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-cloud-check-fill text-success"></i> Terarsip di GDrive
                                    </span>
                                    @if (!empty($arsipGdrive['gdrive_pdf_url']))
                                        <a href="{{ $arsipGdrive['gdrive_pdf_url'] }}" target="_blank" class="btn btn-sm btn-link text-success p-0 ms-1" title="Buka Dokumen di Google Drive">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    @endif
                                @elseif (($arsipGdrive['storage_mode'] ?? '') === 'local')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-hdd-fill text-secondary"></i> Tersimpan Lokal
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-dash-circle"></i> Belum Diarsipkan
                                    </span>
                                @endif
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

<!-- MODAL KONFIGURASI OAUTH CREDENTIALS (OPSIONAL JIKA BELUM ADA DI ENV) -->
<div class="modal fade" id="modalOAuthCredentials" tabindex="-1" aria-labelledby="modalOAuthCredentialsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.gdrive.save-credentials') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom p-4">
                    <h6 class="modal-title fw-bold" id="modalOAuthCredentialsLabel">
                        <i class="bi bi-key-fill text-warning me-2"></i>Konfigurasi OAuth Google Drive
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Google Client ID <span class="text-danger">*</span></label>
                        <input type="text" name="client_id" class="form-control" placeholder="Contoh: 123456789-abc.apps.googleusercontent.com" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Google Client Secret <span class="text-danger">*</span></label>
                        <input type="password" name="client_secret" class="form-control" placeholder="Masukkan Client Secret Google OAuth" required />
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Authorized Redirect URI:</label>
                        <input type="text" class="form-control bg-light font-monospace small" value="{{ $redirectUri }}" readonly />
                        <small class="text-muted" style="font-size: 0.78rem;">Pastikan URI ini terdaftar di Google Cloud Console.</small>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top bg-light">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-hijau">Simpan Kredensial</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
