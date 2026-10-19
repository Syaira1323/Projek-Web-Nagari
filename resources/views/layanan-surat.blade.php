@extends('layouts.app')

@section('title', 'Layanan Surat Nagari - 7 Jenis Surat & AI Chatbot')

@push('styles')
<style>
    body {
        background: #f8fafc;
        padding-top: 75px;
    }

    /* === HERO SECTION === */
    .hero-surat-public {
        background: linear-gradient(135deg, #093c1f 0%, #198754 60%, #157347 100%);
        position: relative;
        overflow: hidden;
        padding: 55px 0 50px;
        color: white;
        margin-bottom: 35px;
    }

    .hero-surat-public::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url("{{ asset('image/forest1.jpg') }}") center/cover no-repeat;
        opacity: 0.12;
        pointer-events: none;
    }

    .hero-title {
        font-size: 2.6rem;
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: -0.5px;
    }

    .stat-pill {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 50rem;
        padding: 6px 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #fff;
    }

    /* === STEP WORKFLOW === */
    .step-card {
        background: white;
        border-radius: 16px;
        padding: 22px 18px;
        border: 1px solid #e2e8f0;
        transition: all 0.25s ease;
        height: 100%;
        text-align: center;
        position: relative;
    }

    .step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(25, 135, 84, 0.1);
        border-color: #198754;
    }

    .step-num {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #198754, #20c997);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.15rem;
        margin: 0 auto 12px;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.25);
    }

    /* === AI CHATBOT BOX === */
    .ai-box-card {
        background: white;
        border-radius: 20px;
        border: 1.5px solid #d1e7dd;
        box-shadow: 0 12px 35px rgba(25, 135, 84, 0.1);
        overflow: hidden;
    }

    .ai-chat-header {
        background: linear-gradient(135deg, #198754, #0f5132);
        color: white;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ai-chat-body {
        padding: 20px;
        height: 320px;
        overflow-y: auto;
        background: #fdfefe;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-bubble {
        padding: 12px 16px;
        font-size: 0.9rem;
        line-height: 1.55;
        max-width: 88%;
        word-wrap: break-word;
    }

    .ai-msg {
        background: #f0faf4;
        border-radius: 4px 18px 18px 18px;
        align-self: flex-start;
        color: #1b3d2f;
        border-left: 3.5px solid #198754;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }

    .user-msg {
        background: #198754;
        border-radius: 18px 4px 18px 18px;
        align-self: flex-end;
        color: white;
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.2);
    }

    .quick-chip {
        display: inline-block;
        background: #e8f5e9;
        color: #198754;
        border: 1px solid #c8e6c9;
        border-radius: 50rem;
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        margin: 3px 2px;
        transition: all 0.2s;
    }

    .quick-chip:hover {
        background: #198754;
        color: white;
        border-color: #198754;
        transform: translateY(-1px);
    }

    /* === 7 SURAT CATALOG CARDS === */
    .catalog-card {
        background: white;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        transition: all 0.28s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .catalog-card:hover {
        transform: translateY(-5px);
        border-color: #198754;
        box-shadow: 0 12px 30px rgba(25, 135, 84, 0.12);
    }

    .catalog-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #e8f5e9;
        color: #198754;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .persyaratan-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 18px;
    }

    .persyaratan-list li {
        font-size: 0.83rem;
        color: #4a5568;
        margin-bottom: 6px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .persyaratan-list li i {
        color: #198754;
        font-size: 0.95rem;
        margin-top: 1px;
        flex-shrink: 0;
    }

    /* === MODAL PREVIEW SURAT === */
    .official-letter-paper {
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 35px 40px;
        font-family: 'Times New Roman', Times, serif;
        color: #111;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }

    .kop-garis-ganda {
        border-top: 3px solid #000;
        border-bottom: 1px solid #000;
        height: 6px;
        margin: 12px 0 20px;
    }

    .signature-box {
        position: relative;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .official-seal-img {
        position: absolute;
        left: 20px;
        top: -5px;
        width: 90px;
        height: 90px;
        opacity: 0.88;
        transform: rotate(-8deg);
        pointer-events: none;
    }

    .digital-sign-text {
        font-family: 'Pacifico', cursive, serif;
        font-size: 1.45rem;
        color: #1a365d;
        transform: rotate(-6deg);
        font-weight: bold;
        position: relative;
        z-index: 2;
    }
</style>
@endpush

@section('content')
<!-- HERO SECTION -->
<header class="hero-surat-public">
    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-patch-check-fill me-1"></i> Layanan Administrasi Publik Resmi
                    </span>
                    <span class="stat-pill">
                        <i class="bi bi-clock-history"></i> Buka 24 Jam Online
                    </span>
                    <span class="stat-pill">
                        <i class="bi bi-shield-check text-warning"></i> 100% Gratis Tanpa Pungutan
                    </span>
                </div>
                <h1 class="hero-title mb-3">Layanan Surat Nagari Koto Malintang</h1>
                <p class="lead opacity-90 mb-4" style="font-size: 1.15rem; max-width: 680px;">
                    Informasi resmi persyaratan, alur, dan format <strong>7 jenis surat keterangan</strong> Nagari Koto Malintang, didukung Asisten Cerdas <strong>eToma AI</strong> untuk konsultasi instan.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#katalogSurat" class="btn btn-warning btn-lg rounded-pill px-4 shadow text-dark fw-bold">
                        <i class="bi bi-grid-fill me-1"></i> Jelajahi 7 Jenis Surat
                    </a>
                    <a href="#chatbotSection" class="btn btn-outline-light btn-lg rounded-pill px-4 shadow">
                        <i class="bi bi-robot me-1"></i> Tanya Chatbot AI
                    </a>
                    @auth
                        <a href="{{ route('warga.surat.create') }}" class="btn btn-light btn-lg rounded-pill px-4 fw-bold text-success shadow">
                            <i class="bi bi-pencil-square me-1"></i> Ajukan Surat Sekarang
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg rounded-pill px-4 fw-bold text-success shadow">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Panel Warga
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="bg-white bg-opacity-10 p-4 rounded-4 border border-white border-opacity-25 backdrop-blur shadow-sm">
                    <div class="mb-3">
                        <i class="bi bi-file-earmark-check-fill text-warning display-4"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Terverifikasi & Sah</h5>
                    <p class="small opacity-75 mb-3">Setiap surat dibubuhi Tanda Tangan & Stempel Resmi Digital Wali Nagari Koto Malintang.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-qr-code me-1"></i> Validasi Digital</span>
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2"><i class="bi bi-printer me-1"></i> Cetak Mandiri</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container mb-5">
    <!-- ALUR 5 LANGKAH PENGURUSAN SURAT -->
    <section class="mb-5 pb-2">
        <div class="text-center mb-4">
            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-2">Panduan Warga</span>
            <h3 class="fw-bold text-dark mb-1">Alur Mudah Pengurusan Surat Nagari</h3>
            <p class="text-muted small">5 langkah praktis dari konsultasi hingga surat resmi terbit dan siap digunakan</p>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-lg">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <h6 class="fw-bold text-dark mb-1">Pilih Jenis Surat</h6>
                    <p class="small text-muted mb-0">Cari jenis surat yang sesuai keperluan dan siapkan dokumen persyaratan.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="step-card">
                    <div class="step-num">2</div>
                    <h6 class="fw-bold text-dark mb-1">Konsultasi AI</h6>
                    <p class="small text-muted mb-0">Tanyakan syarat dan jam layanan secara interaktif ke Asisten eToma AI.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="step-card">
                    <div class="step-num">3</div>
                    <h6 class="fw-bold text-dark mb-1">Ajukan Permohonan</h6>
                    <p class="small text-muted mb-0">Login ke Panel Warga, isi data lengkap, dan unggah foto/scan berkas.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="step-card">
                    <div class="step-num">4</div>
                    <h6 class="fw-bold text-dark mb-1">Verifikasi & TTD</h6>
                    <p class="small text-muted mb-0">Admin nagari memverifikasi berkas dan Wali Nagari menandatangani digital.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="step-card">
                    <div class="step-num">5</div>
                    <h6 class="fw-bold text-dark mb-1">Cetak / Unduh</h6>
                    <p class="small text-muted mb-0">Surat terbit resmi berstempel dan siap diunduh PDF atau dicetak langsung.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION ASISTEN AI & KONSULTASI INTERAKTIF -->
    <section id="chatbotSection" class="mb-5 pb-2">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-2">
                    <i class="bi bi-robot me-1"></i> eToma AI Assistant
                </span>
                <h3 class="fw-bold text-dark mb-3">Konsultasi Layanan Surat 24 Jam dengan eToma AI</h3>
                <p class="text-muted mb-4" style="line-height: 1.6;">
                    Bingung memilih jenis surat atau membutuhkan informasi persyaratan berkas? Tanyakan langsung kepada Asisten Cerdas Nagari Koto Malintang. Ketik pertanyaan Anda dalam bahasa sehari-hari.
                </p>

                <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-lightbulb-fill text-warning me-1"></i>Pertanyaan Populer (Klik untuk Tanya):</h6>
                <div class="d-flex flex-wrap gap-1 mb-4">
                    <span class="quick-chip" onclick="quickAsk('Apa saja syarat membuat Surat Keterangan Usaha (SKU)?')">💼 Syarat SKU</span>
                    <span class="quick-chip" onclick="quickAsk('Bagaimana syarat mengurus SKTM untuk beasiswa?')">🤝 Syarat SKTM</span>
                    <span class="quick-chip" onclick="quickAsk('Apa syarat membuat surat pengantar nikah ke KUA?')">💍 Syarat Nikah NA</span>
                    <span class="quick-chip" onclick="quickAsk('Syarat Surat Keterangan Domisili apa saja?')">🏠 Syarat Domisili</span>
                    <span class="quick-chip" onclick="quickAsk('Kapan jam operasional kantor Wali Nagari Koto Malintang?')">⏰ Jam Layanan Kantor</span>
                    <span class="quick-chip" onclick="quickAsk('Berapa lama proses pembuatan surat selesai?')">⚡ Lama Proses Surat</span>
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-info-circle-fill text-success fs-3"></i>
                        <small class="text-muted">
                            Asisten AI beroperasi secara realtime dan dilengkapi basis pengetahuan resmi dari Pemerintah Nagari Koto Malintang.
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="ai-box-card">
                    <div class="ai-chat-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-20 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-robot text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-white">eToma AI Assistant</h6>
                                <small class="text-white-50" style="font-size: 0.75rem;"><i class="bi bi-circle-fill text-success me-1"></i>Online - Layanan Nagari</small>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 font-monospace" style="font-size: 0.75rem;">AI Powered</span>
                    </div>

                    <div class="ai-chat-body" id="chatBody">
                        <div class="chat-bubble ai-msg">
                            👋 <strong>Salamaik datang di Portal Layanan Nagari Koto Malintang!</strong><br />
                            Saya adalah <strong>eToma AI</strong>, asisten virtual resmi nagari. Anda dapat menanyakan persyaratan surat, jam pelayanan, atau memilih jenis surat yang cocok untuk keperluan Anda. Ada yang bisa saya bantu hari ini?
                        </div>
                    </div>

                    <div class="p-3 border-top bg-white">
                        <form id="chatForm" onsubmit="handleChatSubmit(event)" class="d-flex gap-2">
                            <input type="text" id="chatInput" class="form-control rounded-pill px-3" placeholder="Ketik pertanyaan Anda seputar surat nagari di sini..." autocomplete="off" required />
                            <button type="submit" id="btnSendChat" class="btn btn-success rounded-pill px-4 fw-bold d-flex align-items-center gap-1 shadow-sm">
                                <span>Kirim</span>
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 7 KATALOG JENIS SURAT NAGARI -->
    <section id="katalogSurat" class="mb-5 pb-2">
        <div class="text-center mb-5">
            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-2">Katalog Resmi</span>
            <h3 class="fw-bold text-dark mb-1">7 Jenis Layanan Surat Keterangan Resmi</h3>
            <p class="text-muted small">Pemerintah Nagari Koto Malintang melayani 7 jenis surat keterangan dan pengantar resmi secara digital</p>
            <div class="mx-auto" style="width: 60px; height: 3px; background: #198754; border-radius: 2px;"></div>
        </div>

        <div class="row g-4">
            @php
                $daftar7Surat = [
                    'suket_usaha' => [
                        'nama' => 'Surat Keterangan Usaha',
                        'singkatan' => 'SKU',
                        'icon' => 'bi-shop',
                        'deskripsi' => 'Menerangkan kepemilikan dan legalitas operasional tempat/kegiatan usaha mikro, kecil, atau menengah di wilayah Nagari Koto Malintang.',
                        'kegunaan' => 'Syarat pinjaman modal usaha/KUR Bank (BRI, Bank Nagari, dll), perizinan usaha, dan pembukaan rekening usaha.',
                        'syarat' => [
                            'KTP Pemohon yang masih berlaku (Wajib)',
                            'Kartu Keluarga (KK) Pemohon (Wajib)',
                            'Foto Tempat / Aktivitas Usaha (Wajib)',
                            'Surat Pengantar dari Kepala Jorong (Opsional)',
                        ],
                    ],
                    'suket_tidak_mampu' => [
                        'nama' => 'Surat Keterangan Tidak Mampu',
                        'singkatan' => 'SKTM',
                        'icon' => 'bi-people-fill',
                        'deskripsi' => 'Menerangkan kondisi sosial ekonomi warga yang tergolong dalam keluarga kurang mampu / pra-sejahtera untuk keperluan bantuan sosial.',
                        'kegunaan' => 'Pengajuan beasiswa sekolah/kuliah (KIP-K), keringanan biaya pengobatan di RS/BPJS, dan permohonan bantuan sosial.',
                        'syarat' => [
                            'KTP Pemohon / Kepala Keluarga (Wajib)',
                            'Kartu Keluarga (KK) Pemohon (Wajib)',
                            'Foto Kondisi Rumah / Tempat Tinggal (Wajib)',
                            'Surat Keterangan Kurang Mampu dari Kepala Jorong (Wajib)',
                        ],
                    ],
                    'suket_domisili' => [
                        'nama' => 'Surat Keterangan Domisili',
                        'singkatan' => 'SKD',
                        'icon' => 'bi-geo-alt-fill',
                        'deskripsi' => 'Menerangkan bahwa pemohon adalah benar berdomisili dan bertempat tinggal sah di wilayah jorong Nagari Koto Malintang.',
                        'kegunaan' => 'Kelengkapan administrasi tempat tinggal, pembuatan rekening bank, pendaftaran sekolah, dan administrasi kependudukan.',
                        'syarat' => [
                            'KTP Pemohon (Wajib)',
                            'Kartu Keluarga (KK) Pemohon (Wajib)',
                            'Surat Pengantar Domisili dari Kepala Jorong (Wajib)',
                        ],
                    ],
                    'suket_meninggal' => [
                        'nama' => 'Surat Keterangan Meninggal',
                        'singkatan' => 'SKM',
                        'icon' => 'bi-file-earmark-medical-fill',
                        'deskripsi' => 'Menerangkan peristiwa kematian / wafat warga Nagari Koto Malintang secara sah untuk pencatatan kependudukan resmi.',
                        'kegunaan' => 'Penerbitan akta kematian Disdukcapil, pengurusan klaim asuransi/taspen, dan penetapan hak waris keluarga.',
                        'syarat' => [
                            'KTP Pelapor / Ahli Waris (Wajib)',
                            'Kartu Keluarga (KK) Almarhum / Almarhumah (Wajib)',
                            'Surat Keterangan Kematian dari RS / Bidan / Wali Jorong (Wajib)',
                        ],
                    ],
                    'suket_belum_menikah' => [
                        'nama' => 'Surat Keterangan Belum Menikah',
                        'singkatan' => 'SKBM',
                        'icon' => 'bi-person-check-fill',
                        'deskripsi' => 'Menerangkan bahwa pemohon belum pernah menikah (berstatus lajang / belum kawin) hingga saat surat ini diterbitkan.',
                        'kegunaan' => 'Syarat kelengkapan berkas melamar pekerjaan, pendaftaran CPNS, seleksi TNI/Polri, dan beasiswa.',
                        'syarat' => [
                            'KTP Pemohon yang masih berlaku (Wajib)',
                            'Kartu Keluarga (KK) Pemohon (Wajib)',
                            'Surat Pernyataan Belum Menikah / Pengantar Jorong (Opsional)',
                        ],
                    ],
                    'surat_nikah_na' => [
                        'nama' => 'Surat Pengantar Nikah (Model NA)',
                        'singkatan' => 'Model NA',
                        'icon' => 'bi-gem',
                        'deskripsi' => 'Surat pengantar rekomendasi permohonan kehendak nikah (N-1 s.d N-4) untuk didaftarkan dan diproses ke KUA Kecamatan Tanjung Raya.',
                        'kegunaan' => 'Pendaftaran kehendak nikah resmi ke Kantor Urusan Agama (KUA).',
                        'syarat' => [
                            'KTP Pemohon & Calon Pasangan (Wajib)',
                            'Kartu Keluarga (KK) Pemohon (Wajib)',
                            'Pas Foto Formal Pemohon & Calon Pasangan (Wajib)',
                            'Surat Pengantar Kehendak Nikah dari Kepala Jorong (Wajib)',
                        ],
                    ],
                    'sket_penghasilan' => [
                        'nama' => 'Surat Keterangan Penghasilan',
                        'singkatan' => 'SKP',
                        'icon' => 'bi-cash-coin',
                        'deskripsi' => 'Menerangkan rincian estimasi rata-rata penghasilan/pendapatan orang tua atau pemohon per bulan.',
                        'kegunaan' => 'Kelengkapan pengajuan beasiswa anak, pendaftaran perguruan tinggi (SNBP/SNBT), dan pengajuan kredit pembiayaan.',
                        'syarat' => [
                            'KTP Pemohon / Orang Tua (Wajib)',
                            'Kartu Keluarga (KK) Pemohon (Wajib)',
                            'Slip Gaji atau Surat Pernyataan Estimasi Penghasilan (Wajib)',
                        ],
                    ],
                ];
            @endphp

            @foreach ($daftar7Surat as $key => $surat)
                <div class="col-md-6 col-lg-4">
                    <div class="catalog-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="catalog-icon-wrapper">
                                <i class="bi {{ $surat['icon'] }}"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                {{ $surat['singkatan'] }}
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark mb-2">{{ $surat['nama'] }}</h5>
                        <p class="small text-muted mb-3" style="line-height: 1.5; min-height: 48px;">
                            {{ $surat['deskripsi'] }}
                        </p>

                        <div class="bg-light p-3 rounded-3 mb-3 border flex-grow-1">
                            <div class="small fw-bold text-success mb-2">
                                <i class="bi bi-file-earmark-check me-1"></i> Persyaratan Berkas:
                            </div>
                            <ul class="persyaratan-list mb-0">
                                @foreach ($surat['syarat'] as $item)
                                    <li><i class="bi bi-check-circle-fill"></i><span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="d-flex gap-2 pt-2">
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill flex-grow-1 fw-semibold py-2" onclick="previewSuratModal('{{ $key }}')">
                                <i class="bi bi-eye-fill me-1"></i> Contoh Format
                            </button>
                            @auth
                                <a href="{{ route('warga.surat.create', ['jenis' => $key]) }}" class="btn btn-sm btn-success rounded-pill px-3 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-pencil-square me-1"></i> Ajukan
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-success rounded-pill px-3 py-2 fw-bold shadow-sm" title="Login untuk mengajukan surat">
                                    <i class="bi bi-pencil-square me-1"></i> Ajukan
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- CTA BANNER UNTUK WARGA -->
    <section class="mb-5">
        <div class="p-5 rounded-4 text-white position-relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #0f5132 0%, #198754 100%);">
            <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Portal Warga Nagari</span>
                    <h3 class="fw-bold mb-2">Siap Mengajukan Surat Permohonan Secara Online?</h3>
                    <p class="lead opacity-90 mb-0" style="font-size: 1.05rem;">
                        Warga yang telah memiliki akun dapat langsung mengisi formulir dan mengunggah berkas dari kenyamanan rumah Anda.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    @auth
                        <a href="{{ route('warga.surat.create') }}" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold text-dark shadow me-2 mb-2">
                            <i class="bi bi-pencil-square me-1"></i> Form Pengajuan Surat
                        </a>
                        <a href="{{ route('warga.dashboard') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 shadow mb-2">
                            <i class="bi bi-house-door me-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold text-dark shadow me-2 mb-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Panel Warga
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 shadow mb-2">
                            <i class="bi bi-person-plus me-1"></i> Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ ACCORDION -->
    <section class="mb-5 pb-3">
        <div class="text-center mb-4">
            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-2">Bantuan & Tanya Jawab</span>
            <h3 class="fw-bold text-dark mb-1">Pertanyaan yang Sering Diajukan (FAQ)</h3>
            <p class="text-muted small">Informasi seputar biaya, waktu proses, dan pengambilan surat di Nagari Koto Malintang</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion shadow-sm rounded-4 overflow-hidden border-0" id="accordionFaq">
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <i class="bi bi-question-circle-fill text-success me-2"></i> Apakah pengurusan surat di Nagari Koto Malintang dikenakan biaya?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small lh-lg">
                                <strong>100% GRATIS (Bebas Biaya).</strong> Seluruh pelayanan administrasi persuratan bagi warga Nagari Koto Malintang tidak dipungut biaya apapun sesuai ketentuan pelayanan publik nagari.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="bi bi-question-circle-fill text-success me-2"></i> Berapa lama proses verifikasi hingga surat terbit?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small lh-lg">
                                Proses verifikasi oleh Admin Nagari dan penandatanganan digital oleh Wali Nagari umumnya memakan waktu <strong>1 hari kerja</strong> setelah berkas persyaratan dinyatakan lengkap dan valid.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="bi bi-question-circle-fill text-success me-2"></i> Bagaimana cara mendapatkan dokumen surat yang sudah selesai?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small lh-lg">
                                Surat yang telah disetujui akan dibubuhi <strong>Tanda Tangan dan Stempel Resmi Digital</strong>. Anda dapat mengunduh format PDF atau mencetaknya langsung melalui menu <strong>Riwayat Surat</strong> di Panel Warga tanpa harus datang ke kantor nagari.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <i class="bi bi-question-circle-fill text-success me-2"></i> Apa yang harus dilakukan jika permohonan surat ditolak atau perlu perbaikan?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small lh-lg">
                                Jika berkas kurang lengkap atau terdapat data yang perlu diperbaiki, Admin akan memberikan catatan penolakan/revisi pada status permohonan Anda. Anda dapat memeriksa catatan tersebut di Panel Warga dan mengajukan permohonan ulang dengan berkas yang sudah dilengkapi.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL CONTOH FORMAT SURAT RESMI -->
<div class="modal fade" id="modalPreviewFormatSurat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-success text-white p-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="bi bi-file-earmark-text-fill me-2"></i>Contoh Format Naskah Dinas Surat Resmi</h5>
                    <small class="opacity-75">Pemerintah Nagari Koto Malintang, Kec. Tanjung Raya, Kab. Agam</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="official-letter-paper" id="printableLetter">
                    <!-- KOP SURAT RESMI -->
                    <div class="d-flex align-items-center justify-content-center gap-3 text-center pb-2">
                        <img src="{{ asset('image/logo agam.png') }}" alt="Logo Agam" style="width: 75px; height: 75px; object-fit: contain;" />
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size: 1rem; letter-spacing: 0.5px;">PEMERINTAH KABUPATEN AGAM</h6>
                            <h6 class="fw-bold mb-0" style="font-size: 1rem; letter-spacing: 0.5px;">KECAMATAN TANJUNG RAYA</h6>
                            <h5 class="fw-bold mb-0 text-success" style="font-size: 1.25rem; letter-spacing: 0.5px;">PEMERINTAH NAGARI KOTO MALINTANG</h5>
                            <small style="font-size: 0.85rem;">Alamat: Jl. Raya Maninjau - Lubuk Basung, Koto Malintang, Kode Pos: 26454</small>
                        </div>
                    </div>

                    <div class="kop-garis-ganda"></div>

                    <!-- JUDUL & NOMOR SURAT -->
                    <div class="text-center mb-4">
                        <h6 class="fw-bold text-decoration-underline mb-0" id="pvJudulSurat" style="font-size: 1.15rem;">SURAT KETERANGAN USAHA</h6>
                        <small id="pvNomorSurat" style="font-size: 0.95rem;">Nomor: 470 / 042 / WN-KM / VIII / {{ date('Y') }}</small>
                    </div>

                    <!-- ISI SURAT -->
                    <div class="lh-lg mb-4" style="font-size: 0.95rem; text-align: justify;">
                        <p class="mb-3">
                            Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, Provinsi Sumatera Barat, dengan ini menerangkan bahwa:
                        </p>

                        <table style="width: 100%; margin-bottom: 16px; font-size: 0.95rem;">
                            <tr><td style="width: 220px; vertical-align: top;">Nama Lengkap</td><td>: <strong>BUDI SANTOSO</strong></td></tr>
                            <tr><td style="vertical-align: top;">NIK</td><td>: 1306010101900001</td></tr>
                            <tr><td style="vertical-align: top;">Tempat / Tgl Lahir</td><td>: Koto Malintang, 01 Januari 1995</td></tr>
                            <tr><td style="vertical-align: top;">Jenis Kelamin</td><td>: Laki-laki</td></tr>
                            <tr><td style="vertical-align: top;">Agama</td><td>: Islam</td></tr>
                            <tr><td style="vertical-align: top;">Pekerjaan</td><td>: Wiraswasta / Petani</td></tr>
                            <tr><td style="vertical-align: top;">Alamat Tempat Tinggal</td><td>: Jorong Muko-Muko, Nagari Koto Malintang</td></tr>
                            <tr id="pvExtraRow1"><td style="vertical-align: top;" id="pvExtraLabel1">Nama Usaha</td><td>: <strong id="pvExtraVal1">Warung Kopi & Aneka Minuman</strong></td></tr>
                            <tr id="pvExtraRow2"><td style="vertical-align: top;" id="pvExtraLabel2">Bidang Usaha</td><td>: <span id="pvExtraVal2">Kuliner dan Minuman Tradisional</span></td></tr>
                        </table>

                        <p id="pvParagrafInti" class="mb-3">
                            Adalah benar yang bersangkutan merupakan penduduk yang bertempat tinggal di Nagari Koto Malintang dan menjalankan kegiatan usaha sebagaimana tersebut di atas. Surat keterangan ini diberikan atas permohonan yang bersangkutan untuk keperluan <strong>Kelengkapan Pengajuan Pinjaman Modal Usaha (KUR Bank)</strong>.
                        </p>

                        <p class="mb-0">
                            Demikian surat keterangan ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.
                        </p>
                    </div>

                    <!-- TANDA TANGAN & STEMPEL RESMI -->
                    <div class="row pt-2">
                        <div class="col-7"></div>
                        <div class="col-5 text-center position-relative">
                            <div>Koto Malintang, {{ date('d F Y') }}</div>
                            <div class="fw-bold mb-1">WALI NAGARI KOTO MALINTANG</div>
                            <div class="signature-box my-1">
                                <img src="{{ asset('image/stempel_resmi.svg') }}" alt="Stempel Resmi" class="official-seal-img" />
                                <div class="digital-sign-text">Nazaruddin</div>
                            </div>
                            <div class="fw-bold text-decoration-underline">NAZARUDDIN, S.Pd.I</div>
                            <small class="text-muted d-block">Wali Nagari</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success rounded-pill px-3" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak Dokumen
                    </button>
                    @auth
                        <a href="{{ route('warga.surat.create') }}" id="btnModalAjukan" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-pencil-square me-1"></i> Ajukan Surat Ini
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login & Ajukan
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // DATA SPESIFIK 7 FORMAT SURAT RESMI
    const templateSuratResmi = {
        'suket_usaha': {
            judul: 'SURAT KETERANGAN USAHA',
            nomor: 'Nomor: 503 / 042 / WN-KM / ' + getRomanMonth() + ' / {{ date('Y') }}',
            extra1Label: 'Nama Usaha',
            extra1Val: 'Warung Kopi & Makanan Ringan "Maninjau Sejahtera"',
            extra2Label: 'Bidang Usaha',
            extra2Val: 'Kuliner & Perdagangan Mikro',
            paragrafInti: 'Adalah benar yang bersangkutan merupakan penduduk yang bertempat tinggal di Nagari Koto Malintang dan menurut pencatatan serta sepengetahuan kami yang bersangkutan memiliki dan menjalankan kegiatan usaha tersebut di atas di wilayah Nagari Koto Malintang. Surat keterangan ini diberikan atas permohonan yang bersangkutan untuk keperluan: <strong>Kelengkapan Pengajuan Tambahan Modal Usaha (KUR Bank)</strong>.'
        },
        'suket_tidak_mampu': {
            judul: 'SURAT KETERANGAN TIDAK MAMPU (SKTM)',
            nomor: 'Nomor: 401 / 018 / WN-KM / ' + getRomanMonth() + ' / {{ date('Y') }}',
            extra1Label: 'Penghasilan Keluarga',
            extra1Val: 'Rp 800.000 / Bulan',
            extra2Label: 'Kondisi Ekonomi',
            extra2Val: 'Keluarga Kurang Mampu / Pra-Sejahtera',
            paragrafInti: 'Adalah benar yang bersangkutan merupakan warga masyarakat Nagari Koto Malintang yang berdasarkan pengamatan dan data administrasi kami tergolong dalam keluarga Kurang Mampu / Pra-Sejahtera. Surat keterangan ini diterbitkan atas permohonan yang bersangkutan untuk keperluan: <strong>Permohonan Keringanan Biaya Pendidikan / Pengajuan Beasiswa KIP-Kuliah</strong>.'
        },
        'suket_domisili': {
            judul: 'SURAT KETERANGAN DOMISILI',
            nomor: 'Nomor: 470 / 088 / WN-KM / ' + getRomanMonth() + ' / {{ date('Y') }}',
            extra1Label: 'Lama Menetap',
            extra1Val: 'Tinggal Sejak Lahir (Warga Asli)',
            extra2Label: 'Status Tempat Tinggal',
            extra2Val: 'Rumah Milik Sendiri / Keluarga',
            paragrafInti: 'Adalah benar yang bersangkutan merupakan warga masyarakat yang berdomisili dan bertempat tinggal sah di alamat tersebut di atas dalam wilayah hukum Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam. Surat keterangan ini diberikan untuk keperluan: <strong>Kelengkapan Administrasi Domisili dan Pembukaan Rekening Bank</strong>.'
        },
        'suket_meninggal': {
            judul: 'SURAT KETERANGAN KEMATIAN / MENINGGAL DUNIA',
            nomor: 'Nomor: 474.3 / 024 / WN-KM / ' + getRomanMonth() + ' / {{ date('Y') }}',
            extra1Label: 'Nama Almarhum/ah',
            extra1Val: 'SITI RAHMA (Almarhumah)',
            extra2Label: 'Wafat Pada',
            extra2Val: '15 Agustus {{ date('Y') }} di Rumah Duka Jorong Muko-Muko',
            paragrafInti: 'Dengan ini menerangkan dengan sebenarnya bahwa yang bersangkutan adalah benar ahli waris/keluarga dari Almarhumah tersebut di atas yang telah meninggal dunia karena sakit/faktor usia. Surat keterangan ini diberikan untuk keperluan: <strong>Pengurusan Akta Kematian Disdukcapil dan Klaim Asuransi/Taspen</strong>.'
        },
        'suket_belum_menikah': {
            judul: 'SURAT KETERANGAN BELUM PERNAH MENIKAH',
            nomor: 'Nomor: 474.2 / 035 / WN-KM / ' + getRomanMonth() + ' / {{ date('Y') }}',
            extra1Label: 'Status Perkawinan',
            extra1Val: 'Belum Kawin / Belum Pernah Menikah (Lajang)',
            extra2Label: 'Keterangan',
            extra2Val: 'Tercatat dalam Buku Induk Kependudukan Nagari',
            paragrafInti: 'Adalah benar yang bersangkutan merupakan penduduk yang berdomisili di Nagari Koto Malintang dan hingga surat keterangan ini diterbitkan, yang bersangkutan tercatat berstatus <strong>BELUM PERNAH MENIKAH (LAJANG)</strong>. Surat keterangan ini diberikan untuk keperluan: <strong>Persyaratan Pendaftaran Seleksi Calon Pegawai / Kedinasan</strong>.'
        },
        'surat_nikah_na': {
            judul: 'SURAT PENGANTAR NIKAH (MODEL NA)',
            nomor: 'Nomor: 474.1 / 012 / WN-KM / ' + getRomanMonth() + ' / {{ date('Y') }}',
            extra1Label: 'Nama Calon Pasangan',
            extra1Val: 'FATIMAH AZ-ZAHRA',
            extra2Label: 'Bin / Binti Pasangan',
            extra2Val: 'Binti H. Ahmad Dahlan',
            paragrafInti: 'Adalah benar yang bersangkutan merupakan warga Nagari Koto Malintang yang telah memenuhi persyaratan administrasi pernikahan dan bermaksud akan melangsungkan perkawinan dengan calon pasangan tersebut di atas. Surat pengantar ini diterbitkan untuk keperluan: <strong>Pendaftaran Kehendak Nikah di Kantor Urusan Agama (KUA) Kecamatan Tanjung Raya</strong>.'
        },
        'sket_penghasilan': {
            judul: 'SURAT KETERANGAN PENGHASILAN',
            nomor: 'Nomor: 470 / 063 / WN-KM / ' + getRomanMonth() + ' / {{ date('Y') }}',
            extra1Label: 'Rata-rata Penghasilan',
            extra1Val: 'Rp 1.800.000 / Bulan',
            extra2Label: 'Sumber Penghasilan',
            extra2Val: 'Hasil Pertanian & Perkebunan Nagari',
            paragrafInti: 'Adalah benar yang bersangkutan merupakan warga Nagari Koto Malintang dengan estimasi rata-rata penghasilan per bulan sebagaimana tercantum di atas dengan jumlah tanggungan keluarga sebanyak 3 (tiga) orang. Surat keterangan ini diterbitkan untuk keperluan: <strong>Kelengkapan Syarat Pengajuan Beasiswa Pendidikan Anak</strong>.'
        }
    };

    function getRomanMonth() {
        const months = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return months[new Date().getMonth()];
    }

    // FUNGSI TAMPILKAN MODAL PREVIEW CONTOH FORMAT
    function previewSuratModal(key) {
        const conf = templateSuratResmi[key] || templateSuratResmi['suket_usaha'];

        document.getElementById('pvJudulSurat').innerText = conf.judul;
        document.getElementById('pvNomorSurat').innerText = conf.nomor;
        document.getElementById('pvExtraLabel1').innerText = conf.extra1Label;
        document.getElementById('pvExtraVal1').innerText = conf.extra1Val;
        document.getElementById('pvExtraLabel2').innerText = conf.extra2Label;
        document.getElementById('pvExtraVal2').innerText = conf.extra2Val;
        document.getElementById('pvParagrafInti').innerHTML = conf.paragrafInti;

        const btnAjukan = document.getElementById('btnModalAjukan');
        if (btnAjukan) {
            btnAjukan.href = "{{ url('/warga/surat/create') }}?jenis=" + key;
        }

        const modal = new bootstrap.Modal(document.getElementById('modalPreviewFormatSurat'));
        modal.show();
    }

    // FUNGSI CHATBOT AI INTERAKTIF
    function quickAsk(text) {
        document.getElementById('chatInput').value = text;
        handleChatSubmit(new Event('submit'));
    }

    async function handleChatSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (!text) return;

        const chatBody = document.getElementById('chatBody');
        const btnSend = document.getElementById('btnSendChat');

        // Append User Message Bubble
        const userDiv = document.createElement('div');
        userDiv.className = 'chat-bubble user-msg';
        userDiv.innerText = text;
        chatBody.appendChild(userDiv);
        input.value = '';
        chatBody.scrollTop = chatBody.scrollHeight;

        // Append Loading Indicator Bubble
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'chat-bubble ai-msg';
        loadingDiv.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Sedang memproses jawaban...';
        chatBody.appendChild(loadingDiv);
        chatBody.scrollTop = chatBody.scrollHeight;

        btnSend.disabled = true;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const response = await fetch("{{ url('/api/ai/surat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: text })
            });

            if (response.ok) {
                const result = await response.json();
                let answerText = result.message || (result.data ? result.data.response : '') || result.reply || '';
                if (!answerText) {
                    answerText = getLocalAiKnowledge(text);
                }
                loadingDiv.innerHTML = formatMarkdownToHtml(answerText);
            } else {
                loadingDiv.innerHTML = formatMarkdownToHtml(getLocalAiKnowledge(text));
            }
        } catch (err) {
            loadingDiv.innerHTML = formatMarkdownToHtml(getLocalAiKnowledge(text));
        } finally {
            btnSend.disabled = false;
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    function formatMarkdownToHtml(md) {
        if (!md) return '';
        let html = md
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br />');
        return html;
    }

    function getLocalAiKnowledge(text) {
        const lower = text.toLowerCase();
        if (lower.includes('usaha') || lower.includes('sku') || lower.includes('dagang') || lower.includes('modal') || lower.includes('kur') || lower.includes('warung') || lower.includes('toko')) {
            return 'Untuk keperluan legalitas usaha atau pengajuan kredit modal usaha/KUR di Bank, Anda dapat mengajukan **Surat Keterangan Usaha (SKU)**.\n\n📋 **Syarat Berkas:**\n1. KTP Pemohon\n2. Kartu Keluarga (KK)\n3. Foto tempat / aktivitas usaha\n\nSilakan klik tombol **Ajukan Surat** pada bagian katalog surat di atas.';
        }
        if (lower.includes('tidak mampu') || lower.includes('sktm') || lower.includes('miskin') || lower.includes('beasiswa') || lower.includes('kip') || lower.includes('bpjs') || lower.includes('bantuan')) {
            return 'Untuk pengajuan beasiswa (KIP-Kuliah/sekolah), keringanan biaya RS/BPJS, atau bantuan sosial, surat yang tepat adalah **Surat Keterangan Tidak Mampu (SKTM)**.\n\n📋 **Syarat Berkas:**\n1. KTP Pemohon / Kepala Keluarga\n2. Kartu Keluarga (KK)\n3. Foto kondisi rumah tinggal\n4. Surat Pengantar Kurang Mampu dari Kepala Jorong.';
        }
        if (lower.includes('nikah') || lower.includes('kawin') || lower.includes('kua') || lower.includes('pengantin') || lower.includes('na') || lower.includes('pasangan')) {
            return 'Bagi calon pengantin yang akan melangsungkan akad dan mendaftar ke KUA, silakan ajukan **Surat Pengantar Nikah (Model NA)**.\n\n📋 **Syarat Berkas:**\n1. KTP Pemohon & Calon Pasangan\n2. Kartu Keluarga (KK)\n3. Pas foto formal calon pengantin\n4. Surat Pengantar Nikah dari Kepala Jorong.';
        }
        if (lower.includes('mati') || lower.includes('meninggal') || lower.includes('wafat') || lower.includes('kematian') || lower.includes('skm') || lower.includes('waris')) {
            return 'Untuk pencatatan kematian resmi di Disdukcapil atau klaim asuransi ahli waris, silakan ajukan **Surat Keterangan Meninggal (SKM)**.\n\n📋 **Syarat Berkas:**\n1. KTP Pelapor / Ahli Waris\n2. Kartu Keluarga (KK) Almarhum\n3. Surat Kematian dari RS / Bidan / Wali Jorong.';
        }
        if (lower.includes('belum nikah') || lower.includes('lajang') || lower.includes('single') || lower.includes('bujang') || lower.includes('gadis') || lower.includes('skbm')) {
            return 'Untuk melamar pekerjaan, pendaftaran seleksi TNI/Polri/CPNS, atau persyaratan kedinasan, silakan ajukan **Surat Keterangan Belum Menikah (SKBM)**.\n\n📋 **Syarat Berkas:** KTP dan Kartu Keluarga (KK) yang masih berlaku.';
        }
        if (lower.includes('gaji') || lower.includes('penghasilan') || lower.includes('pendapatan') || lower.includes('skp')) {
            return 'Untuk rincian estimasi rata-rata pendapatan orang tua atau pribadi (syarat beasiswa/kredit), silakan ajukan **Surat Keterangan Penghasilan (SKP)**.\n\n📋 **Syarat Berkas:** KTP, KK, dan Slip Gaji / Surat Pernyataan Penghasilan.';
        }
        if (lower.includes('domisili') || lower.includes('tinggal') || lower.includes('skd') || lower.includes('alamat')) {
            return 'Sebagai bukti tempat tinggal sah di Nagari Koto Malintang, Anda dapat mengajukan **Surat Keterangan Domisili (SKD)**.\n\n📋 **Syarat Berkas:** KTP, Kartu Keluarga (KK), dan Surat Pengantar Domisili dari Kepala Jorong.';
        }
        if (lower.includes('jam') || lower.includes('buka') || lower.includes('tutup') || lower.includes('operasional') || lower.includes('kantor') || lower.includes('jadwal')) {
            return 'Kantor Wali Nagari Koto Malintang beroperasi pada:\n\n🕒 **Senin - Kamis:** 08.00 - 16.00 WIB\n🕒 **Jumat:** 08.00 - 11.30 WIB\n🚫 **Sabtu & Minggu:** Libur / Tutup\n\n*Catatan: Layanan pengajuan surat online melalui website ini tetap aktif 24 jam sehari!*';
        }
        if (lower.includes('lama') || lower.includes('waktu') || lower.includes('proses') || lower.includes('selesai') || lower.includes('berapa')) {
            return 'Proses verifikasi dan penerbitan surat resmi rata-rata memakan waktu **1 hari kerja** setelah berkas persyaratan dinyatakan lengkap oleh Admin dan disetujui Wali Nagari.';
        }

        return 'Halo dunsanak, Pemerintah Nagari Koto Malintang melayani 7 jenis surat resmi online:\n\n1. **SKU** (Surat Keterangan Usaha)\n2. **SKTM** (Surat Keterangan Tidak Mampu)\n3. **SKD** (Surat Keterangan Domisili)\n4. **SKM** (Surat Keterangan Meninggal)\n5. **SKBM** (Surat Keterangan Belum Menikah)\n6. **Model NA** (Surat Pengantar Nikah KUA)\n7. **SKP** (Surat Keterangan Penghasilan)\n\nSilakan tanyakan persyaratan dari jenis surat yang ingin Anda buat!';
    }
</script>
@endpush