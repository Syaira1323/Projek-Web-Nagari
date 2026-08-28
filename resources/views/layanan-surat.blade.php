@extends('layouts.app')

@section('title', 'Layanan Surat Nagari - 7 Jenis Surat & AI')

@push('styles')
<style>
    body {
        background: #f8faf9;
        padding-top: 75px;
    }

    /* === HERO SECTION === */
    .hero-surat {
        background: linear-gradient(135deg, #0d5c2e 0%, #198754 60%, #20c997 100%);
        position: relative;
        overflow: hidden;
        padding: 60px 0 50px;
        color: white;
        margin-bottom: 40px;
    }

    .hero-surat::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url("{{ asset('image/forest1.jpg') }}") center/cover;
        opacity: 0.12;
    }

    .hero-title {
        font-size: 2.8rem;
        font-weight: 800;
        line-height: 1.2;
    }

    /* === AI CHAT ASSISTANT BOX === */
    .ai-box {
        background: white;
        border-radius: 20px;
        border: 1.5px solid #d1e7dd;
        box-shadow: 0 10px 30px rgba(25, 135, 84, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .ai-header {
        background: linear-gradient(135deg, #198754, #20c997);
        color: white;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ai-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .ai-chat-body {
        padding: 20px;
        min-height: 180px;
        max-height: 280px;
        overflow-y: auto;
        background: #fdfefe;
    }

    .ai-bubble {
        background: #f0faf4;
        border-radius: 4px 16px 16px 16px;
        padding: 12px 16px;
        margin-bottom: 12px;
        max-width: 90%;
        color: #1a2e1a;
        font-size: 0.9rem;
        border-left: 3.5px solid #198754;
    }

    .user-bubble {
        background: #e8f5e9;
        border-radius: 16px 4px 16px 16px;
        padding: 12px 16px;
        margin-bottom: 12px;
        max-width: 85%;
        margin-left: auto;
        color: #0b2e22;
        font-size: 0.9rem;
        border-right: 3.5px solid #20c997;
    }

    .ai-input-area {
        padding: 12px 16px;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 10px;
        background: white;
    }

    .ai-input-area textarea {
        flex: 1;
        border: 1.5px solid #e2f0e8;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.9rem;
        resize: none;
        outline: none;
    }

    .ai-input-area textarea:focus {
        border-color: #198754;
    }

    .ai-send-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #198754, #20c997);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .ai-send-btn:hover {
        transform: scale(1.08);
    }

    /* === 7 SURAT CARDS === */
    .surat-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 16px;
        padding: 16px;
        transition: all 0.25s ease;
        text-align: center;
        cursor: pointer;
        height: 100%;
    }

    .surat-card:hover {
        border-color: #198754;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(25, 135, 84, 0.12);
    }

    .surat-card.selected {
        border-color: #198754;
        background: #f0faf4;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.2);
    }

    .surat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #e8f5e9;
        color: #198754;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin: 0 auto 10px;
    }

    .surat-card.selected .surat-icon {
        background: #198754;
        color: white;
    }

    /* === GENERATOR BOX === */
    .generator-box {
        background: white;
        border-radius: 20px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        padding: 30px;
    }

    .preview-paper {
        background: white;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 24px;
        font-family: 'Times New Roman', Times, serif;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<!-- HERO -->
<section class="hero-surat">
    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                    <i class="bi bi-robot me-1"></i> Pelayanan Administrasi Digital & Asisten AI
                </span>
                <h1 class="hero-title">Layanan Pengajuan Surat Nagari</h1>
                <p class="lead opacity-90 mb-0">
                    Ketik kebutuhan Anda secara bebas, asisten cerdas <strong>eToma AI</strong> akan mengenali 1 dari <strong>7 jenis surat resmi</strong> Nagari Koto Malintang dan menyusun draf otomatis.
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="bg-white bg-opacity-15 p-3 rounded-4 backdrop-blur">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <i class="bi bi-patch-check-fill fs-1 text-warning"></i>
                        <div class="text-start">
                            <h5 class="fw-bold mb-0">7 Jenis Surat</h5>
                            <small class="opacity-75">Resmi & Terstempel Digital</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mb-5">
    <div class="row g-4">
        <!-- KOLOM KIRI: AI ASISTEN & PILIHAN 7 SURAT -->
        <div class="col-lg-5">
            <!-- Asisten AI Chat Box -->
            <div class="ai-box">
                <div class="ai-header">
                    <div class="ai-avatar">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">eToma AI Assistant</h6>
                        <small class="opacity-75">Konsultasi Kebutuhan Surat Anda</small>
                    </div>
                </div>

                <div class="ai-chat-body" id="aiChatBody">
                    <div class="ai-bubble">
                        👋 <strong>Salamaik datang di Nagari Koto Malintang!</strong><br />
                        Ketikkan kebutuhan surat Anda dalam bahasa sehari-hari (contoh: <em>"Saya mau buka usaha warung kopi"</em>, <em>"Butuh surat pengantar nikah"</em>, <em>"Untuk beasiswa anak"</em>). Saya akan memilihkan jenis surat yang tepat!
                    </div>
                </div>

                <div class="ai-input-area">
                    <textarea id="aiInput" rows="2" placeholder="Ceritakan keperluan surat Anda di sini..." onkeypress="handleKeyPress(event)"></textarea>
                    <button type="button" class="ai-send-btn" onclick="askAI()" title="Kirim ke AI">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>

            <!-- 7 JENIS SURAT GRID -->
            <div>
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-grid-fill text-success me-2"></i>Pilih Langsung 7 Jenis Surat:</h5>
                <div class="row g-2">
                    @php
                        $daftarSuratStatic = [
                            'suket_usaha' => ['nama' => 'Surat Keterangan Usaha', 'singkatan' => 'SKU', 'icon' => 'bi-shop'],
                            'suket_meninggal' => ['nama' => 'Surat Keterangan Meninggal', 'singkatan' => 'SKM', 'icon' => 'bi-file-earmark-medical'],
                            'suket_belum_menikah' => ['nama' => 'Surat Keterangan Belum Menikah', 'singkatan' => 'SKBM', 'icon' => 'bi-person-heart'],
                            'surat_nikah_na' => ['nama' => 'Surat Pengantar Nikah NA', 'singkatan' => 'Model NA', 'icon' => 'bi-heart-pulse-fill'],
                            'sket_penghasilan' => ['nama' => 'Surat Keterangan Penghasilan', 'singkatan' => 'SKP', 'icon' => 'bi-cash-coin'],
                            'suket_tidak_mampu' => ['nama' => 'Surat Keterangan Tidak Mampu', 'singkatan' => 'SKTM', 'icon' => 'bi-people'],
                            'suket_domisili' => ['nama' => 'Surat Keterangan Domisili', 'singkatan' => 'SKD', 'icon' => 'bi-geo-alt'],
                        ];
                    @endphp
                    @foreach ($daftarSuratStatic as $key => $surat)
                        <div class="col-6">
                            <div class="surat-card" id="card_{{ $key }}" onclick="selectSurat('{{ $key }}', '{{ $surat['nama'] }}')">
                                <div class="surat-icon"><i class="bi {{ $surat['icon'] }}"></i></div>
                                <h6 class="fw-bold text-dark small mb-1">{{ $surat['singkatan'] }}</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $surat['nama'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: FORM GENERATOR OTOMATIS & PREVIEW -->
        <div class="col-lg-7">
            <div class="generator-box">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <span class="badge bg-success rounded-pill px-3 py-1 mb-1" id="labelSelectedSurat">Surat Keterangan Usaha</span>
                        <h4 class="fw-bold text-dark mb-0">Formulir Pengajuan Surat</h4>
                    </div>
                    <span class="badge bg-warning text-dark"><i class="bi bi-magic me-1"></i> AI Ready</span>
                </div>

                <form id="formSurat" action="{{ route('warga.surat.store') }}" method="POST" onsubmit="handleFormSubmit(event)">
                    @csrf
                    <input type="hidden" name="jenis_surat" id="form_jenis_surat" value="suket_usaha" />

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap Pemohon <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemohon" id="f_nama" class="form-control" value="{{ auth()->check() ? auth()->user()->name : 'Budi Santoso' }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIK Pemohon</label>
                            <input type="text" name="nik_pemohon" id="f_nik" class="form-control" value="{{ auth()->check() && auth()->user()->nik ? auth()->user()->nik : '1306010101900001' }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="tempat_lahir" id="f_tempat_lahir" class="form-control" value="Koto Malintang" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir" id="f_tanggal_lahir" class="form-control" value="1995-01-01" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" id="f_jk" class="form-select" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Agama & Pekerjaan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="agama" id="f_agama" class="form-control" value="Islam" required />
                                <input type="text" name="pekerjaan" id="f_pekerjaan" class="form-control" placeholder="Pekerjaan" value="Wiraswasta" required />
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Alamat Tempat Tinggal / Jorong <span class="text-danger">*</span></label>
                            <input type="text" name="alamat" id="f_alamat" class="form-control" value="{{ auth()->check() && auth()->user()->address ? auth()->user()->address : 'Jorong Muko-Muko, Nagari Koto Malintang' }}" required />
                        </div>
                    </div>

                    <!-- Data Keterangan Surat -->
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <h6 class="fw-bold text-success mb-2"><i class="bi bi-sliders me-1"></i>Data Keterangan Surat:</h6>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Keperluan Pembuatan Surat <span class="text-danger">*</span></label>
                            <textarea name="keperluan" id="f_keperluan" rows="2" class="form-control" placeholder="Contoh: Pengajuan pinjaman KUR / Beasiswa pendidikan" required>Untuk keperluan administrasi pengajuan tambahan modal usaha mikro</textarea>
                        </div>

                        <div class="row g-2" id="extra_fields">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold" id="label_extra1">Nama Usaha</label>
                                <input type="text" name="nama_usaha" id="f_extra1" class="form-control form-control-sm" placeholder="Nama usaha" value="Warung Kopi & Aneka Minuman" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold" id="label_extra2">Bidang Usaha</label>
                                <input type="text" name="bidang_usaha" id="f_extra2" class="form-control form-control-sm" placeholder="Bidang usaha" value="Kuliner dan Minuman Tradisional" />
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2">
                        <button type="button" class="btn btn-outline-success rounded-pill px-3" onclick="showPreviewModal()">
                            <i class="bi bi-eye-fill me-1"></i> Pratinjau Surat Resmi
                        </button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow">
                            <i class="bi bi-send-fill me-1"></i> Ajukan Surat ke Nagari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PREVIEW SURAT RESMI -->
<div class="modal fade" id="modalPreviewSurat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-success text-white p-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-text-fill me-2"></i>Draf Surat Resmi Nagari Koto Malintang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="preview-paper">
                    <!-- KOP SURAT -->
                    <div class="d-flex align-items-center justify-content-center gap-3 border-bottom border-dark border-3 pb-3 mb-3 text-center">
                        <img src="{{ asset('image/logo agam.png') }}" alt="Logo Agam" style="width: 70px; height: 70px; object-fit: contain;" />
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size: 0.95rem;">PEMERINTAH KABUPATEN AGAM</h6>
                            <h6 class="fw-bold mb-0" style="font-size: 0.95rem;">KECAMATAN TANJUNG RAYA</h6>
                            <h5 class="fw-bold mb-0 text-success" style="font-size: 1.1rem;">PEMERINTAH NAGARI KOTO MALINTANG</h5>
                            <small style="font-size: 0.8rem;">Alamat: Jl. Raya Maninjau - Lubuk Basung, Koto Malintang, Kode Pos: 26454</small>
                        </div>
                    </div>

                    <!-- JUDUL SURAT -->
                    <div class="text-center mb-4">
                        <h6 class="fw-bold text-decoration-underline mb-0" id="pv_judul" style="font-size: 1.05rem;">SURAT KETERANGAN USAHA</h6>
                        <small id="pv_nomor">Nomor: 470 / 042 / WN-KM / VIII / 2026</small>
                    </div>

                    <!-- ISI SURAT -->
                    <div id="pv_isi" class="lh-lg mb-4" style="font-size: 0.95rem; text-align: justify;">
                        Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, menerangkan bahwa:
                    </div>

                    <!-- TTD & STEMPEL -->
                    <div class="row pt-3">
                        <div class="col-7"></div>
                        <div class="col-5 text-center position-relative">
                            <div>Koto Malintang, {{ date('d F Y') }}</div>
                            <div class="fw-bold mb-2">WALI NAGARI KOTO MALINTANG</div>
                            <div class="position-relative d-flex align-items-center justify-content-center my-2" style="height: 85px;">
                                <img src="{{ asset('image/stempel_resmi.svg') }}" alt="Stempel Resmi" style="position: absolute; left: 5px; top: -5px; width: 85px; height: 85px; opacity: 0.88; transform: rotate(-8deg);" />
                                <div style="position: absolute; right: 15px; top: 20px; font-family: 'Pacifico', cursive; font-size: 1.35rem; color: #1a365d; transform: rotate(-6deg); font-weight: bold;">
                                    Nazaruddin
                                </div>
                            </div>
                            <div class="fw-bold text-decoration-underline">NAZARUDDIN, S.Pd.I</div>
                            <small>NIP. -</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup Pratinjau</button>
                <button type="button" class="btn btn-success rounded-pill px-4" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const suratTypes = {
        'suket_usaha': {
            nama: 'Surat Keterangan Usaha (SKU)',
            judulResmi: 'SURAT KETERANGAN USAHA',
            keperluanDefault: 'Untuk keperluan pengajuan tambahan modal usaha perbankan / KUR',
            extra1Label: 'Nama Usaha',
            extra2Label: 'Bidang Usaha',
            extra1: 'Kedai Kopi & Makanan Ringan',
            extra2: 'Kuliner & Minuman Tradisional',
        },
        'suket_meninggal': {
            nama: 'Surat Keterangan Meninggal (SKM)',
            judulResmi: 'SURAT KETERANGAN MENINGGAL DUNIA',
            keperluanDefault: 'Untuk pencatatan administrasi kependudukan dan klaim asuransi ahli waris',
            extra1Label: 'Nama Almarhum / Almarhumah',
            extra2Label: 'Penyebab / Lokasi Wafat',
            extra1: 'Siti Rahma (Almarhumah)',
            extra2: 'Sakit Menua / Wafat di Rumah Duka',
        },
        'suket_belum_menikah': {
            nama: 'Surat Keterangan Belum Menikah (SKBM)',
            judulResmi: 'SURAT KETERANGAN BELUM PERNAH MENIKAH',
            keperluanDefault: 'Untuk persyaratan administrasi melamar pekerjaan / pendaftaran instansi',
            extra1Label: 'Status Perkawinan',
            extra2Label: 'Keterangan Tambahan',
            extra1: 'Lajang / Belum Pernah Menikah',
            extra2: 'Warga Asli Nagari Koto Malintang',
        },
        'surat_nikah_na': {
            nama: 'Surat Pengantar Nikah (Model NA)',
            judulResmi: 'SURAT PENGANTAR NIKAH (MODEL N-1 s.d N-4)',
            keperluanDefault: 'Untuk persyaratan pendaftaran kehendak nikah di Kantor Urusan Agama (KUA)',
            extra1Label: 'Nama Calon Pasangan',
            extra2Label: 'Bin / Binti Pasangan',
            extra1: 'Fatimah Az-Zahra',
            extra2: 'Binti H. Ahmad Dahlan',
        },
        'sket_penghasilan': {
            nama: 'Surat Keterangan Penghasilan (SKP)',
            judulResmi: 'SURAT KETERANGAN PENGHASILAN',
            keperluanDefault: 'Untuk persyaratan pengajuan beasiswa pendidikan anak',
            extra1Label: 'Rata-rata Penghasilan (Rp)',
            extra2Label: 'Sumber Penghasilan',
            extra1: 'Rp 1.500.000 / Bulan',
            extra2: 'Hasil Pertanian & Perkebunan',
        },
        'suket_tidak_mampu': {
            nama: 'Surat Keterangan Tidak Mampu (SKTM)',
            judulResmi: 'SURAT KETERANGAN TIDAK MAMPU (SKTM)',
            keperluanDefault: 'Untuk permohonan keringanan biaya pengobatan / bantuan pendidikan',
            extra1Label: 'Penghasilan Keluarga',
            extra2Label: 'Kondisi Ekonomi',
            extra1: 'Rp 800.000 / Bulan',
            extra2: 'Keluarga Pra-Sejahtera',
        },
        'suket_domisili': {
            nama: 'Surat Keterangan Domisili (SKD)',
            judulResmi: 'SURAT KETERANGAN DOMISILI',
            keperluanDefault: 'Untuk kelengkapan administrasi domisili dan pembuatan rekening bank',
            extra1Label: 'Lama Menetap',
            extra2Label: 'Status Tempat Tinggal',
            extra1: 'Tinggal Sejak Lahir di Jorong Muko-Muko',
            extra2: 'Warga Tetap Nagari Koto Malintang',
        }
    };

    function selectSurat(key, label) {
        document.querySelectorAll('.surat-card').forEach(el => el.classList.remove('selected'));
        const card = document.getElementById('card_' + key);
        if (card) card.classList.add('selected');

        const labelElem = document.getElementById('labelSelectedSurat');
        if (labelElem) labelElem.innerText = suratTypes[key]?.nama || label;

        const formJenis = document.getElementById('form_jenis_surat');
        if (formJenis) formJenis.value = key;

        if (suratTypes[key]) {
            const item = suratTypes[key];
            const fKeperluan = document.getElementById('f_keperluan');
            if (fKeperluan) fKeperluan.value = item.keperluanDefault;

            const lExtra1 = document.getElementById('label_extra1');
            const lExtra2 = document.getElementById('label_extra2');
            const fExtra1 = document.getElementById('f_extra1');
            const fExtra2 = document.getElementById('f_extra2');

            if (lExtra1) lExtra1.innerText = item.extra1Label;
            if (lExtra2) lExtra2.innerText = item.extra2Label;
            if (fExtra1) fExtra1.value = item.extra1;
            if (fExtra2) fExtra2.value = item.extra2;
        }
    }

    function handleKeyPress(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            askAI();
        }
    }

    function askAI() {
        const input = document.getElementById('aiInput');
        const text = input.value.trim();
        if (!text) return;

        const chatBody = document.getElementById('aiChatBody');

        const userBubble = document.createElement('div');
        userBubble.className = 'user-bubble';
        userBubble.innerText = text;
        chatBody.appendChild(userBubble);
        input.value = '';
        chatBody.scrollTop = chatBody.scrollHeight;

        const lower = text.toLowerCase();
        let matchedKey = 'suket_domisili';
        let aiResponse = '';

        if (lower.includes('usaha') || lower.includes('jual') || lower.includes('dagang') || lower.includes('toko') || lower.includes('warung') || lower.includes('kur') || lower.includes('modal') || lower.includes('bank')) {
            matchedKey = 'suket_usaha';
            aiResponse = 'Baik Sanak! Berdasarkan keterangan usaha, saya telah memilihkan <strong>Surat Keterangan Usaha (SKU)</strong> dan mengisi draf data otomatis.';
        } else if (lower.includes('mati') || lower.includes('meninggal') || lower.includes('wafat') || lower.includes('kematian') || lower.includes('ahli waris')) {
            matchedKey = 'suket_meninggal';
            aiResponse = 'Innalillahi wa inna ilaihi rajiun. Saya telah memilihkan <strong>Surat Keterangan Meninggal (SKM)</strong> untuk keperluan administrasi.';
        } else if (lower.includes('lajang') || lower.includes('belum nikah') || lower.includes('belum menikah') || lower.includes('single') || lower.includes('belum kawin')) {
            matchedKey = 'suket_belum_menikah';
            aiResponse = 'Siap Sanak! Saya telah memilihkan <strong>Surat Keterangan Belum Menikah (SKBM)</strong> untuk keperluan Anda.';
        } else if (lower.includes('nikah') || lower.includes('kawin') || lower.includes('kua') || lower.includes('pengantin') || lower.includes('calon')) {
            matchedKey = 'surat_nikah_na';
            aiResponse = 'Selamat Sanak! Saya telah memilihkan <strong>Surat Pengantar Nikah (Model NA)</strong> untuk didaftarkan ke KUA.';
        } else if (lower.includes('gaji') || lower.includes('penghasilan') || lower.includes('pendapatan') || lower.includes('slip')) {
            matchedKey = 'sket_penghasilan';
            aiResponse = 'Baik! Saya telah memilihkan <strong>Surat Keterangan Penghasilan (SKP)</strong> untuk menerangkan rata-rata pendapatan Anda.';
        } else if (lower.includes('tidak mampu') || lower.includes('sktm') || lower.includes('miskin') || lower.includes('bantuan') || lower.includes('bpjs') || lower.includes('beasiswa')) {
            matchedKey = 'suket_tidak_mampu';
            aiResponse = 'Siap Sanak! Saya telah memilihkan <strong>Surat Keterangan Tidak Mampu (SKTM)</strong> untuk permohonan beasiswa atau bantuan.';
        } else {
            matchedKey = 'suket_domisili';
            aiResponse = 'Baik Sanak! Saya telah memilihkan <strong>Surat Keterangan Domisili (SKD)</strong> sebagai bukti tinggal sah di Nagari Koto Malintang.';
        }

        setTimeout(() => {
            const aiBubble = document.createElement('div');
            aiBubble.className = 'ai-bubble';
            aiBubble.innerHTML = aiResponse;
            chatBody.appendChild(aiBubble);
            chatBody.scrollTop = chatBody.scrollHeight;

            selectSurat(matchedKey, suratTypes[matchedKey].nama);
        }, 400);
    }

    function showPreviewModal() {
        const jenis = document.getElementById('form_jenis_surat').value;
        const conf = suratTypes[jenis] || suratTypes['suket_usaha'];
        const nama = document.getElementById('f_nama').value;
        const nik = document.getElementById('f_nik').value;
        const tempat = document.getElementById('f_tempat_lahir').value;
        const tgl = document.getElementById('f_tanggal_lahir').value;
        const jk = document.getElementById('f_jk').value;
        const agama = document.getElementById('f_agama').value;
        const pekerjaan = document.getElementById('f_pekerjaan').value;
        const alamat = document.getElementById('f_alamat').value;
        const keperluan = document.getElementById('f_keperluan').value;
        const extra1 = document.getElementById('f_extra1').value;
        const extra2 = document.getElementById('f_extra2').value;

        document.getElementById('pv_judul').innerText = conf.judulResmi;

        const bodyHtml = `
            <p>Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, dengan ini menerangkan bahwa:</p>
            <table style="width: 100%; margin-bottom: 15px;">
                <tr><td style="width: 200px;">Nama Lengkap</td><td>: <strong>${nama}</strong></td></tr>
                <tr><td>NIK</td><td>: ${nik}</td></tr>
                <tr><td>Tempat / Tgl Lahir</td><td>: ${tempat}, ${tgl}</td></tr>
                <tr><td>Jenis Kelamin</td><td>: ${jk}</td></tr>
                <tr><td>Agama</td><td>: ${agama}</td></tr>
                <tr><td>Pekerjaan</td><td>: ${pekerjaan}</td></tr>
                <tr><td>Alamat</td><td>: ${alamat}</td></tr>
                <tr><td>${conf.extra1Label}</td><td>: <strong>${extra1}</strong></td></tr>
                <tr><td>${conf.extra2Label}</td><td>: ${extra2}</td></tr>
            </table>
            <p>Orang tersebut di atas adalah benar penduduk yang berdomisili di Nagari Koto Malintang dan surat keterangan ini diberikan untuk: <strong>${keperluan}</strong>.</p>
            <p>Demikian surat keterangan ini kami perbuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.</p>
        `;

        document.getElementById('pv_isi').innerHTML = bodyHtml;

        const modal = new bootstrap.Modal(document.getElementById('modalPreviewSurat'));
        modal.show();
    }

    function handleFormSubmit(e) {
        @guest
            e.preventDefault();
            alert('Silakan login terlebih dahulu untuk mengajukan permohonan surat resmi.');
            window.location.href = "{{ route('login') }}";
        @endguest
    }

    document.addEventListener('DOMContentLoaded', function() {
        selectSurat('suket_usaha', 'Surat Keterangan Usaha');
    });
</script>
@endpush
