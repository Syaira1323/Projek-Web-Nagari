@extends('admin.layouts.admin')

@section('title', 'Pengaturan Google Drive')
@section('page-title', 'Integrasi Google Drive Cloud Storage')

@section('content')
<div class="row g-4 mb-4">
    <!-- Status Koneksi Box -->
    <div class="col-lg-5">
        <div class="card-custom h-100">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-google text-danger me-2"></i>Status Koneksi Google Drive
            </h5>

            <div class="p-4 rounded-4 text-center mb-4 {{ $isConnected ? 'bg-success bg-opacity-10 border border-success' : 'bg-warning bg-opacity-10 border border-warning' }}">
                @if ($isConnected)
                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px; font-size: 1.8rem;">
                        <i class="bi bi-cloud-check-fill"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1">Google Drive Terhubung</h5>
                    <p class="small text-muted mb-2">Akun: <strong>{{ $connectedEmail }}</strong></p>
                    <span class="badge bg-success rounded-pill px-3 py-1">Siap Menyimpan Berkas & Surat</span>
                @else
                    <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px; font-size: 1.8rem;">
                        <i class="bi bi-cloud-slash"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Terhubung ke Google Drive</h5>
                    <p class="small text-muted mb-2">Penyimpanan sementara menggunakan <strong>Local Storage</strong> secara aman.</p>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Memerlukan Otorisasi OAuth</span>
                @endif
            </div>

            <!-- Tombol Aksi Koneksi -->
            <div class="d-flex flex-column gap-2">
                @if ($isConnected)
                    <form action="{{ route('admin.gdrive.test') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-success w-100 rounded-pill fw-semibold">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Uji Koneksi & Cek Kuota
                        </button>
                    </form>
                    <form action="{{ route('admin.gdrive.disconnect') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan koneksi Google Drive?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-semibold">
                            <i class="bi bi-x-circle me-1"></i> Putus Koneksi
                        </button>
                    </form>
                @else
                    @if ($authUrl)
                        <a href="{{ $authUrl }}" class="btn btn-danger w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="bi bi-google me-1"></i> Hubungkan Akun Google Drive
                        </a>
                    @else
                        <div class="alert alert-info small mb-0 rounded-3">
                            <i class="bi bi-info-circle me-1"></i> Silakan isi <strong>Client ID</strong> dan <strong>Client Secret</strong> di samping untuk mengaktifkan tombol otorisasi.
                        </div>
                    @endif
                @endif
            </div>

            <!-- Hirarki Folder Info -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold text-dark mb-2 small text-uppercase">Hirarki Folder Otomatis:</h6>
                <div class="bg-light p-3 rounded-3 small font-monospace" style="font-size: 0.82rem;">
                    📁 Layanan Surat Nagari Koto Malintang<br>
                    &nbsp;&nbsp;├── 📁 Berkas Persyaratan / 2026 / ID_Pengajuan<br>
                    &nbsp;&nbsp;└── 📁 Surat Selesai / 2026 / ID_Surat
                </div>
            </div>
        </div>
    </div>

    <!-- Form Konfigurasi OAuth Kredensial -->
    <div class="col-lg-7">
        <div class="card-custom h-100">
            <h5 class="fw-bold text-dark mb-2">
                <i class="bi bi-key-fill text-warning me-2"></i>Konfigurasi Google Cloud Console
            </h5>
            <p class="text-muted small mb-4">
                Dapatkan Client ID & Secret dari <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="fw-semibold text-success">Google Cloud Console</a> dengan mengaktifkan <strong>Google Drive API</strong>.
            </p>

            <form action="{{ route('admin.gdrive.save-credentials') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Google Client ID <span class="text-danger">*</span></label>
                    <input type="text" name="client_id" class="form-control" placeholder="Contoh: 123456789-abc.apps.googleusercontent.com" value="{{ old('client_id', $clientId) }}" required />
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Google Client Secret <span class="text-danger">*</span></label>
                    <input type="password" name="client_secret" class="form-control" placeholder="Masukkan Client Secret Google OAuth" value="{{ old('client_secret', $clientSecret) }}" required />
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">Authorized Redirect URI (Masukkan ini di Google Console):</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light font-monospace small" value="{{ $redirectUri }}" readonly id="redirectUriInput" />
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('redirectUriInput').value); alert('URL disalin ke clipboard!');">
                            <i class="bi bi-clipboard"></i> Salin
                        </button>
                    </div>
                    <small class="text-muted" style="font-size: 0.78rem;">Salin URL ini ke bagian "Authorized redirect URIs" pada OAuth 2.0 Client di Google Cloud Console.</small>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Kredensial OAuth
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
