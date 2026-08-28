@extends('layouts.app')

@section('title', 'Persetujuan & Stempel Surat')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .surat-paper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        padding: 50px 60px;
        font-family: 'Times New Roman', Times, serif;
        color: #111;
        line-height: 1.8;
        max-width: 850px;
        margin: 0 auto;
        position: relative;
    }

    .kop-surat {
        text-align: center;
        border-bottom: 3px double #111;
        padding-bottom: 14px;
        margin-bottom: 24px;
    }

    .kop-surat h4 {
        font-size: 1.15rem;
        font-weight: bold;
        margin: 2px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .kop-surat h3 {
        font-size: 1.35rem;
        font-weight: 800;
        margin: 2px 0;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .kop-surat p {
        font-size: 0.88rem;
        margin: 2px 0;
        font-style: italic;
    }

    .judul-surat {
        text-align: center;
        margin: 24px 0 20px;
    }

    .judul-surat h5 {
        font-weight: bold;
        text-decoration: underline;
        text-transform: uppercase;
        font-size: 1.15rem;
        margin-bottom: 2px;
    }

    .isi-surat-text {
        font-size: 1rem;
        text-align: justify;
        white-space: pre-line;
    }

    .ttd-container {
        margin-top: 40px;
        display: flex;
        justify-content: flex-end;
        text-align: center;
    }

    .ttd-box {
        min-width: 250px;
        position: relative;
    }

    .action-panel-wali {
        background: white;
        border-radius: 20px;
        border: 1px solid #e9ecef;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        max-width: 850px;
        margin-left: auto;
        margin-right: auto;
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- PANEL AKSI WALI NAGARI -->
    <div class="action-panel-wali">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-{{ $surat->status_badge_color }} rounded-pill px-3 py-2 fw-semibold fs-6">
                    <i class="bi bi-info-circle me-1"></i> Status: {{ $surat->status_label }}
                </span>
                <div class="small text-muted mt-2">
                    Pemohon: <strong>{{ $surat->nama_pemohon }}</strong> (NIK: {{ $surat->nik_pemohon }}) &bull; Keperluan: {{ $surat->keperluan }}
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @if ($surat->status === 'menunggu_wali_nagari')
                    <!-- FORM APPROVE 1-KLIK STEMPEL OTOMATIS -->
                    <form action="{{ route('wali-nagari.surat.approve', $surat->id) }}" method="POST" onsubmit="return confirm('Bubuhkan stempel digital resmi dan tanda tangani surat ini?');">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 fw-bold shadow">
                            <i class="bi bi-pen-fill me-1"></i> Beri Stempel & TTD Digital Resmi
                        </button>
                    </form>
                @elseif ($surat->status === 'disetujui')
                    <a href="{{ route('warga.surat.print', $surat->id) }}" target="_blank" class="btn btn-success rounded-pill px-4 shadow">
                        <i class="bi bi-printer-fill me-1"></i> Cetak / Unduh Surat Resmi
                    </a>
                @endif

                <a href="{{ route('wali-nagari.surat.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Berkas Persyaratan Terlampir -->
        @if ($surat->berkasPersyaratans->count() > 0)
            <div class="mt-3 pt-3 border-top">
                <span class="fw-bold small text-dark d-block mb-2"><i class="bi bi-paperclip text-primary me-1"></i> Berkas Persyaratan yang Diunggah Warga:</span>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($surat->berkasPersyaratans as $berkas)
                        <a href="{{ route('warga.berkas.download', $berkas->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-dark" style="font-size: 0.82rem;">
                            <i class="bi bi-file-earmark-check text-success me-1"></i> {{ $berkas->label_jenis }} ({{ $berkas->formatted_size }})
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($surat->status === 'menunggu_wali_nagari')
            <!-- Catatan Admin -->
            @if ($surat->catatan_admin)
                <div class="mt-3 p-3 bg-light rounded-3 small border-start border-3 border-info">
                    <strong>Catatan Pemeriksaan Admin Nagari:</strong> {{ $surat->catatan_admin }}
                </div>
            @endif

            <!-- Tombol Tolak jika Diperlukan -->
            <div class="mt-3 pt-3 border-top d-flex justify-content-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#collapseTolakWali">
                    <i class="bi bi-x-circle me-1"></i> Tolak / Kembalikan Surat
                </button>
            </div>

            <div class="collapse mt-2" id="collapseTolakWali">
                <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25">
                    <form action="{{ route('wali-nagari.surat.reject', $surat->id) }}" method="POST">
                        @csrf
                        <label class="form-label small fw-bold text-danger">Alasan Penolakan Wali Nagari:</label>
                        <textarea name="catatan_wali" rows="2" class="form-control form-control-sm mb-2" placeholder="Tuliskan catatan perbaikan atau penolakan..." required></textarea>
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                            Konfirmasi Tolak
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- DOKUMEN SURAT RESMI -->
    <div class="surat-paper">
        <!-- KOP SURAT NAGARI KOTO MALINTANG -->
        <div class="kop-surat d-flex align-items-center justify-content-center gap-4">
            <img src="{{ asset('image/logo agam.png') }}" alt="Logo Agam" style="width: 75px; height: 75px; object-fit: contain;" />
            <div class="text-center">
                <h4>PEMERINTAH KABUPATEN AGAM</h4>
                <h4>KECAMATAN TANJUNG RAYA</h4>
                <h3>PEMERINTAH NAGARI KOTO MALINTANG</h3>
                <p>Alamat: Jl. Raya Maninjau - Lubuk Basung, Koto Malintang, Kode Pos: 26454</p>
            </div>
        </div>

        <!-- JUDUL & NOMOR SURAT -->
        <div class="judul-surat">
            <h5>{{ $surat->nama_jenis_surat }}</h5>
            <div>Nomor: {{ $surat->nomor_surat ?: '470 / ...... / WN-KM / ' . date('m/Y') }}</div>
        </div>

        <!-- ISI SURAT -->
        <div class="isi-surat-text">
            {{ $surat->isi_surat }}
        </div>

        <!-- TANDA TANGAN & STEMPEL RESMI -->
        <div class="ttd-container">
            <div class="ttd-box">
                <div>Koto Malintang, {{ $surat->disetujui_wali_at ? $surat->disetujui_wali_at->translatedFormat('d F Y') : date('d F Y') }}</div>
                <div class="fw-bold">WALI NAGARI KOTO MALINTANG</div>

                <!-- STEMPEL RESMI DIGITAL JIKA SUDAH DISETUJUI -->
                @if ($surat->status === 'disetujui' && $surat->stempel_digital)
                    <div class="my-2 position-relative d-flex align-items-center justify-content-center" style="height: 100px;">
                        @if (!empty($stempelPath) && file_exists(public_path($stempelPath)))
                            <img src="{{ asset($stempelPath) }}" alt="Stempel Resmi" style="position: absolute; left: 10px; top: 0; width: 100px; height: 100px; opacity: 0.9; transform: rotate(-8deg); z-index: 1;" />
                        @else
                            <img src="{{ asset('image/stempel_resmi.svg') }}" alt="Stempel Resmi Nagari" style="position: absolute; left: 15px; top: -5px; width: 95px; height: 95px; opacity: 0.88; transform: rotate(-8deg); z-index: 1;" />
                        @endif

                        @if (!empty($ttdPath) && file_exists(public_path($ttdPath)))
                            <img src="{{ asset($ttdPath) }}" alt="Tanda Tangan" style="position: absolute; right: 20px; top: 10px; max-height: 80px; max-width: 140px; z-index: 2;" />
                        @else
                            <div style="position: absolute; right: 25px; top: 22px; font-family: 'Pacifico', cursive, sans-serif; color: #1a365d; font-size: 1.55rem; transform: rotate(-6deg); font-weight: bold; letter-spacing: 1px; z-index: 2;">
                                {{ explode(',', $namaWali)[0] }}
                            </div>
                        @endif
                    </div>
                @else
                    <div style="height: 95px;" class="d-flex align-items-center justify-content-center text-muted small fst-italic">
                        [Stempel & TTD Digital akan dibubuhkan otomatis di sini saat disetujui]
                    </div>
                @endif

                <div class="fw-bold text-decoration-underline mt-2">{{ $namaWali }}</div>
                <small>Gelar: {{ $gelarWali }}</small>
            </div>
        </div>
    </div>
</div>
@endsection
