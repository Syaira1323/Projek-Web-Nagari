<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Panel') - Nagari Koto Malintang</title>
    <link rel="icon" href="{{ asset('image/logo agam.png') }}" type="image/png" />

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <style>
        :root {
            --sidebar-w: 260px;
            --bg-color: #f3f6f4;
            --primary-dark: #0f1d10;
            --primary-green: #198754;
            --primary-light: #20c997;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .admin-sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(180deg, #0f1d10 0%, #1a3620 100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 24px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand h6 {
            color: #4ade80;
            margin: 0;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .sidebar-user {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .nav-label-section {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            padding: 16px 16px 6px;
        }

        .nav-link-admin {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 10px;
            color: rgba(255,255,255,0.7);
            font-size: 0.88rem;
            text-decoration: none;
            margin: 2px 10px;
            transition: all 0.2s ease;
        }

        .nav-link-admin i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
        }

        .nav-link-admin:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }

        .nav-link-admin.active {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            box-shadow: 0 4px 12px rgba(25,135,84,0.35);
            font-weight: 600;
        }

        /* MAIN CONTENT */
        .admin-main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .admin-topbar {
            background: white;
            height: 65px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .admin-content {
            padding: 30px;
            flex: 1;
        }

        .card-custom {
            background: white;
            border-radius: 18px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            padding: 24px;
            margin-bottom: 24px;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR ADMIN -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('image/logo agam.png') }}" alt="Logo Agam" width="34" />
            <div>
                <h6>PANEL ADMIN</h6>
                <small class="text-white-50" style="font-size: 0.72rem;">Nagari Koto Malintang</small>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar-circle">A</div>
            <div class="text-truncate">
                <span class="text-white fw-bold d-block small">{{ auth()->user()->name }}</span>
                <small class="text-success fw-semibold">Administrator</small>
            </div>
        </div>

        <div class="py-2 flex-grow-1">
            <!-- Menu Utama -->
            <div class="nav-label-section">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('admin.surat.index') }}" class="nav-link-admin {{ request()->routeIs('admin.surat.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check-fill"></i> Pengajuan Surat
            </a>
            <a href="{{ route('admin.warga.index') }}" class="nav-link-admin {{ request()->routeIs('admin.warga.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Data Warga
            </a>
            <a href="{{ route('admin.gdrive.index') }}" class="nav-link-admin {{ request()->routeIs('admin.gdrive.*') ? 'active' : '' }}">
                <i class="bi bi-google-play"></i> Google Drive
            </a>
            <a href="{{ route('admin.pengaturan.index') }}" class="nav-link-admin {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Pengaturan
            </a>
            <a href="{{ route('admin.arsip.index') }}" class="nav-link-admin {{ request()->routeIs('admin.arsip.*') ? 'active' : '' }}">
                <i class="bi bi-archive-fill"></i> Arsip Surat & Cadangan
            </a>

            <!-- CMS Konten Website Nagari -->
            <div class="nav-label-section">Kelola Konten Publik</div>
            <a href="{{ route('admin.konten.profil') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.profil') ? 'active' : '' }}">
                <i class="bi bi-info-circle-fill"></i> Profil Nagari
            </a>
            <a href="{{ route('admin.konten.adat') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.adat') ? 'active' : '' }}">
                <i class="bi bi-feather"></i> Adat & Budaya
            </a>
            <a href="{{ route('admin.konten.visi-misi') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.visi-misi') ? 'active' : '' }}">
                <i class="bi bi-eye-fill"></i> Visi & Misi
            </a>
            <a href="{{ route('admin.konten.sejarah') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.sejarah') ? 'active' : '' }}">
                <i class="bi bi-hourglass-split"></i> Sejarah Nagari
            </a>
            <a href="{{ route('admin.konten.perangkat') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.perangkat') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i> Perangkat Nagari
            </a>
            <a href="{{ route('admin.konten.struktur') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.struktur') ? 'active' : '' }}">
                <i class="bi bi-diagram-3-fill"></i> Struktur Organisasi
            </a>
            <a href="{{ route('admin.konten.wisata') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.wisata') ? 'active' : '' }}">
                <i class="bi bi-compass-fill"></i> Objek Wisata
            </a>
            <a href="{{ route('admin.konten.umkm') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.umkm') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> UMKM Nagari
            </a>
            <a href="{{ route('admin.konten.kaba') }}" class="nav-link-admin {{ request()->routeIs('admin.konten.kaba') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i> Kaba Nagari
            </a>

            <!-- Keamanan Akun -->
            <div class="nav-label-section">Akun & Keamanan</div>
            <a href="{{ route('admin.password.index') }}" class="nav-link-admin {{ request()->routeIs('admin.password.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill text-warning"></i> Ubah Kata Sandi
            </a>

            <!-- Navigasi Luar -->
            <div class="nav-label-section">Lainnya</div>
            <a href="{{ route('beranda') }}" target="_blank" class="nav-link-admin">
                <i class="bi bi-globe"></i> Lihat Website Publik
            </a>
        </div>

        <div class="p-3 border-top border-secondary border-opacity-25">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm rounded-pill w-100 fw-semibold">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN AREA -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <h5 class="fw-bold text-dark mb-0">@yield('page-title', 'Panel Kontrol Administrator')</h5>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalUbahPasswordAdmin">
                    <i class="bi bi-key-fill me-1 text-warning"></i> Ubah Password
                </button>
                <a href="{{ route('beranda') }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Website
                </a>
            </div>
        </header>

        <!-- Content -->
        <main class="admin-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- MODAL UBAH PASSWORD ADMIN -->
    <div class="modal fade" id="modalUbahPasswordAdmin" tabindex="-1" aria-labelledby="modalUbahPasswordAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white p-4">
                        <h5 class="modal-title fw-bold" id="modalUbahPasswordAdminLabel">
                            <i class="bi bi-shield-lock text-warning me-2"></i>Ubah Password Akun Admin
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Password Lama Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" placeholder="Masukkan password lama Anda" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru" required />
                        </div>
                    </div>
                    <div class="modal-footer p-4 pt-0 border-0">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">Simpan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
