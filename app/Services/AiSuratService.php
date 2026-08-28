<?php

namespace App\Services;

use App\Models\ProfilNagari;
use App\Models\SuratPermohonan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSuratService
{
    protected string $apiKey;
    protected string $model;
    protected string $distilbertModel;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.huggingface.api_key', env('HUGGINGFACE_API_KEY', ''));
        $this->model = config('services.huggingface.model', env('HUGGINGFACE_MODEL', 'Qwen/Qwen2.5-7B-Instruct'));
        $this->distilbertModel = config('services.huggingface.distilbert_model', env('HUGGINGFACE_DISTILBERT_MODEL', 'cahya/distilbert-base-indonesian-522M'));
        $this->timeout = (int) env('HUGGINGFACE_TIMEOUT', 15);
    }

    /**
     * Tanya Jawab Asisten AI Layanan Nagari (Chatbot Warga)
     */
    public function answerCitizenQuestion(string $question, ?string $userName = 'Warga'): string
    {
        $profil = ProfilNagari::first();
        $namaNagari = $profil->nama_nagari ?? 'Nagari Koto Malintang';
        $kecamatan = $profil->kecamatan ?? 'Tanjung Raya';
        $kabupaten = $profil->kabupaten ?? 'Agam';
        $jumlahJorong = $profil->jumlah_jorong ?? 8;

        $prompt = "Anda adalah Asisten Virtual Cerdas Layanan Surat & Informasi Resmi Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, Sumatera Barat.\n" .
                  "Nama Warga yang bertanya: {$userName}\n\n" .
                  "PENGETAHUAN RESMI NAGARI KOTO MALINTANG:\n" .
                  "1. Wilayah: Nagari Koto Malintang, Kec. Tanjung Raya, Kab. Agam, Sumbar. Memiliki {$jumlahJorong} Jorong (di antaranya Jorong Muko-Muko, Alahan Panjang, Pasar Ahad, dll).\n" .
                  "2. Jam Pelayanan Kantor Nagari: Senin-Kamis (08:00 - 16:00 WIB), Jumat (08:00 - 11:30 WIB). Sabtu & Minggu Libur.\n" .
                  "3. 7 Jenis Layanan Surat Resmi Online:\n" .
                  "   - Surat Keterangan Usaha (SKU): Untuk syarat pengajuan modal/KUR bank, legalitas usaha dagang/toko/bengkel. Syarat: KTP, KK, Foto tempat usaha.\n" .
                  "   - Surat Keterangan Tidak Mampu (SKTM): Untuk beasiswa, keringanan BPJS, KIP, bantuan sosial. Syarat: KTP, KK, Foto rumah, Pengantar Jorong.\n" .
                  "   - Surat Keterangan Domisili (SKD): Untuk keterangan tempat tinggal resmi. Syarat: KTP, KK, Pengantar Jorong.\n" .
                  "   - Surat Keterangan Meninggal (SKM): Untuk pencatatan kematian, klaim asuransi/taspen/ahli waris. Syarat: KTP pelapor, KK almarhum, Surat Kematian RS/Bidan/Jorong.\n" .
                  "   - Surat Keterangan Belum Menikah (SKBM): Untuk syarat melamar kerja, pendaftaran TNI/Polri/CPNS, beasiswa. Syarat: KTP, KK.\n" .
                  "   - Surat Pengantar Nikah (Model NA): Untuk didaftarkan ke KUA sebelum menikah. Syarat: KTP pemohon & pasangan, KK, Pengantar Nikah Jorong.\n" .
                  "   - Surat Keterangan Penghasilan (SKP): Untuk rincian estimasi pendapatan orang tua/pribadi. Syarat: KTP, KK, Slip gaji/Surat pernyataan.\n" .
                  "4. Alur Pelayanan: Warga Login -> Isi Form & Upload Berkas -> Diverifikasi Admin Nagari -> Dibubuhi Tanda Tangan & Stempel Resmi Digital oleh Wali Nagari -> Surat Selesai (Dapat diunduh/dicetak langsung secara mandiri).\n\n" .
                  "ATURAN MENJAWAB:\n" .
                  "- Berikan jawaban yang ramah, sopan, akurat, informatif, dan langsung pada intinya dalam bahasa Indonesia yang baik.\n" .
                  "- Jika warga menanyakan jenis surat yang cocok, rekomendasikan jenis surat dan jelaskan syarat berkasnya secara jelas.\n" .
                  "- Jangan pernah mengarang persyaratan yang tidak ada.\n\n" .
                  "PERTANYAAN WARGA: \"{$question}\"\n\n" .
                  "JAWABAN ASISTEN:";

        // 1. Coba Hugging Face Inference API
        if (!empty($this->apiKey)) {
            $hfResult = $this->queryHuggingFace($prompt, 500, 0.6);
            if (!empty($hfResult) && strlen($hfResult) > 20) {
                return $this->cleanAiOutput($hfResult);
            }
        }

        // 2. Fallback Rule-Based Knowledge Engine Cerdas (Zero-latency fallback)
        return $this->generateCitizenAnswerFallback($question, $userName);
    }

    /**
     * Generate naskah resmi surat nagari menggunakan AI / Transformer
     */
    public function generateDraft(array $data, array $spesifik = [], ?string $instruksiTambahan = null): string
    {
        $prompt = $this->buildPrompt($data, $spesifik, $instruksiTambahan);

        // Jika API Key tersedia, coba request ke Hugging Face
        if (!empty($this->apiKey)) {
            $generatedText = $this->queryHuggingFace($prompt, 700, 0.5);
            if (!empty($generatedText) && strlen($generatedText) > 100) {
                return $this->cleanAiOutput($generatedText);
            }
        }

        // Fallback otomatis ke Template Baku Nagari (100% konsisten dan valid)
        return $this->generateBakuFallback($data, $spesifik);
    }

    /**
     * Helper generate naskah langsung dari Model SuratPermohonan
     */
    public function generateDraftFromModel(\App\Models\SuratPermohonan $surat, ?string $instruksiTambahan = null): string
    {
        return $this->generateDraft(
            $surat->toArray(),
            $surat->data_spesifik ?? [],
            $instruksiTambahan
        );
    }

    /**
     * Alias generateIsiSurat
     */
    public function generateIsiSurat(\App\Models\SuratPermohonan $surat, ?string $instruksiTambahan = null): string
    {
        return $this->generateDraftFromModel($surat, $instruksiTambahan);
    }

    /**
     * Sempurnakan / revisi naskah yang sudah ada berdasarkan instruksi Admin
     */
    public function refineDraft(string $currentText, string $instruksi): string
    {
        if (empty($this->apiKey)) {
            return $currentText;
        }

        $prompt = "Berikut adalah draf naskah surat resmi Nagari Koto Malintang:\n\n" .
                  "\"\"\"\n{$currentText}\n\"\"\"\n\n" .
                  "Instruksi perbaikan dari Admin:\n{$instruksi}\n\n" .
                  "Tolong tuliskan kembali teks surat lengkap yang sudah diperbaiki sesuai instruksi di atas dengan bahasa dinas resmi.";

        $result = $this->queryHuggingFace($prompt, 700, 0.4);
        if (!empty($result) && strlen($result) > 50) {
            return $this->cleanAiOutput($result);
        }

        return $currentText;
    }

    /**
     * Klasifikasi Maksud / Jenis Surat menggunakan DistilBERT Transformer
     */
    public function classifyWithDistilBERT(string $textPermohonan): array
    {
        $candidateLabels = [
            'Surat Keterangan Usaha (SKU)',
            'Surat Keterangan Tidak Mampu (SKTM)',
            'Surat Keterangan Domisili (SKD)',
            'Surat Keterangan Kematian / Meninggal (SKM)',
            'Surat Keterangan Belum Menikah (SKBM)',
            'Surat Pengantar Nikah (Model NA)',
            'Surat Keterangan Penghasilan (SKP)',
        ];

        if (!empty($this->apiKey)) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout($this->timeout)
                    ->post("https://router.huggingface.co/hf-inference/models/{$this->distilbertModel}", [
                        'inputs' => $textPermohonan,
                        'parameters' => [
                            'candidate_labels' => $candidateLabels,
                        ],
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['labels'][0])) {
                        return [
                            'predicted_label' => $json['labels'][0],
                            'confidence' => $json['scores'][0] ?? 0.95,
                            'key' => $this->mapLabelToKey($json['labels'][0]),
                            'source' => 'distilbert_huggingface'
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('DistilBERT classification error: ' . $e->getMessage());
            }
        }

        // Rule-based Fallback Classifier
        $lower = strtolower($textPermohonan);
        if (preg_match('/(usaha|dagang|toko|warung|kur|modal|bank|jual|kios)/i', $lower)) {
            return ['predicted_label' => 'Surat Keterangan Usaha (SKU)', 'confidence' => 0.98, 'key' => 'suket_usaha', 'source' => 'distilbert_fallback'];
        }
        if (preg_match('/(mati|meninggal|wafat|kematian|almarhum|ahli waris)/i', $lower)) {
            return ['predicted_label' => 'Surat Keterangan Kematian / Meninggal (SKM)', 'confidence' => 0.98, 'key' => 'suket_meninggal', 'source' => 'distilbert_fallback'];
        }
        if (preg_match('/(belum nikah|belum menikah|lajang|single|belum kawin|gadis|perawan|bujang)/i', $lower)) {
            return ['predicted_label' => 'Surat Keterangan Belum Menikah (SKBM)', 'confidence' => 0.98, 'key' => 'suket_belum_menikah', 'source' => 'distilbert_fallback'];
        }
        if (preg_match('/(nikah|kawin|kua|pengantin|calon suami|calon istri|akad)/i', $lower)) {
            return ['predicted_label' => 'Surat Pengantar Nikah (Model NA)', 'confidence' => 0.98, 'key' => 'surat_nikah_na', 'source' => 'distilbert_fallback'];
        }
        if (preg_match('/(gaji|penghasilan|pendapatan|slip)/i', $lower)) {
            return ['predicted_label' => 'Surat Keterangan Penghasilan (SKP)', 'confidence' => 0.98, 'key' => 'sket_penghasilan', 'source' => 'distilbert_fallback'];
        }
        if (preg_match('/(tidak mampu|sktm|miskin|bantuan|kip|beasiswa|kurang mampu|pkh)/i', $lower)) {
            return ['predicted_label' => 'Surat Keterangan Tidak Mampu (SKTM)', 'confidence' => 0.98, 'key' => 'suket_tidak_mampu', 'source' => 'distilbert_fallback'];
        }

        return ['predicted_label' => 'Surat Keterangan Domisili (SKD)', 'confidence' => 0.92, 'key' => 'suket_domisili', 'source' => 'distilbert_fallback'];
    }

    /**
     * Query ke Hugging Face Inference API / Router
     */
    protected function queryHuggingFace(string $prompt, int $maxTokens = 600, float $temperature = 0.5): ?string
    {
        $endpoints = [
            "https://router.huggingface.co/hf-inference/models/{$this->model}",
            "https://api-inference.huggingface.co/models/{$this->model}",
        ];

        foreach ($endpoints as $url) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout($this->timeout)
                    ->post($url, [
                        'inputs' => $prompt,
                        'parameters' => [
                            'max_new_tokens' => $maxTokens,
                            'temperature' => $temperature,
                            'return_full_text' => false,
                        ],
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (is_array($json) && isset($json[0]['generated_text'])) {
                        return trim($json[0]['generated_text']);
                    } elseif (is_string($json)) {
                        return trim($json);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("HF Query error at {$url}: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Jawaban Fallback Ramah & Cerdas Berbasis Knowledge Base Nagari
     */
    protected function generateCitizenAnswerFallback(string $question, string $userName): string
    {
        $lower = strtolower($question);

        if (preg_match('/(usaha|dagang|toko|warung|kur|modal|sku)/i', $lower)) {
            return "Halo Bapak/Ibu {$userName}, untuk keperluan legalitas atau permodalan usaha seperti KUR Bank, Anda dapat mengajukan **Surat Keterangan Usaha (SKU)**.\n\n" .
                   "📋 **Persyaratan Berkas:**\n" .
                   "1. Foto / Scan KTP Pemohon\n" .
                   "2. Foto / Scan Kartu Keluarga (KK)\n" .
                   "3. Foto tempat / aktivitas usaha Anda\n\n" .
                   "Silakan klik menu **Ajukan Surat** lalu pilih jenis *Surat Keterangan Usaha* untuk memulai permohonan.";
        }

        if (preg_match('/(tidak mampu|sktm|miskin|beasiswa|kip|bpjs|bantuan)/i', $lower)) {
            return "Halo Bapak/Ibu {$userName}, untuk keperluan permohonan beasiswa, keringanan biaya pendidikan (KIP), atau BPJS bantuan sosial, Anda dapat mengajukan **Surat Keterangan Tidak Mampu (SKTM)**.\n\n" .
                   "📋 **Persyaratan Berkas:**\n" .
                   "1. Foto / Scan KTP Pemohon\n" .
                   "2. Foto / Scan Kartu Keluarga (KK)\n" .
                   "3. Foto kondisi rumah / tempat tinggal\n" .
                   "4. Surat Pengantar dari Kepala Jorong setempat\n\n" .
                   "Setelah diajukan, Admin dan Wali Nagari akan memverifikasi permohonan Anda secara online.";
        }

        if (preg_match('/(nikah|kawin|kua|calon|pengantin|na)/i', $lower)) {
            return "Halo Bapak/Ibu {$userName}, bagi warga yang akan melangsungkan pernikahan, permohonan yang tepat adalah **Surat Pengantar Nikah (Model NA)** untuk didaftarkan ke KUA.\n\n" .
                   "📋 **Persyaratan Berkas:**\n" .
                   "1. KTP Pemohon & Calon Pasangan\n" .
                   "2. Kartu Keluarga (KK) Pemohon\n" .
                   "3. Surat Pengantar Kehendak Nikah dari Kepala Jorong setempat\n\n" .
                   "Silakan lengkapi data calon pasangan pada formulir pengajuan surat nikah.";
        }

        if (preg_match('/(mati|meninggal|wafat|kematian|skm|waris)/i', $lower)) {
            return "Halo Bapak/Ibu {$userName}, untuk pencatatan resmi kematian warga, pengurusan klaim asuransi/taspen, atau pembagian ahli waris, silakan ajukan **Surat Keterangan Meninggal (SKM)**.\n\n" .
                   "📋 **Persyaratan Berkas:**\n" .
                   "1. KTP Pelapor / Ahli Waris\n" .
                   "2. Kartu Keluarga (KK) Almarhum/Almarhumah\n" .
                   "3. Surat Keterangan Kematian dari Rumah Sakit / Bidan / Wali Jorong.";
        }

        if (preg_match('/(belum nikah|lajang|single|bujang|gadis|skbm)/i', $lower)) {
            return "Halo Bapak/Ibu {$userName}, untuk keperluan melamar pekerjaan, pendaftaran TNI/Polri, kedinasan, atau beasiswa yang mensyaratkan status lajang, Anda dapat mengajukan **Surat Keterangan Belum Menikah (SKBM)**.\n\n" .
                   "📋 **Persyaratan Berkas:** KTP dan Kartu Keluarga (KK) yang masih berlaku.";
        }

        if (preg_match('/(gaji|penghasilan|pendapatan|skp)/i', $lower)) {
            return "Halo Bapak/Ibu {$userName}, untuk keperluan rincian penghasilan orang tua / pribadi sebagai syarat beasiswa atau kredit, silakan ajukan **Surat Keterangan Penghasilan (SKP)**.\n\n" .
                   "📋 **Persyaratan Berkas:** KTP, Kartu Keluarga, dan Slip Gaji atau Surat Pernyataan Estimasi Penghasilan.";
        }

        if (preg_match('/(jam|buka|tutup|pelayanan|kantor|jadwal)/i', $lower)) {
            return "Halo Bapak/Ibu {$userName}, Kantor Pelayanan Wali Nagari Koto Malintang buka pada:\n\n" .
                   "🕒 **Senin - Kamis:** 08:00 - 16:00 WIB\n" .
                   "🕒 **Jumat:** 08:00 - 11:30 WIB\n" .
                   "🚫 **Sabtu & Minggu:** Tutup / Libur Operasional\n\n" .
                   "Namun, melalui portal Layanan Surat Online ini, Anda dapat mengajukan permohonan surat kapan saja (24 jam sehari)!";
        }

        return "Halo Bapak/Ibu {$userName}, selamat datang di Layanan Nagari Koto Malintang. Kami menyediakan 7 jenis layanan permohonan surat resmi online:\n\n" .
               "1. **SKU** (Surat Keterangan Usaha)\n" .
               "2. **SKTM** (Surat Keterangan Tidak Mampu)\n" .
               "3. **SKD** (Surat Keterangan Domisili)\n" .
               "4. **SKM** (Surat Keterangan Meninggal)\n" .
               "5. **SKBM** (Surat Keterangan Belum Menikah)\n" .
               "6. **Model NA** (Surat Pengantar Nikah ke KUA)\n" .
               "7. **SKP** (Surat Keterangan Penghasilan)\n\n" .
               "Silakan ketikkan keperluan surat Anda, dan kami akan dengan senang hati membantu proses pengajuannya!";
    }

    protected function mapLabelToKey(string $label): string
    {
        $map = [
            'Surat Keterangan Usaha (SKU)' => 'suket_usaha',
            'Surat Keterangan Tidak Mampu (SKTM)' => 'suket_tidak_mampu',
            'Surat Keterangan Domisili (SKD)' => 'suket_domisili',
            'Surat Keterangan Kematian / Meninggal (SKM)' => 'suket_meninggal',
            'Surat Keterangan Belum Menikah (SKBM)' => 'suket_belum_menikah',
            'Surat Pengantar Nikah (Model NA)' => 'surat_nikah_na',
            'Surat Keterangan Penghasilan (SKP)' => 'sket_penghasilan',
        ];

        return $map[$label] ?? 'suket_domisili';
    }

    protected function buildPrompt(array $data, array $spesifik, ?string $instruksiTambahan = null): string
    {
        $jenisNama = $this->getNamaJenisSurat($data['jenis_surat'] ?? '');
        $tglLahir = isset($data['tanggal_lahir']) ? date('d-m-Y', strtotime($data['tanggal_lahir'])) : '-';

        $detailSpesifikStr = '';
        foreach ($spesifik as $key => $val) {
            $label = ucwords(str_replace('_', ' ', $key));
            $detailSpesifikStr .= "- {$label}: {$val}\n";
        }

        $prompt = "Anda adalah Asisten Administrasi Pemerintahan Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, Sumatera Barat.\n" .
                  "Tugas Anda: Buat teks isi surat resmi naskah dinas untuk jenis \"{$jenisNama}\" berdasarkan data permohonan warga berikut.\n\n" .
                  "DATA PEMOHON:\n" .
                  "- Nama Lengkap: " . ($data['nama_pemohon'] ?? '-') . "\n" .
                  "- NIK: " . ($data['nik_pemohon'] ?? '-') . "\n" .
                  "- Tempat, Tanggal Lahir: " . ($data['tempat_lahir'] ?? '-') . ", {$tglLahir}\n" .
                  "- Jenis Kelamin: " . ($data['jenis_kelamin'] ?? '-') . "\n" .
                  "- Agama: " . ($data['agama'] ?? 'Islam') . "\n" .
                  "- Pekerjaan: " . ($data['pekerjaan'] ?? '-') . "\n" .
                  "- Alamat Domisili: " . ($data['alamat'] ?? '-') . "\n" .
                  "- Keperluan Surat: " . ($data['keperluan'] ?? '-') . "\n" .
                  ($detailSpesifikStr ? "DATA TAMBAHAN:\n{$detailSpesifikStr}" : '') .
                  ($instruksiTambahan ? "CATATAN KHUSUS:\n{$instruksiTambahan}\n" : '') . "\n" .
                  "ATURAN FORMAT:\n" .
                  "1. Awali dengan kalimat pernyataan: 'Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, Provinsi Sumatera Barat, dengan ini menerangkan bahwa:'\n" .
                  "2. Cantumkan rincian identitas pemohon secara rapi.\n" .
                  "3. Tuliskan klausul inti keterangan yang formal, jelas, dan sesuai keperluan hukum administrasi nagari.\n" .
                  "4. Akhiri dengan penutup: 'Demikianlah surat keterangan ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.'\n" .
                  "5. Tuliskan HANYA isi naskah surat tanpa komentar luar.";

        return $prompt;
    }

    public function generateBakuFallback(array $data, array $spesifik = []): string
    {
        $jenis = $data['jenis_surat'] ?? '';
        $tglLahir = isset($data['tanggal_lahir']) ? date('d-m-Y', strtotime($data['tanggal_lahir'])) : '-';

        $headerPernyataan = "Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, Provinsi Sumatera Barat, dengan ini menerangkan bahwa:";

        $dataIdentitas = "\n\nNama Lengkap : " . ($data['nama_pemohon'] ?? '-') .
                         "\nNIK : " . ($data['nik_pemohon'] ?? '-') .
                         "\nTempat, Tanggal Lahir : " . ($data['tempat_lahir'] ?? '-') . ", " . $tglLahir .
                         "\nJenis Kelamin : " . ($data['jenis_kelamin'] ?? '-') .
                         "\nAgama : " . ($data['agama'] ?? 'Islam') .
                         "\nPekerjaan : " . ($data['pekerjaan'] ?? '-') .
                         "\nAlamat Domisili : " . ($data['alamat'] ?? '-');

        $isiKeterangan = "";

        if ($jenis === 'suket_usaha') {
            $isiKeterangan = "\n\nAdalah benar yang bersangkutan merupakan penduduk Nagari Koto Malintang dan menurut pencatatan serta sepengetahuan kami, yang bersangkutan memiliki dan menjalankan kegiatan usaha:" .
                             "\n- Nama Usaha : " . ($spesifik['nama_usaha'] ?? '-') .
                             "\n- Bidang Usaha : " . ($spesifik['bidang_usaha'] ?? '-') .
                             "\n- Alamat Usaha : " . ($spesifik['alamat_usaha'] ?? ($data['alamat'] ?? '-')) .
                             "\n\nSurat Keterangan Usaha ini diterbitkan atas permohonan yang bersangkutan untuk keperluan: " . ($data['keperluan'] ?? '-') . ".";
        } elseif ($jenis === 'suket_meninggal') {
            $isiKeterangan = "\n\nDengan ini menerangkan bahwa telah meninggal dunia:" .
                             "\n- Nama Almarhum/Almarhumah : " . ($spesifik['nama_almarhum'] ?? '-') .
                             "\n- Pada Tanggal : " . ($spesifik['tanggal_meninggal'] ?? date('d-m-Y')) .
                             "\n- Tempat Meninggal : " . ($spesifik['tempat_meninggal'] ?? '-') .
                             "\n- Sebab Meninggal : " . ($spesifik['sebab_meninggal'] ?? '-') .
                             "\n\nSurat Keterangan Kematian ini diterbitkan atas permohonan pihak keluarga/ahli waris untuk keperluan: " . ($data['keperluan'] ?? '-') . ".";
        } elseif ($jenis === 'suket_belum_menikah') {
            $isiKeterangan = "\n\nAdalah benar yang bersangkutan merupakan warga yang berdomisili di Nagari Koto Malintang dan hingga saat surat keterangan ini diterbitkan, yang bersangkutan tercatat berstatus BELUM PERNAH MENIKAH (LAJANG/JEJAKA/PERAWAN)." .
                             "\n\nSurat Keterangan ini diberikan kepada yang bersangkutan untuk kelengkapan administrasi keperluan: " . ($data['keperluan'] ?? '-') . ".";
        } elseif ($jenis === 'surat_nikah_na') {
            $isiKeterangan = "\n\nAdalah benar yang bersangkutan merupakan warga Nagari Koto Malintang yang telah memenuhi persyaratan administrasi dan bermaksud akan melangsungkan pernikahan dengan calon pasangan:" .
                             "\n- Nama Calon Pasangan : " . ($spesifik['nama_calon_pasangan'] ?? '-') .
                             "\n- Bin / Binti : " . ($spesifik['bin_binti_pasangan'] ?? '-') .
                             "\n\nSurat Pengantar Kehendak Nikah (Model NA) ini diberikan untuk didaftarkan dan diproses lebih lanjut ke Kantor Urusan Agama (KUA).";
        } elseif ($jenis === 'sket_penghasilan') {
            $nominal = is_numeric($spesifik['nominal_penghasilan'] ?? null) ? number_format((float)$spesifik['nominal_penghasilan'], 0, ',', '.') : '0';
            $isiKeterangan = "\n\nAdalah benar yang bersangkutan merupakan warga Nagari Koto Malintang dengan estimasi rata-rata penghasilan per bulan sebesar Rp " . $nominal . " (" . ($spesifik['sumber_penghasilan'] ?? 'Usaha/Pekerjaan') . ") dengan jumlah tanggungan keluarga " . ($spesifik['tanggungan_anak'] ?? 0) . " orang." .
                             "\n\nSurat Keterangan Penghasilan ini diberikan atas permohonan yang bersangkutan untuk keperluan: " . ($data['keperluan'] ?? '-') . ".";
        } elseif ($jenis === 'suket_tidak_mampu') {
            $isiKeterangan = "\n\nAdalah benar yang bersangkutan merupakan warga masyarakat Nagari Koto Malintang yang berdasarkan keadaan sosial ekonominya tergolong dalam keluarga Kurang Mampu / Pra-Sejahtera." .
                             "\n\nSurat Keterangan Tidak Mampu (SKTM) ini diterbitkan atas permohonan yang bersangkutan untuk keperluan: " . ($data['keperluan'] ?? '-') . ".";
        } elseif ($jenis === 'suket_domisili') {
            $isiKeterangan = "\n\nAdalah benar yang bersangkutan bertempat tinggal dan berdomisili sah di alamat tersebut di atas dalam wilayah hukum Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam (" . ($spesifik['lama_domisili'] ?? 'Sejak Lahir') . ")." .
                             "\n\nSurat Keterangan Domisili ini diberikan kepada yang bersangkutan untuk keperluan: " . ($data['keperluan'] ?? '-') . ".";
        } else {
            $isiKeterangan = "\n\nAdalah benar yang bersangkutan merupakan warga Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam.\n\nSurat keterangan ini diterbitkan untuk keperluan: " . ($data['keperluan'] ?? '-') . ".";
        }

        $penutup = "\n\nDemikianlah surat keterangan ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.";

        return $headerPernyataan . $dataIdentitas . $isiKeterangan . $penutup;
    }

    protected function cleanAiOutput(string $text): string
    {
        $cleaned = preg_replace('/^```[a-zA-Z]*\n?/m', '', $text);
        $cleaned = preg_replace('/```$/m', '', $cleaned);
        return trim($cleaned);
    }

    protected function getNamaJenisSurat(string $jenis): string
    {
        $map = [
            'suket_usaha' => 'Surat Keterangan Usaha (SKU)',
            'suket_meninggal' => 'Surat Keterangan Meninggal (SKM)',
            'suket_belum_menikah' => 'Surat Keterangan Belum Menikah (SKBM)',
            'surat_nikah_na' => 'Surat Pengantar Nikah (Model NA)',
            'sket_penghasilan' => 'Surat Keterangan Penghasilan (SKP)',
            'suket_tidak_mampu' => 'Surat Keterangan Tidak Mampu (SKTM)',
            'suket_domisili' => 'Surat Keterangan Domisili (SKD)',
        ];

        return $map[$jenis] ?? 'Surat Keterangan Resmi';
    }
}
