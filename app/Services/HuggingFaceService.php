<?php

namespace App\Services;

use App\Models\Pengaturan;
use App\Models\ProfilNagari;
use App\Models\SuratPermohonan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HuggingFaceService
{
    protected string $apiKey;
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.huggingface.key', config('services.huggingface.api_key', env('HUGGINGFACE_API_KEY', '')));
        $this->model = config('services.huggingface.model', env('HUGGINGFACE_MODEL', 'Qwen/Qwen2.5-7B-Instruct'));
        $this->timeout = (int) env('HUGGINGFACE_TIMEOUT', 20);
    }

    /**
     * Layanan Tanya Jawab & Panduan Pembuatan Surat Berbasis AI & Data Resmi Database
     */
    public function generateResponse(string $message, ?string $jenisSurat = null, ?int $suratId = null): array
    {
        $profil = ProfilNagari::first();
        $namaNagari = $profil->nama_nagari ?? 'Nagari Koto Malintang';
        $kecamatan = $profil->kecamatan ?? 'Tanjung Raya';
        $kabupaten = $profil->kabupaten ?? 'Agam';
        $namaWali = Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I');
        $gelarWali = Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro');
        $jamLayanan = Pengaturan::get('jam_layanan', 'Senin - Kamis: 08.00 - 16.00 WIB | Jumat: 08.00 - 11.30 WIB');

        // Master Data Resmi 7 Jenis Surat & Persyaratan
        $daftarSurat = [
            'suket_usaha' => [
                'nama' => 'Surat Keterangan Usaha (SKU)',
                'fungsi' => 'Syarat pengajuan modal usaha, pinjaman KUR Bank (BRI/Bank Nagari), izin operasional usaha/toko/bengkel.',
                'syarat' => 'KTP Pemohon, Kartu Keluarga (KK), Foto Tempat/Aktivitas Usaha.',
            ],
            'suket_tidak_mampu' => [
                'nama' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'fungsi' => 'Permohonan beasiswa KIP-Kuliah/sekolah, keringanan biaya pengobatan BPJS, bantuan sosial pra-sejahtera.',
                'syarat' => 'KTP Pemohon, Kartu Keluarga (KK), Foto Rumah Tempat Tinggal, Surat Pengantar Kepala Jorong.',
            ],
            'suket_domisili' => [
                'nama' => 'Surat Keterangan Domisili (SKD)',
                'fungsi' => 'Bukti tempat tinggal sah bagi warga yang menetap di wilayah Nagari Koto Malintang.',
                'syarat' => 'KTP Pemohon, Kartu Keluarga (KK), Surat Pengantar RT/Jorong.',
            ],
            'suket_meninggal' => [
                'nama' => 'Surat Keterangan Kematian / Meninggal (SKM)',
                'fungsi' => 'Pencatatan akta kematian Disdukcapil, pengurusan klaim asuransi/taspen, dan penetapan ahli waris.',
                'syarat' => 'KTP Pelapor/Ahli Waris, KK Almarhum, Surat Keterangan Kematian dari RS/Bidan/Jorong.',
            ],
            'suket_belum_menikah' => [
                'nama' => 'Surat Keterangan Belum Menikah (SKBM)',
                'fungsi' => 'Syarat pendaftaran TNI/Polri/CPNS, beasiswa, dan melamar pekerjaan.',
                'syarat' => 'KTP Pemohon dan Kartu Keluarga (KK) yang masih berlaku.',
            ],
            'surat_nikah_na' => [
                'nama' => 'Surat Pengantar Nikah (Model NA)',
                'fungsi' => 'Pendaftaran kehendak pernikahan ke Kantor Urusan Agama (KUA) Kecamatan Tanjung Raya.',
                'syarat' => 'KTP Pemohon & Calon Pasangan, Kartu Keluarga (KK), Pengantar Nikah dari Jorong.',
            ],
            'sket_penghasilan' => [
                'nama' => 'Surat Keterangan Penghasilan (SKP)',
                'fungsi' => 'Keterangan rincian estimasi pendapatan orang tua/pribadi untuk beasiswa atau pengajuan kredit.',
                'syarat' => 'KTP, Kartu Keluarga (KK), Slip Gaji atau Surat Pernyataan Penghasilan.',
            ],
        ];

        // Jika ada konteks status surat tertentu
        $konteksSurat = '';
        if ($suratId) {
            $surat = SuratPermohonan::find($suratId);
            if ($surat) {
                $konteksSurat = "\nDATA SURAT USER SAAT INI:\n" .
                                "- Nomor Pengajuan: #KM-" . str_pad($surat->id, 5, '0', STR_PAD_LEFT) . "\n" .
                                "- Jenis Surat: {$surat->nama_jenis_surat}\n" .
                                "- Status Saat Ini: {$surat->status_label}\n" .
                                "- Nomor Resmi: " . ($surat->nomor_surat ?: 'Belum terbit') . "\n";
            }
        }

        $systemPrompt = "Anda adalah Asisten Cerdas Layanan Surat Resmi Nagari Koto Malintang, Kecamatan {$kecamatan}, Kabupaten {$kabupaten}, Sumatera Barat.\n" .
                        "Wali Nagari: {$namaWali} ({$gelarWali})\n" .
                        "Jam Pelayanan Kantor: {$jamLayanan}\n\n" .
                        "DATA RESMI LAYANAN SURAT:\n";

        foreach ($daftarSurat as $key => $info) {
            $systemPrompt .= "- {$info['nama']}: {$info['fungsi']} (Syarat: {$info['syarat']})\n";
        }
        $systemPrompt .= $konteksSurat;

        $systemPrompt .= "\nATURAN UTAMA:\n" .
                         "1. Jawab pertanyaan warga dengan sopan, ramah, dan ringkas dalam Bahasa Indonesia yang baik.\n" .
                         "2. Berikan panduan langkah pengisian form dan syarat berkas sesuai data resmi di atas.\n" .
                         "3. JANGAN PERNAH mengarang data palsu (seperti NIK, nomor surat, atau persyaratan palsu).\n\n" .
                         "PERTANYAAN WARGA: \"{$message}\"\n\nJAWABAN ASISTEN:";

        // Kirim request ke Hugging Face Inference API
        if (!empty($this->apiKey)) {
            $hfResponse = $this->queryHuggingFace($systemPrompt);
            if (!empty($hfResponse)) {
                return [
                    'success' => true,
                    'response' => $hfResponse,
                    'source' => 'huggingface_model',
                ];
            }
        }

        // Fallback cerdas berbasis data resmi nagari jika API sedang sibuk
        $fallback = $this->ruleBasedFallback($message, $daftarSurat);
        return [
            'success' => true,
            'response' => $fallback,
            'source' => 'nagari_knowledge_engine',
        ];
    }

    protected function queryHuggingFace(string $prompt): ?string
    {
        $endpoints = [
            "https://router.huggingface.co/hf-inference/models/{$this->model}",
            "https://api-inference.huggingface.co/models/{$this->model}",
        ];

        foreach ($endpoints as $url) {
            try {
                $res = Http::withToken($this->apiKey)
                    ->timeout($this->timeout)
                    ->post($url, [
                        'inputs' => $prompt,
                        'parameters' => [
                            'max_new_tokens' => 450,
                            'temperature' => 0.5,
                            'return_full_text' => false,
                        ],
                    ]);

                if ($res->successful()) {
                    $json = $res->json();
                    if (is_array($json) && isset($json[0]['generated_text'])) {
                        return trim($json[0]['generated_text']);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("HF API error: " . $e->getMessage());
            }
        }

        return null;
    }

    protected function ruleBasedFallback(string $message, array $daftarSurat): string
    {
        $lower = strtolower($message);

        if (preg_match('/(usaha|sku|dagang|toko|kur|modal|bank)/i', $lower)) {
            $s = $daftarSurat['suket_usaha'];
            return "Untuk keperluan legalitas atau permodalan usaha, Anda dapat mengajukan **{$s['nama']}**.\n\n📋 **Persyaratan:**\n{$s['syarat']}\n\nSilakan pilih menu **Ajukan Surat** pada portal warga.";
        }

        if (preg_match('/(tidak mampu|sktm|miskin|beasiswa|kip|bpjs|bantuan)/i', $lower)) {
            $s = $daftarSurat['suket_tidak_mampu'];
            return "Untuk keperluan beasiswa atau bantuan kesehatan, silakan ajukan **{$s['nama']}**.\n\n📋 **Persyaratan:**\n{$s['syarat']}.";
        }

        if (preg_match('/(nikah|kawin|kua|pengantin|na)/i', $lower)) {
            $s = $daftarSurat['surat_nikah_na'];
            return "Bagi warga yang akan mendaftar ke KUA, silakan ajukan **{$s['nama']}**.\n\n📋 **Persyaratan:**\n{$s['syarat']}.";
        }

        if (preg_match('/(mati|meninggal|wafat|kematian|skm|waris)/i', $lower)) {
            $s = $daftarSurat['suket_meninggal'];
            return "Untuk keperluan pencatatan kematian atau ahli waris, silakan ajukan **{$s['nama']}**.\n\n📋 **Persyaratan:**\n{$s['syarat']}.";
        }

        if (preg_match('/(belum nikah|lajang|single|skbm)/i', $lower)) {
            $s = $daftarSurat['suket_belum_menikah'];
            return "Untuk keperluan melamar kerja atau seleksi kedinasan/TNI/Polri, silakan ajukan **{$s['nama']}**.\n\n📋 **Persyaratan:**\n{$s['syarat']}.";
        }

        if (preg_match('/(gaji|penghasilan|pendapatan|skp)/i', $lower)) {
            $s = $daftarSurat['sket_penghasilan'];
            return "Untuk rincian estimasi pendapatan orang tua/pribadi, silakan ajukan **{$s['nama']}**.\n\n📋 **Persyaratan:**\n{$s['syarat']}.";
        }

        if (preg_match('/(domisili|tinggal|skd|alamat)/i', $lower)) {
            $s = $daftarSurat['suket_domisili'];
            return "Untuk keterangan tempat tinggal sah, silakan ajukan **{$s['nama']}**.\n\n📋 **Persyaratan:**\n{$s['syarat']}.";
        }

        return "Halo dunsanak, selamat datang di Asisten Layanan Surat Nagari Koto Malintang. Kami siap membantu informasi persyaratan 7 jenis surat resmi: SKU, SKTM, SKD, SKM, SKBM, Model NA, dan SKP. Ada yang dapat kami bantu?";
    }
}
