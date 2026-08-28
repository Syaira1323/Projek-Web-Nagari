<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak - {{ $surat->nama_jenis_surat }} - {{ $surat->nama_pemohon }}</title>
    <link rel="icon" href="{{ asset('image/logo agam.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 25mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            line-height: 1.8;
            font-size: 12pt;
            position: relative;
        }

        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
            text-align: center;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-text h4 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-text h3 {
            font-size: 15pt;
            font-weight: 800;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .kop-text p {
            font-size: 9pt;
            margin: 0;
            font-style: italic;
        }

        .judul-surat {
            text-align: center;
            margin: 20px 0;
        }

        .judul-surat h5 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .isi-surat {
            text-align: justify;
            white-space: pre-line;
            font-size: 11pt;
            line-height: 1.7;
        }

        .ttd-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
            text-align: center;
        }

        .ttd-box {
            width: 260px;
            position: relative;
        }

        .ttd-space {
            height: 95px;
        }

        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            background: #198754;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .paper {
                box-shadow: none;
                width: 100%;
                min-height: auto;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <button class="no-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>

    <div class="paper">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <img src="{{ asset('image/logo agam.png') }}" class="kop-logo" alt="Logo Agam">
            <div class="kop-text">
                <h4>PEMERINTAH KABUPATEN AGAM</h4>
                <h4>KECAMATAN TANJUNG RAYA</h4>
                <h3>PEMERINTAH NAGARI KOTO MALINTANG</h3>
                <p>Alamat: Jl. Raya Maninjau - Lubuk Basung, Koto Malintang, Kode Pos: 26454</p>
            </div>
        </div>

        <!-- JUDUL -->
        <div class="judul-surat">
            <h5>{{ $surat->nama_jenis_surat }}</h5>
            <div>Nomor: {{ $surat->nomor_surat ?: '470 / ...... / WN-KM / ' . date('m/Y') }}</div>
        </div>

        <!-- ISI -->
        <div class="isi-surat">
            {{ $surat->isi_surat }}
        </div>

        <!-- TTD & STEMPEL -->
        <div class="ttd-section">
            <div class="ttd-box">
                <div>Koto Malintang, {{ $surat->disetujui_wali_at ? $surat->disetujui_wali_at->translatedFormat('d F Y') : date('d F Y') }}</div>
                <div style="font-weight: bold; margin-bottom: 5px;">WALI NAGARI KOTO MALINTANG</div>

                @if ($surat->status === 'disetujui' && $surat->stempel_digital)
                    <div style="position: relative; height: 100px; display: flex; align-items: center; justify-content: center;">
                        <!-- Stempel Nagari -->
                        @if (!empty($stempelPath) && file_exists(public_path($stempelPath)))
                            <img src="{{ asset($stempelPath) }}" alt="Stempel Resmi" style="position: absolute; left: 10px; top: 0; width: 100px; height: 100px; opacity: 0.9; transform: rotate(-8deg); z-index: 1;" />
                        @else
                            <img src="{{ asset('image/stempel_resmi.svg') }}" alt="Stempel Resmi" style="position: absolute; left: 10px; top: 0; width: 95px; height: 95px; opacity: 0.88; transform: rotate(-8deg); z-index: 1;" />
                        @endif

                        <!-- Tanda Tangan Wali Nagari -->
                        @if (!empty($ttdPath) && file_exists(public_path($ttdPath)))
                            <img src="{{ asset($ttdPath) }}" alt="Tanda Tangan Wali" style="position: absolute; right: 20px; top: 10px; max-height: 80px; max-width: 140px; z-index: 2;" />
                        @else
                            <div style="position: absolute; right: 20px; top: 25px; font-family: 'Pacifico', cursive, sans-serif; font-size: 16pt; color: #1a365d; transform: rotate(-6deg); font-weight: bold; z-index: 2;">
                                {{ explode(',', $namaWali)[0] }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="ttd-space"></div>
                @endif

                <div style="font-weight: bold; text-decoration: underline;">{{ $namaWali }}</div>
                <div style="font-size: 10pt;">Gelar: {{ $gelarWali }}</div>
            </div>
        </div>
    </div>

</body>
</html>
