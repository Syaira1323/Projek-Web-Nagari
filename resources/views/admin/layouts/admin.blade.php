<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Panel Administrator') - Nagari Koto Malintang</title>
    <link rel="icon" href="{{ asset('image/logo agam.png') }}" type="image/png" />

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <style>
        :root {
            --hijau-tua: #0f1d10;
            --hijau-primer: #1e3a20;
            --hijau-aksen: #198754;
            --bg-admin: #f8fafc;
            --sidebar-w: 260px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-admin);
            color: #1e293b;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar, .admin-sidebar {
            width: var(--sidebar-w);
            background: var(--hijau-primer);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            z-index: 1040;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-header img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .sidebar-header h6 {
            margin: 0;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
        }

        .sidebar-header small {
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
        }

        .sidebar-user {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0,0,0,0.15);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #22c55e;
            color: #064e3b;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar-user-info span {
            font-weight: 600;
            font-size: 0.88rem;
            display: block;
            color: #ffffff;
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-info small {
            color: #86efac;
            font-size: 0.75rem;
            display: block;
        }

        .sidebar-nav {
            padding: 15px 12px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.45);
            padding: 10px 12px 4px;
            font-weight: 700;
        }

        .nav-item-admin, .nav-link-admin {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.86rem;
            font-weight: 500;
            margin-bottom: 3px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .nav-item-admin:hover, .nav-link-admin:hover,
        .nav-item-admin.active, .nav-link-admin.active {
            background: var(--hijau-aksen);
            color: white;
        }

        .nav-item-admin i, .nav-link-admin i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .badge-count {
            margin-left: auto;
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 700;
        }

        .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }

        /* MAIN AREA */
        .main-area, .admin-main {
            margin-left: var(--sidebar-w);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
        }

        .topbar, .admin-topbar {
            height: 65px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .content-area, .admin-content {
            padding: 25px;
            flex-grow: 1;
        }

        /* CARDS & PANELS */
        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            height: 100%;
        }

        .stat-card .num {
            font-size: 1.7rem;
            font-weight: 800;
            color: #0f172a;
            margin: 6px 0 2px;
        }

        .stat-card .lbl {
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 500;
        }

        .admin-panel, .card-custom, .card {
            background: white;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .panel-header, .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
        }

        .panel-header h6, .panel-header h5, .card-header h5, .card-header h6 {
            margin: 0;
            font-weight: 700;
            font-size: 0.95rem;
            color: #0f172a;
        }

        .panel-body, .card-body {
            padding: 20px;
        }

        /* TABLES */
        .admin-table, .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .admin-table th, .table th, .table thead th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 0.78rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            border-top: none;
        }

        .admin-table td, .table td, .table tbody td {
            padding: 14px 16px;
            font-size: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #1e293b;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }

        /* BUTTONS */
        .btn-hijau {
            background: var(--hijau-aksen);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-hijau:hover {
            background: #146c43;
            color: white;
        }

        .btn-outline-hijau {
            background: transparent;
            color: var(--hijau-aksen);
            border: 1px solid var(--hijau-aksen);
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-hijau:hover {
            background: var(--hijau-aksen);
            color: white;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.88rem;
            padding: 8px 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
        }

        /* MOBILE RESPONSIVENESS */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1030;
        }

        @media (max-width: 991px) {
            .sidebar, .admin-sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open, .admin-sidebar.open {
                transform: translateX(0);
            }
            .sidebar-backdrop.show {
                display: block;
            }
            .main-area, .admin-main {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR BACKDROP FOR MOBILE -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR ADMIN -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('image/logo agam.png') }}" alt="Logo Agam" />
            <div>
                <h6>Koto Malintang</h6>
                <small>Panel Administrator</small>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <span>{{ auth()->user()->name ?? 'Administrator Nagari' }}</span>
                <small><i class="bi bi-shield-lock-fill me-1 text-warning"></i>Admin Pengelola</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <!-- Menu Utama -->
            <div class="nav-section-label">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.surat.index') }}" class="nav-item-admin {{ request()->routeIs('admin.surat.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-check-fill"></i> Verifikasi & Nomor Surat
            </a>
            <a href="{{ route('admin.warga.index') }}" class="nav-item-admin {{ request()->routeIs('admin.warga.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Data Pengguna Warga
            </a>
            <a href="{{ route('admin.arsip.index') }}" class="nav-item-admin {{ request()->routeIs('admin.arsip.*') ? 'active' : '' }}">
                <i class="bi bi-archive-fill"></i> Berkas Arsip Surat
            </a>
            <a href="{{ route('admin.pengaturan.index') }}" class="nav-item-admin {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Pengaturan Sistem
            </a>

            <!-- CMS Konten Website Nagari -->
            <div class="nav-section-label">Konten Publik</div>
            <a href="{{ route('admin.konten.profil') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.profil') ? 'active' : '' }}">
                <i class="bi bi-info-circle-fill"></i> Profil Nagari
            </a>
            <a href="{{ route('admin.konten.adat') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.adat') ? 'active' : '' }}">
                <i class="bi bi-feather"></i> Adat & Budaya
            </a>
            <a href="{{ route('admin.konten.visi-misi') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.visi-misi') ? 'active' : '' }}">
                <i class="bi bi-eye-fill"></i> Visi & Misi
            </a>
            <a href="{{ route('admin.konten.sejarah') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.sejarah') ? 'active' : '' }}">
                <i class="bi bi-hourglass-split"></i> Sejarah Nagari
            </a>
            <a href="{{ route('admin.konten.perangkat') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.perangkat') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i> Perangkat Nagari
            </a>
            <a href="{{ route('admin.konten.struktur') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.struktur') ? 'active' : '' }}">
                <i class="bi bi-diagram-3-fill"></i> Struktur Organisasi
            </a>
            <a href="{{ route('admin.konten.wisata') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.wisata') ? 'active' : '' }}">
                <i class="bi bi-compass-fill"></i> Objek Wisata
            </a>
            <a href="{{ route('admin.konten.umkm') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.umkm') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> UMKM Nagari
            </a>
            <a href="{{ route('admin.konten.kaba') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.kaba') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i> Kaba Nagari (Berita)
            </a>
            <a href="{{ route('admin.konten.galeri') }}" class="nav-item-admin {{ request()->routeIs('admin.konten.galeri') ? 'active' : '' }}">
                <i class="bi bi-images"></i> Galeri & Media
            </a>

            <!-- Keamanan Akun -->
            <div class="nav-section-label">Akun & Keamanan</div>
            <a href="{{ route('admin.password.index') }}" class="nav-item-admin {{ request()->routeIs('admin.password.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill text-warning"></i> Ubah Kata Sandi
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('beranda') }}" target="_blank" class="nav-item-admin text-white mb-2">
                <i class="bi bi-globe"></i> Portal Warga
            </a>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="nav-item-admin text-danger border-0 bg-transparent w-100 text-start p-2" style="cursor: pointer;">
                    <i class="bi bi-box-arrow-right text-danger"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN AREA -->
    <div class="main-area">
        <!-- TOPBAR -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 fw-bold" id="pageTitle">@yield('page-title', 'Dashboard Administrator')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalUbahPasswordAdmin">
                    <i class="bi bi-key-fill me-1 text-warning"></i> Ubah Password
                </button>
                <a href="{{ route('beranda') }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="bi bi-globe me-1"></i> Website Publik
                </a>
                <div class="user-avatar" style="width:36px;height:36px;font-size:0.9rem;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="content-area">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 border-0 shadow-sm" role="alert" style="background: #f0fdf4; color: #166534; border-left: 4px solid #16a34a !important;">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4 border-0 shadow-sm" role="alert" style="background: #fffbeb; color: #92400e; border-left: 4px solid #f59e0b !important;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 border-0 shadow-sm" role="alert" style="background: #fef2f2; color: #991b1b; border-left: 4px solid #dc2626 !important;">
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-1 ps-3 small">
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom p-4">
                        <h6 class="modal-title fw-bold" id="modalUbahPasswordAdminLabel">
                            <i class="bi bi-shield-lock-fill text-warning me-2"></i>Ubah Kata Sandi Akun Admin
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <div class="modal-footer p-3 border-top bg-light">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-hijau">Simpan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sb = document.getElementById('sidebar');
            const bd = document.getElementById('sidebarBackdrop');
            if (sb) sb.classList.toggle('open');
            if (bd) bd.classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
