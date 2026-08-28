<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Koto Malintang') - Nagari Koto Malintang</title>
    <link rel="icon" href="{{ asset('image/logo agam.png') }}" type="image/png" />

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />

    <!-- Footer styles -->
    <link href="{{ asset('footer-styles.css') }}" rel="stylesheet" />

    <style>
        /* === BASE STYLES === */
        html, body {
            overflow-x: hidden;
            max-width: 100%;
            position: relative;
        }

        * {
            box-sizing: border-box;
        }

        /* === NAVBAR BEHAVIOR === */
        .navbar {
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            z-index: 1050;
        }

        .navbar.transparent {
            background-color: transparent !important;
        }

        .navbar.scrolled {
            background-color: #198754 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        /* Desktop Dropdown Hover */
        @media (min-width: 992px) {
            .dropdown:hover > .dropdown-menu {
                display: block;
            }
            .dropdown-submenu {
                position: relative;
            }
            .dropdown-submenu .dropdown-menu {
                top: 0;
                left: 100%;
                margin-left: 0;
                display: none;
                position: absolute;
            }
            .dropdown-submenu:hover .dropdown-menu {
                display: block;
            }
        }

        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border: none;
        }

        .dropdown-item {
            padding: 8px 16px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #e8f5e9;
            color: #198754;
            padding-left: 20px;
        }

        .social-icons a {
            margin-left: 12px;
            color: white;
            font-size: 1.1rem;
            transition: color 0.2s ease, transform 0.2s ease;
            display: inline-block;
        }

        .social-icons a:hover {
            color: #ffc107;
            transform: scale(1.15);
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- NAVBAR (Match user frontend navbar) -->
    <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-success px-3" id="mainNavbar">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('beranda') }}">
                <img src="{{ asset('image/logo agam.png') }}" alt="Logo Agam" width="40" class="me-2" />
                NAGARI KOTO MALINTANG
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- MENU NAVIGASI -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('beranda') ? 'active fw-bold text-warning' : '' }}" href="{{ route('beranda') }}">
                            Beranda
                        </a>
                    </li>

                    <!-- Dropdown Profil Nagari -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('profil-nagari', 'sejarah', 'struktur-nagari', 'visi-misi', 'perangkat-nagari', 'umkm', 'adat-budaya-nagari') ? 'active fw-bold text-warning' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil Nagari
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ request()->routeIs('profil-nagari') ? 'active text-success' : '' }}" href="{{ route('profil-nagari') }}">Profil Nagari</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('sejarah') ? 'active text-success' : '' }}" href="{{ route('sejarah') }}">Sejarah Nagari</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('struktur-nagari') ? 'active text-success' : '' }}" href="{{ route('struktur-nagari') }}">Struktur Nagari</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('visi-misi') ? 'active text-success' : '' }}" href="{{ route('visi-misi') }}">Visi Misi</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('perangkat-nagari') ? 'active text-success' : '' }}" href="{{ route('perangkat-nagari') }}">Perangkat Nagari</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('umkm') ? 'active text-success' : '' }}" href="{{ route('umkm') }}">UMKM Nagari</a></li>
                        </ul>
                    </li>

                    <!-- Menu Lainnya -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('layanan-surat') ? 'active fw-bold text-warning' : '' }}" href="{{ route('layanan-surat') }}">
                            <i class="bi bi-file-earmark-text me-1"></i> Layanan Surat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('objek-wisata') ? 'active fw-bold text-warning' : '' }}" href="{{ route('objek-wisata') }}">Objek Wisata</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kaba-nagari') ? 'active fw-bold text-warning' : '' }}" href="{{ route('kaba-nagari') }}">Kaba Nagari</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://ppid.agamkab.go.id/" target="_blank">PPID</a>
                    </li>

                    <!-- MENU AUTENTIKASI / USER -->
                    @auth
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle btn btn-sm btn-outline-light rounded-pill px-3 text-white d-inline-flex align-items-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1 fs-6"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                @if (auth()->user()->isAdmin())
                                    <li><a class="dropdown-item fw-bold text-success" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2-fill me-2"></i>Panel Admin</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.surat.index') }}"><i class="bi bi-file-earmark-check me-2"></i>Verifikasi Surat</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.warga.index') }}"><i class="bi bi-people-fill me-2"></i>Data Warga</a></li>
                                @elseif (auth()->user()->isWaliNagari())
                                    <li><a class="dropdown-item fw-bold text-success" href="{{ route('wali-nagari.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Panel Wali Nagari</a></li>
                                    <li><a class="dropdown-item" href="{{ route('wali-nagari.surat.index') }}"><i class="bi bi-pen me-2"></i>Persetujuan Surat</a></li>
                                    <li><a class="dropdown-item" href="{{ route('wali-nagari.ttd-stempel.index') }}"><i class="bi bi-stamp me-2"></i>Tanda Tangan & Stempel</a></li>
                                @else
                                    <li><a class="dropdown-item fw-bold text-success" href="{{ route('warga.dashboard') }}"><i class="bi bi-house-door me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('warga.profil') }}"><i class="bi bi-person-lines-fill me-2"></i>Profil Saya</a></li>
                                    <li><a class="dropdown-item" href="{{ route('warga.surat.index') }}"><i class="bi bi-clock-history me-2"></i>Riwayat Surat</a></li>
                                    <li><a class="dropdown-item" href="{{ route('warga.bantuan') }}"><i class="bi bi-robot me-2 text-primary"></i>Bantuan / AI</a></li>
                                    <li><a class="dropdown-item" href="{{ route('warga.umkm.index') }}"><i class="bi bi-shop me-2"></i>UMKM Saya</a></li>
                                @endif
                                <li><a class="dropdown-item text-dark" href="#" data-bs-toggle="modal" data-bs-target="#modalUbahPasswordUniversal"><i class="bi bi-key-fill me-2 text-warning"></i>Ubah Kata Sandi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-2">
                            <a href="{{ route('login') }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk / Daftar
                            </a>
                        </li>
                    @endauth

                    <!-- Sosial Media -->
                    <li class="nav-item social-icons d-flex align-items-center ms-lg-2 mt-2 mt-lg-0">
                        <a href="https://facebook.com" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://instagram.com" target="_blank" title="Instagram"><i class="bi bi-instagram"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- === FOOTER WITH MAP (Match user frontend footer.html) === -->
    <footer class="elegant-footer">
        <div class="footer-background">
            <div class="footer-shapes">
                <div class="footer-shape footer-shape-1"></div>
                <div class="footer-shape footer-shape-2"></div>
                <div class="footer-shape footer-shape-3"></div>
            </div>
        </div>

        <div class="container position-relative">
            <div class="row g-4">
                <!-- Informasi Kontak -->
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h4 class="footer-title">
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            Nagari Koto Malintang
                        </h4>
                        <div class="footer-content">
                            <div class="contact-item">
                                <i class="bi bi-house-door-fill"></i>
                                <div>
                                    <strong>Alamat Kantor</strong>
                                    <p>Koto Malintang, Kec. Tanjung Raya, Kabupaten Agam, Sumatera Barat 26454</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="bi bi-telephone-fill"></i>
                                <div>
                                    <strong>Telepon</strong>
                                    <p>+62 752 123456</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="bi bi-envelope-fill"></i>
                                <div>
                                    <strong>Email</strong>
                                    <p>info@kotomalintang.go.id</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jam Pelayanan -->
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h4 class="footer-title">
                            <i class="bi bi-clock-fill me-2"></i>
                            Jam Pelayanan
                        </h4>
                        <div class="footer-content">
                            <div class="schedule-item">
                                <div class="day">Senin - Kamis</div>
                                <div class="time">08:00 - 16:00 WIB</div>
                            </div>
                            <div class="schedule-item">
                                <div class="day">Jumat</div>
                                <div class="time">08:00 - 11:30 WIB</div>
                            </div>
                            <div class="schedule-item">
                                <div class="day">Sabtu - Minggu</div>
                                <div class="time">Tutup</div>
                            </div>
                            <div class="emergency-contact">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span>Layanan Darurat: 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Peta Lokasi -->
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h4 class="footer-title">
                            <i class="bi bi-map-fill me-2"></i>
                            Lokasi Kami
                        </h4>
                        <div class="map-container">
                            <div class="map-placeholder">
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.817267894847!2d100.2134567!3d-0.2876543!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd4b942e2b9d2c5%3A0x5c5c5c5c5c5c5c5c!2sKoto%20Malintang%2C%20Agam%2C%20West%20Sumatra!5e0!3m2!1sen!2sid!4v1234567890123"
                                    width="100%" 
                                    height="200" 
                                    style="border:0; border-radius: 15px;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                            <div class="map-actions">
                                <a href="https://maps.google.com/?q=Nagari+Koto+Malintang+Agam" target="_blank" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-arrow-up-right-square"></i>
                                    Buka di Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="footer-logo">
                            <img src="{{ asset('image/logo agam.png') }}" alt="Logo Agam" width="40" class="me-3">
                            <div>
                                <h6 class="mb-0">Nagari Koto Malintang</h6>
                                <small>Kabupaten Agam, Sumatera Barat</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-youtube"></i></a>
                            <a href="mailto:info@kotomalintang.go.id" class="social-link"><i class="bi bi-envelope"></i></a>
                        </div>
                        <p class="copyright">
                            &copy; {{ date('Y') }} Nagari Koto Malintang. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @auth
        <!-- MODAL UBAH PASSWORD UNIVERSAL -->
        <div class="modal fade" id="modalUbahPasswordUniversal" tabindex="-1" aria-labelledby="modalUbahPasswordUniversalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-4 border-0">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <div class="modal-header bg-dark text-white p-4">
                            <h5 class="modal-title fw-bold" id="modalUbahPasswordUniversalLabel">
                                <i class="bi bi-shield-lock text-warning me-2"></i>Ubah Password Akun
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
    @endauth

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Navbar Scroll Effect -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
