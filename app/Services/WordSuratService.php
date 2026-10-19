<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pengaturan;
use App\Models\SuratPermohonan;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class WordSuratService
{
    /**
     * Path ke direktori template DOCX.
     */
    protected string $templatePath;

    /**
     * Direktori penyimpanan sementara hasil file DOCX dan PDF.
     */
    protected string $outputDirectory;

    /**
     * Inisialisasi service generator dokumen Word & PDF.
     */
    public function __construct(?string $templatePath = null, ?string $outputDirectory = null)
    {
        $this->templatePath = $templatePath ?: resource_path('templates/surat/SKTM.docx');
        $this->outputDirectory = $outputDirectory ?: storage_path('app/temp_surat');
    }

    /**
     * Generate file DOCX Surat Keterangan Tidak Mampu (SKTM) dari data model SuratPermohonan.
     *
     * @param SuratPermohonan $surat
     * @return string Path absolut file DOCX sementara yang berhasil dibuat
     * @throws Exception
     */
    public function generate(SuratPermohonan $surat): string
    {
        // 1. Validasi keberadaan file template asli
        if (!file_exists($this->templatePath)) {
            throw new Exception("File template DOCX tidak ditemukan pada lokasi: {$this->templatePath}");
        }

        // 2. Pastikan direktori output sementara tersedia
        if (!is_dir($this->outputDirectory)) {
            mkdir($this->outputDirectory, 0755, true);
        }

        // 3. Muat template menggunakan PhpWord TemplateProcessor
        $templateProcessor = new TemplateProcessor($this->templatePath);

        // 4. Pemetaan Placeholder Resmi Naskah
        $nomorSurat = $surat->nomor_surat ?: ('470 / ...... / WN-KM / ' . date('Y'));
        $templateProcessor->setValue('nomor_naskah', $nomorSurat);

        // Format tanggal persetujuan surat / tanggal hari ini (Bahasa Indonesia)
        $tanggalSurat = $surat->disetujui_wali_at ? Carbon::parse($surat->disetujui_wali_at) : Carbon::now();
        $templateProcessor->setValue('tanggal_naskah', $tanggalSurat->translatedFormat('d F Y'));

        // 5. Pemetaan Profil Pejabat Pengirim / Wali Nagari dari Data Spesifik (Snapshot Historis) atau Pengaturan Aktif
        $ds = $surat->data_spesifik;
        if (!is_array($ds) && !empty($ds)) {
            $ds = json_decode((string) $ds, true) ?? [];
        }
        $namaPengirim = (is_array($ds) && !empty($ds['penandatangan_nama']))
            ? $ds['penandatangan_nama']
            : Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I');
        $jabatanPengirim = (is_array($ds) && !empty($ds['penandatangan_jabatan']))
            ? $ds['penandatangan_jabatan']
            : Pengaturan::get('jabatan_wali_nagari', 'WALI NAGARI KOTO MALINTANG');

        $templateProcessor->setValue('nama_pengirim', $namaPengirim);
        $templateProcessor->setValue('jabatan_pengirim', $jabatanPengirim);

        // 6. Pemetaan Tanda Tangan Digital Pejabat
        $this->populateTandaTangan($templateProcessor, $surat);

        // 7. Pemetaan Data Identitas Pemohon
        $this->populateDataPemohon($templateProcessor, $surat);

        // 8. Pemetaan Data Tanggungan Keluarga
        $this->populateTanggunganKeluarga($templateProcessor, $surat);

        // 9. Simpan Dokumen Hasil Generate ke Lokasi Temporary
        $safeName = Str::slug($surat->nama_pemohon ?: 'pemohon', '_');
        $refId = $surat->id ? (string) $surat->id : 'draft';
        $filename = 'SKTM_' . $safeName . '_' . $refId . '_' . date('Ymd_His') . '_' . Str::random(6) . '.docx';
        $outputPath = $this->outputDirectory . DIRECTORY_SEPARATOR . $filename;

        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    /**
     * Generate file PDF resmi dari model SuratPermohonan dengan mengonversi DOCX perantara.
     *
     * @param SuratPermohonan $surat
     * @return string Path absolut file PDF resmi yang siap diunduh
     * @throws Exception
     */
    public function generatePdf(SuratPermohonan $surat): string
    {
        // 1. Validasi: Nomor surat resmi wajib ada sebelum PDF final dibuat
        $nomorSurat = trim((string) $surat->nomor_surat);
        if (empty($nomorSurat)) {
            throw new \InvalidArgumentException("Nomor surat resmi belum diisi oleh Admin. Dokumen PDF final tidak dapat dibuat.");
        }

        // 2. Buat file DOCX perantara dari template
        $docxPath = $this->generate($surat);
        $pdfPath = preg_replace('/\.docx$/i', '.pdf', $docxPath);

        if (file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        $converted = false;

        // 3. Prioritas Konversi: Microsoft Word COM (Native Windows / Laragon)
        try {
            $converted = $this->convertDocxToPdfViaWordCom($docxPath, $pdfPath);
        } catch (\Throwable $e) {
            Log::warning("Konversi DOCX ke PDF via Word COM gagal: " . $e->getMessage());
        }

        // 4. Fallback: Konversi via LibreOffice CLI jika terpasang di sistem
        if (!$converted || !file_exists($pdfPath) || filesize($pdfPath) === 0) {
            try {
                $converted = $this->convertDocxToPdfViaLibreOffice($docxPath, $pdfPath);
            } catch (\Throwable $e) {
                Log::warning("Konversi DOCX ke PDF via LibreOffice gagal: " . $e->getMessage());
            }
        }

        // 5. Bersihkan file DOCX perantara agar tidak menumpuk di disk
        if (file_exists($docxPath)) {
            @unlink($docxPath);
        }

        // 6. Validasi hasil konversi PDF
        if (!$converted || !file_exists($pdfPath) || filesize($pdfPath) === 0) {
            Log::error("Gagal total konversi dokumen surat ke PDF untuk SuratPermohonan ID: " . ($surat->id ?? 'draft'));
            throw new Exception("Surat belum dapat dibuat dalam format PDF resmi. Silakan coba kembali atau hubungi petugas.");
        }

        return $pdfPath;
    }

    /**
     * Konversi DOCX ke PDF menggunakan Microsoft Word COM melalui PowerShell headless.
     */
    protected function convertDocxToPdfViaWordCom(string $docxPath, string $pdfPath): bool
    {
        $escapedDocx = str_replace("'", "''", $docxPath);
        $escapedPdf = str_replace("'", "''", $pdfPath);

        $psScript = "
\$word = New-Object -ComObject Word.Application
\$word.Visible = \$false
\$word.DisplayAlerts = [Microsoft.Office.Interop.Word.WdAlertLevel]::wdAlertsNone
try {
    \$doc = \$word.Documents.Open('$escapedDocx', \$false, \$true)
    \$doc.ExportAsFixedFormat('$escapedPdf', 17) # 17 = wdExportFormatPDF
    \$doc.Close([ref]\$false)
    Write-Output 'CONVERT_SUCCESS'
} catch {
    Write-Output ('CONVERT_ERROR: ' + \$_.Exception.Message)
} finally {
    \$word.Quit([ref]\$false)
    [System.Runtime.Interopservices.Marshal]::ReleaseComObject(\$word) | Out-Null
    [System.GC]::Collect()
    [System.GC]::WaitForPendingFinalizers()
}
";

        $encoded = base64_encode(iconv('UTF-8', 'UTF-16LE', $psScript));
        $cmd = "powershell -NoProfile -NonInteractive -EncodedCommand {$encoded}";

        exec($cmd, $output, $returnCode);

        return file_exists($pdfPath) && filesize($pdfPath) > 0;
    }

    /**
     * Konversi DOCX ke PDF menggunakan LibreOffice jika terpasang di server.
     */
    protected function convertDocxToPdfViaLibreOffice(string $docxPath, string $pdfPath): bool
    {
        $outputDir = dirname($pdfPath);
        $cmd = "soffice --headless --convert-to pdf --outdir " . escapeshellarg($outputDir) . " " . escapeshellarg($docxPath);
        exec($cmd, $output, $returnCode);

        return file_exists($pdfPath) && filesize($pdfPath) > 0;
    }

    /**
     * Memetakan data identitas pemohon ke placeholder template.
     */
    protected function populateDataPemohon(TemplateProcessor $processor, SuratPermohonan $surat): void
    {
        $tanggalLahirFormatted = $surat->tanggal_lahir
            ? Carbon::parse($surat->tanggal_lahir)->translatedFormat('d-m-Y')
            : '-';

        $tempatTglLahir = trim(($surat->tempat_lahir ? $surat->tempat_lahir . ', ' : '') . $tanggalLahirFormatted);

        $processor->setValue('nama_pemohon', $surat->nama_pemohon ?: '-');
        $processor->setValue('nik_pemohon', $surat->nik_pemohon ?: '-');
        $processor->setValue('tempat_lahir', $surat->tempat_lahir ?: '-');
        $processor->setValue('tanggal_lahir', $tanggalLahirFormatted);
        $processor->setValue('tempat_tgl_lahir', $tempatTglLahir);
        $processor->setValue('jenis_kelamin', $surat->jenis_kelamin ?: '-');
        $processor->setValue('agama', $surat->agama ?: 'Islam');
        $processor->setValue('pekerjaan', $surat->pekerjaan ?: '-');
        $processor->setValue('alamat', $surat->alamat ?: '-');
        $processor->setValue('keperluan', $surat->keperluan ?: '-');
    }

    /**
     * Memetakan tanda tangan pengirim dan stempel resmi nagari ke template.
     */
    protected function populateTandaTangan(TemplateProcessor $processor, SuratPermohonan $surat): void
    {
        $ttdPath = $surat->ttd_wali_path ?: Pengaturan::get('tanda_tangan_wali_path');
        $stempelPath = Pengaturan::get('stempel_nagari_path', 'image/stempel_resmi.svg');

        $realTtd = $this->resolveImagePath($ttdPath);
        $realStempel = $this->resolveImagePath($stempelPath);

        // Jika stempel adalah SVG, cek apakah ada versi PNG
        if ($realStempel && strtolower(pathinfo($realStempel, PATHINFO_EXTENSION)) === 'svg') {
            $pngCandidate = preg_replace('/\.svg$/i', '.png', $realStempel);
            if (file_exists($pngCandidate)) {
                $realStempel = $pngCandidate;
            } else {
                $realStempel = null;
            }
        }

        $imageApplied = false;

        // Kasus 1: Keduanya (TTD dan Stempel) tersedia -> Buat komposit transparan resmi
        if ($realTtd && $realStempel && extension_loaded('gd')) {
            try {
                $compositePath = $this->createCompositeTtdStempel($realStempel, $realTtd);
                if ($compositePath && file_exists($compositePath)) {
                    $processor->setImageValue('ttd_pengirim', [
                        'path' => $compositePath,
                        'width' => 160,
                        'height' => 60,
                        'ratio' => true,
                    ]);
                    $imageApplied = true;
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal membuat komposit TTD dan Stempel: " . $e->getMessage());
            }
        }

        // Kasus 2: Hanya TTD tersedia
        if (!$imageApplied && $realTtd) {
            try {
                $processor->setImageValue('ttd_pengirim', [
                    'path' => $realTtd,
                    'width' => 120,
                    'height' => 70,
                    'ratio' => true,
                ]);
                $imageApplied = true;
            } catch (\Throwable $e) {
                Log::warning("Gagal memetakan TTD: " . $e->getMessage());
            }
        }

        // Kasus 3: Hanya Stempel tersedia
        if (!$imageApplied && $realStempel) {
            try {
                $processor->setImageValue('ttd_pengirim', [
                    'path' => $realStempel,
                    'width' => 90,
                    'height' => 90,
                    'ratio' => true,
                ]);
                $imageApplied = true;
            } catch (\Throwable $e) {
                Log::warning("Gagal memetakan Stempel: " . $e->getMessage());
            }
        }

        // Jika tidak ada gambar, kosongkan placeholder
        if (!$imageApplied) {
            $processor->setValue('ttd_pengirim', '');
        }

        // Jika template memiliki variabel stempel_pengirim terpisah, isi juga
        $variables = $processor->getVariables();
        if (in_array('stempel_pengirim', $variables)) {
            if ($realStempel) {
                try {
                    $processor->setImageValue('stempel_pengirim', [
                        'path' => $realStempel,
                        'width' => 80,
                        'height' => 80,
                        'ratio' => true,
                    ]);
                } catch (\Throwable $e) {
                    $processor->setValue('stempel_pengirim', '');
                }
            } else {
                $processor->setValue('stempel_pengirim', '');
            }
        }
    }

    /**
     * Membuat gambar gabungan transparan antara Stempel Nagari dan TTD Pejabat.
     */
    protected function createCompositeTtdStempel(string $stempelPath, string $ttdPath): ?string
    {
        $canvasW = 320;
        $canvasH = 120;
        $canvas = imagecreatetruecolor($canvasW, $canvasH);
        if (!$canvas) return null;

        imagesavealpha($canvas, true);
        $trans = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $trans);

        // 1. Gambar Stempel di sebelah kiri (x=10, y=10, w=100, h=100)
        $stampImg = imagecreatefrompng($stempelPath);
        if ($stampImg) {
            imagecopyresampled($canvas, $stampImg, 10, 10, 0, 0, 100, 100, imagesx($stampImg), imagesy($stampImg));
            imagedestroy($stampImg);
        }

        // 2. Gambar TTD di bagian tengah/kanan (x=80, y=15, w=180, h=90)
        $ext = strtolower(pathinfo($ttdPath, PATHINFO_EXTENSION));
        $ttdImg = null;
        if ($ext === 'png') {
            $ttdImg = imagecreatefrompng($ttdPath);
        } elseif (in_array($ext, ['jpg', 'jpeg', 'jfif'])) {
            $ttdImg = imagecreatefromjpeg($ttdPath);
        }

        if ($ttdImg) {
            imagecopyresampled($canvas, $ttdImg, 80, 15, 0, 0, 180, 90, imagesx($ttdImg), imagesy($ttdImg));
            imagedestroy($ttdImg);
        }

        $filename = 'ttd_stempel_' . md5($stempelPath . $ttdPath) . '.png';
        $outputPath = $this->outputDirectory . DIRECTORY_SEPARATOR . $filename;
        imagepng($canvas, $outputPath);
        imagedestroy($canvas);

        return $outputPath;
    }

    /**
     * Helper resolusi path absolut file gambar dari storage atau public.
     */
    protected function resolveImagePath(?string $path): ?string
    {
        if (empty($path)) return null;

        $p1 = public_path($path);
        if (file_exists($p1) && is_file($p1)) return $p1;

        $p2 = storage_path('app/public/' . ltrim($path, '/'));
        if (file_exists($p2) && is_file($p2)) return $p2;

        return null;
    }

    /**
     * Memetakan data tanggungan keluarga dari data_spesifik jika tabel template memiliki baris placeholder.
     */
    protected function populateTanggunganKeluarga(TemplateProcessor $processor, SuratPermohonan $surat): void
    {
        $dataSpesifik = $surat->data_spesifik ?: [];
        $tanggunganList = $dataSpesifik['tanggungan_keluarga'] ?? [];

        // Cek jika template memiliki variabel baris dinamis (misal: tanggungan_nama / no_tanggungan)
        $variables = $processor->getVariables();

        if (in_array('tanggungan_nama', $variables)) {
            if (is_array($tanggunganList) && count($tanggunganList) > 0) {
                $processor->cloneRow('tanggungan_nama', count($tanggunganList));

                $no = 1;
                foreach ($tanggunganList as $item) {
                    $processor->setValue("no_tanggungan#{$no}", (string) $no);
                    $processor->setValue("tanggungan_nama#{$no}", (string) ($item['nama'] ?? '-'));
                    $processor->setValue("tanggungan_jk#{$no}", (string) ($item['jenis_kelamin'] ?? '-'));
                    $processor->setValue("tanggungan_umur#{$no}", (string) ($item['umur'] ?? '-'));
                    $processor->setValue("tanggungan_hubungan#{$no}", (string) ($item['hubungan'] ?? '-'));
                    $no++;
                }
            } else {
                $processor->setValue('no_tanggungan', '-');
                $processor->setValue('tanggungan_nama', '-');
                $processor->setValue('tanggungan_jk', '-');
                $processor->setValue('tanggungan_umur', '-');
                $processor->setValue('tanggungan_hubungan', '-');
            }
        }
    }

    /**
     * Mengatur path template alternatif jika diperlukan.
     */
    public function setTemplatePath(string $path): self
    {
        $this->templatePath = $path;
        return $this;
    }

    /**
     * Mengatur direktori output alternatif.
     */
    public function setOutputDirectory(string $dir): self
    {
        $this->outputDirectory = $dir;
        return $this;
    }
}
