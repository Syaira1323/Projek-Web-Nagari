<?php

namespace App\Services;

use App\Models\Pengaturan;
use App\Models\SuratPermohonan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleDriveService
{
    protected string $clientId;
    protected string $clientSecret;
    protected ?string $refreshToken;
    protected ?string $accessToken;
    protected ?int $tokenExpiresAt;

    public function __construct()
    {
        $this->clientId = (string) (Pengaturan::get('gdrive_client_id') ?: config('services.google.client_id', env('GOOGLE_CLIENT_ID', env('GOOGLE_DRIVE_CLIENT_ID', ''))));
        $this->clientSecret = (string) (Pengaturan::get('gdrive_client_secret') ?: config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET', env('GOOGLE_DRIVE_CLIENT_SECRET', ''))));
        $this->refreshToken = Pengaturan::get('gdrive_refresh_token') ?: config('services.google.refresh_token', env('GOOGLE_DRIVE_REFRESH_TOKEN', null));
        $this->accessToken = Pengaturan::get('gdrive_access_token', null);
        $this->tokenExpiresAt = (int) Pengaturan::get('gdrive_token_expires_at', 0);
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    public function isConnected(): bool
    {
        return !empty($this->refreshToken) && (bool) Pengaturan::get('gdrive_connected', false);
    }

    /**
     * Dapatkan URL Login Google OAuth untuk Admin
     */
    public function getAuthUrl(string $redirectUri): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/userinfo.email',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Tukarkan kode auth OAuth dengan Token Akses & Refresh Token
     */
    public function exchangeCode(string $code, string $redirectUri): array
    {
        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'] ?? null;
                $refreshToken = $data['refresh_token'] ?? $this->refreshToken;
                $expiresIn = (int) ($data['expires_in'] ?? 3600);
                $expiresAt = time() + $expiresIn - 60;

                Pengaturan::set('gdrive_access_token', $accessToken, 'gdrive', 'text', 'Google Drive Access Token');
                Pengaturan::set('gdrive_token_expires_at', $expiresAt, 'gdrive', 'text', 'Expiry timestamp');
                if (!empty($refreshToken)) {
                    Pengaturan::set('gdrive_refresh_token', $refreshToken, 'gdrive', 'text', 'Google Drive Refresh Token');
                }
                Pengaturan::set('gdrive_connected', '1', 'gdrive', 'boolean', 'Status koneksi Google Drive');

                // Ambil info email akun terhubung
                $email = $this->fetchConnectedEmail($accessToken);
                Pengaturan::set('gdrive_account_email', $email, 'gdrive', 'text', 'Email Akun Google Terhubung');

                // Inisialisasi struktur folder otomatis
                $this->initializeFolderStructure($accessToken);

                return ['success' => true, 'email' => $email, 'message' => 'Google Drive berhasil terhubung.'];
            }

            return ['success' => false, 'message' => 'Gagal verifikasi token Google: ' . $response->body()];
        } catch (\Throwable $e) {
            Log::error('Google Drive OAuth Exchange error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Ambil Access Token yang masih aktif (Otomatis refresh jika expired)
     */
    public function getValidAccessToken(): ?string
    {
        if (!$this->isConnected()) {
            return null;
        }

        if (!empty($this->accessToken) && $this->tokenExpiresAt > time()) {
            return $this->accessToken;
        }

        if (empty($this->refreshToken)) {
            return null;
        }

        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $expiresIn = (int) ($data['expires_in'] ?? 3600);
                $expiresAt = time() + $expiresIn - 60;

                Pengaturan::set('gdrive_access_token', $accessToken, 'gdrive');
                Pengaturan::set('gdrive_token_expires_at', $expiresAt, 'gdrive');
                $this->accessToken = $accessToken;
                $this->tokenExpiresAt = $expiresAt;

                return $accessToken;
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal refresh access token Google Drive: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Uji koneksi Google Drive API
     */
    public function testConnection(): array
    {
        if (!$this->isConnected()) {
            return [
                'success' => false,
                'status' => 'disconnected',
                'message' => 'Google Drive belum terhubung. Silakan hubungkan akun Google Drive terlebih dahulu.',
            ];
        }

        $token = $this->getValidAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'status' => 'token_expired',
                'message' => 'Sesi Google Drive kedaluwarsa atau Refresh Token tidak valid. Silakan hubungkan ulang.',
            ];
        }

        try {
            $response = Http::withToken($token)->get('https://www.googleapis.com/drive/v3/about', [
                'fields' => 'user, storageQuota',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user = $data['user'] ?? [];
                $quota = $data['storageQuota'] ?? [];

                $usedGb = round(($quota['usage'] ?? 0) / 1073741824, 2);
                $totalGb = isset($quota['limit']) ? round($quota['limit'] / 1073741824, 2) . ' GB' : 'Unlimited';

                return [
                    'success' => true,
                    'status' => 'connected',
                    'email' => $user['emailAddress'] ?? Pengaturan::get('gdrive_account_email', '-'),
                    'displayName' => $user['displayName'] ?? '-',
                    'used' => "{$usedGb} GB",
                    'total' => $totalGb,
                    'message' => 'Koneksi Google Drive aktif dan siap digunakan.',
                ];
            }

            return ['success' => false, 'status' => 'error', 'message' => 'Gagal verifikasi ke Google Drive: ' . $response->body()];
        } catch (\Throwable $e) {
            return ['success' => false, 'status' => 'exception', 'message' => $e->getMessage()];
        }
    }

    /**
     * Putus koneksi Google Drive
     */
    public function disconnect(): void
    {
        Pengaturan::set('gdrive_connected', '0', 'gdrive');
        Pengaturan::set('gdrive_access_token', '', 'gdrive');
        Pengaturan::set('gdrive_refresh_token', '', 'gdrive');
        Pengaturan::set('gdrive_account_email', '', 'gdrive');
        Pengaturan::set('gdrive_token_expires_at', '0', 'gdrive');
    }

    /**
     * Inisialisasi hirarki folder Nagari di Google Drive
     */
    public function initializeFolderStructure(?string $token = null): array
    {
        $token = $token ?: $this->getValidAccessToken();
        if (!$token) {
            return [];
        }

        // 1. Root folder: Layanan Surat Nagari Koto Malintang
        $rootId = Pengaturan::get('gdrive_root_folder_id');
        if (!$rootId || !$this->checkFolderExists($token, $rootId)) {
            $rootId = $this->createFolder($token, 'Layanan Surat Nagari Koto Malintang');
            Pengaturan::set('gdrive_root_folder_id', $rootId, 'gdrive');
        }

        // 2. Berkas Persyaratan Folder
        $berkasId = Pengaturan::get('gdrive_berkas_folder_id');
        if (!$berkasId || !$this->checkFolderExists($token, $berkasId)) {
            $berkasId = $this->createFolder($token, 'Berkas Persyaratan', $rootId);
            Pengaturan::set('gdrive_berkas_folder_id', $berkasId, 'gdrive');
        }

        // 3. Surat Selesai Folder
        $suratId = Pengaturan::get('gdrive_surat_folder_id');
        if (!$suratId || !$this->checkFolderExists($token, $suratId)) {
            $suratId = $this->createFolder($token, 'Surat Selesai', $rootId);
            Pengaturan::set('gdrive_surat_folder_id', $suratId, 'gdrive');
        }

        return [
            'root' => $rootId,
            'berkas' => $berkasId,
            'surat' => $suratId,
        ];
    }

    /**
     * Dapatkan atau buat folder spesifik permohonan surat: Berkas Persyaratan / {Tahun} / {ID Pengajuan}
     */
    public function getOrCreateSuratFolder(int $suratId, string $kategori = 'berkas'): ?string
    {
        $token = $this->getValidAccessToken();
        if (!$token) {
            return null;
        }

        $folders = $this->initializeFolderStructure($token);
        $parentFolderId = $kategori === 'surat_selesai' ? ($folders['surat'] ?? null) : ($folders['berkas'] ?? null);

        if (!$parentFolderId) {
            return null;
        }

        // Subfolder Tahun (e.g. 2026)
        $year = date('Y');
        $yearFolderId = $this->createOrFindSubfolder($token, $year, $parentFolderId);

        // Subfolder ID Permohonan (e.g. ID_12)
        $suratFolderId = $this->createOrFindSubfolder($token, "ID_{$suratId}", $yearFolderId);

        return $suratFolderId;
    }

    /**
     * Upload berkas ke Google Drive (Dengan auto fallback ke Local Storage)
     */
    public function uploadFile(UploadedFile $file, int $suratId, string $jenisBerkas): array
    {
        $originalName = $file->getClientOriginalName();
        $cleanOriginalName = pathinfo($originalName, PATHINFO_FILENAME);
        $cleanOriginalName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $cleanOriginalName);
        $extension = $file->getClientOriginalExtension() ?: 'dat';
        $fileName = "{$jenisBerkas}_{$cleanOriginalName}_{$suratId}." . $extension;
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';
        $fileSize = $file->getSize();

        $token = $this->getValidAccessToken();

        // 1. Jika terhubung Google Drive, upload ke Google Drive
        if ($token) {
            try {
                $targetFolderId = $this->getOrCreateSuratFolder($suratId, 'berkas');
                $folderPath = "Layanan Surat Nagari Koto Malintang/Berkas Persyaratan/" . date('Y') . "/ID_{$suratId}";

                $gdriveFile = $this->uploadToGDrive($token, $file, $fileName, $targetFolderId);

                if (!empty($gdriveFile['id'])) {
                    return [
                        'success' => true,
                        'storage_mode' => 'gdrive',
                        'gdrive_file_id' => $gdriveFile['id'],
                        'gdrive_view_link' => $gdriveFile['webViewLink'] ?? "https://drive.google.com/file/d/{$gdriveFile['id']}/view",
                        'file_path_local' => null,
                        'file_name' => $fileName,
                        'nama_file_asli' => $originalName,
                        'mime_type' => $mimeType,
                        'file_size' => $fileSize,
                        'folder_path' => $folderPath,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("Upload Google Drive gagal, beralih ke local storage: " . $e->getMessage());
            }
        }

        // 2. Fallback Storage Lokal Aman (storage/app/berkas/...)
        $year = date('Y');
        $localDir = "berkas/{$year}/ID_{$suratId}";
        $storedPath = $file->storeAs($localDir, $fileName);

        return [
            'success' => true,
            'storage_mode' => 'local',
            'gdrive_file_id' => null,
            'gdrive_view_link' => null,
            'file_path_local' => $storedPath,
            'file_name' => $fileName,
            'nama_file_asli' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'folder_path' => "storage/app/{$storedPath}",
        ];
    }

    /**
     * Upload Surat Selesai ke Google Drive (Format PDF)
     */
    public function uploadSuratSelesai(string $fileContent, string $fileName, int $suratId): ?array
    {
        $token = $this->getValidAccessToken();
        if (!$token) {
            // Simpan lokal jika Google Drive belum terhubung
            $localPath = "surat_selesai/" . date('Y') . "/ID_{$suratId}/{$fileName}";
            Storage::put($localPath, $fileContent);
            return [
                'storage_mode' => 'local',
                'file_path' => $localPath,
                'gdrive_file_id' => null,
            ];
        }

        try {
            $folderId = $this->getOrCreateSuratFolder($suratId, 'surat_selesai');
            $metadata = [
                'name' => $fileName,
                'parents' => $folderId ? [$folderId] : [],
            ];

            $boundary = '-------314159265358979323846';
            $delimiter = "\r\n--" . $boundary . "\r\n";
            $closeDelimiter = "\r\n--" . $boundary . "--";

            $body = $delimiter
                . 'Content-Type: application/json; charset=UTF-8' . "\r\n\r\n"
                . json_encode($metadata)
                . $delimiter
                . 'Content-Type: application/pdf' . "\r\n\r\n"
                . $fileContent
                . $closeDelimiter;

            $response = Http::withToken($token)
                ->withHeaders(['Content-Type' => 'multipart/related; boundary=' . $boundary])
                ->send('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,webViewLink', [
                    'body' => $body,
                ]);

            if ($response->successful()) {
                $json = $response->json();
                return [
                    'storage_mode' => 'gdrive',
                    'gdrive_file_id' => $json['id'] ?? null,
                    'file_path' => $json['webViewLink'] ?? ("https://drive.google.com/file/d/" . ($json['id'] ?? '') . "/view"),
                ];
            } else {
                Log::warning('Upload surat selesai ke GDrive gagal (HTTP ' . $response->status() . '): ' . $response->body(), [
                    'surat_id' => $suratId,
                    'file_name' => $fileName,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Upload surat selesai ke GDrive gagal: ' . $e->getMessage(), [
                'surat_id' => $suratId,
                'file_name' => $fileName,
                'error' => $e->getMessage(),
            ]);
        }

        $localPath = "surat_selesai/" . date('Y') . "/ID_{$suratId}/{$fileName}";
        Storage::put($localPath, $fileContent);
        return [
            'storage_mode' => 'local',
            'file_path' => $localPath,
            'gdrive_file_id' => null,
        ];
    }

    /**
     * Arsipkan PDF final surat yang telah disetujui ke Google Drive (Idempotent & Safe).
     */
    public function archiveSuratSelesai(SuratPermohonan $surat): array
    {
        // 1. Guard Status: Hanya surat dengan status 'disetujui' yang boleh diarsipkan
        if ($surat->status !== 'disetujui') {
            Log::info("Arsip surat ID {$surat->id} dilewati karena status bukan 'disetujui' (Status: {$surat->status}).");
            return [
                'success' => false,
                'message' => "Surat belum disetujui (Status: {$surat->status}). Hanya surat yang disetujui yang dapat diarsipkan.",
            ];
        }

        // 2. Guard Nomor Surat: Nomor surat resmi wajib tersedia
        $nomorSurat = trim((string) $surat->nomor_surat);
        if (empty($nomorSurat)) {
            Log::warning("Arsip surat ID {$surat->id} gagal: Nomor surat resmi belum tersedia.");
            return [
                'success' => false,
                'message' => 'Nomor surat resmi belum tersedia. Surat tidak dapat diarsipkan.',
            ];
        }

        // 3. Idempotency Check: Periksa apakah sudah pernah diarsipkan
        $dataSpesifik = is_array($surat->data_spesifik) ? $surat->data_spesifik : [];
        if (!empty($dataSpesifik['arsip_gdrive']['gdrive_pdf_id'])) {
            Log::info("Surat ID {$surat->id} sudah memiliki arsip Google Drive (ID: {$dataSpesifik['arsip_gdrive']['gdrive_pdf_id']}). Idempotency: upload ulang dilewati.");
            return [
                'success' => true,
                'already_archived' => true,
                'is_duplicate' => false,
                'arsip' => $dataSpesifik['arsip_gdrive'],
            ];
        }

        // Mode lokal: jika sudah pernah diarsipkan ke file lokal yang masih ada
        if (!empty($dataSpesifik['arsip_gdrive']['file_path']) && ($dataSpesifik['arsip_gdrive']['storage_mode'] ?? '') === 'local' && Storage::exists($dataSpesifik['arsip_gdrive']['file_path'])) {
            Log::info("Surat ID {$surat->id} sudah memiliki arsip lokal. Idempotency: upload ulang dilewati.");
            return [
                'success' => true,
                'already_archived' => true,
                'is_duplicate' => false,
                'arsip' => $dataSpesifik['arsip_gdrive'],
            ];
        }

        // 4. Generate PDF final menggunakan WordSuratService
        $wordService = app(WordSuratService::class);
        $pdfPath = null;
        try {
            $pdfPath = $wordService->generatePdf($surat);
            if (!file_exists($pdfPath) || filesize($pdfPath) === 0) {
                throw new \Exception("File PDF final tidak berhasil dibuat di path: {$pdfPath}");
            }
            $fileContent = file_get_contents($pdfPath);
        } catch (\Throwable $e) {
            Log::error("Gagal generate PDF final untuk arsip Surat ID {$surat->id}: " . $e->getMessage(), [
                'surat_id' => $surat->id,
                'jenis_surat' => $surat->jenis_surat,
                'status' => $surat->status,
                'tahap' => 'generate_pdf_arsip',
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Gagal generate PDF final untuk arsip: ' . $e->getMessage(),
            ];
        } finally {
            if ($pdfPath && file_exists($pdfPath)) {
                @unlink($pdfPath);
            }
        }

        // 5. Format Penamaan File: {JENIS_SURAT}_{NAMA_PEMOHON}.pdf
        $singkatan = $surat->singkatan_jenis_surat ?: 'SURAT';
        $safeName = Str::upper(Str::slug($surat->nama_pemohon ?: 'WARGA', '_'));
        $fileName = "{$singkatan}_{$safeName}.pdf";

        // 6. Upload ke Google Drive (atau fallback ke Local Storage)
        $uploadResult = $this->uploadSuratSelesai($fileContent, $fileName, $surat->id);

        // 7. Simpan Metadata Arsip ke data_spesifik (Merge tanpa menghapus field lain)
        $arsipMetadata = [
            'storage_mode' => $uploadResult['storage_mode'] ?? 'local',
            'gdrive_pdf_id' => $uploadResult['gdrive_file_id'] ?? null,
            'gdrive_pdf_url' => $uploadResult['file_path'] ?? ($uploadResult['gdrive_file_id'] ? "https://drive.google.com/file/d/{$uploadResult['gdrive_file_id']}/view" : null),
            'file_path' => $uploadResult['file_path'] ?? null,
            'file_name' => $fileName,
            'uploaded_at' => now()->toIso8601String(),
        ];

        $dataSpesifik['arsip_gdrive'] = $arsipMetadata;
        $surat->data_spesifik = $dataSpesifik;
        $surat->save();

        Log::info("Arsip PDF final Surat ID {$surat->id} berhasil disimpan ({$arsipMetadata['storage_mode']}).", [
            'surat_id' => $surat->id,
            'storage_mode' => $arsipMetadata['storage_mode'],
            'gdrive_pdf_id' => $arsipMetadata['gdrive_pdf_id'],
        ]);

        return [
            'success' => true,
            'already_archived' => false,
            'is_duplicate' => false,
            'arsip' => $arsipMetadata,
        ];
    }

    /**
     * Download / Stream konten berkas untuk diunduh secara aman
     */
    public function downloadFileStream(string $gdriveFileId)
    {
        $token = $this->getValidAccessToken();
        if (!$token) {
            return null;
        }

        try {
            $response = Http::withToken($token)->get("https://www.googleapis.com/drive/v3/files/{$gdriveFileId}?alt=media");
            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Throwable $e) {
            Log::error("Download stream GDrive error: " . $e->getMessage());
        }

        return null;
    }

    // ==========================================
    // HELPER FUNCTIONS
    // ==========================================

    protected function uploadToGDrive(string $token, UploadedFile $file, string $fileName, ?string $folderId): array
    {
        $metadata = [
            'name' => $fileName,
            'parents' => $folderId ? [$folderId] : [],
        ];

        $boundary = '-------314159265358979323846';
        $delimiter = "\r\n--" . $boundary . "\r\n";
        $closeDelimiter = "\r\n--" . $boundary . "--";

        $body = $delimiter
            . 'Content-Type: application/json; charset=UTF-8' . "\r\n\r\n"
            . json_encode($metadata)
            . $delimiter
            . 'Content-Type: ' . ($file->getMimeType() ?: 'application/octet-stream') . "\r\n\r\n"
            . file_get_contents($file->getRealPath())
            . $closeDelimiter;

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'multipart/related; boundary=' . $boundary])
            ->send('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,webViewLink,size', [
                'body' => $body,
            ]);

        return $response->successful() ? $response->json() : [];
    }

    protected function createFolder(string $token, string $name, ?string $parentId = null): ?string
    {
        $metadata = [
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => $parentId ? [$parentId] : [],
        ];

        $response = Http::withToken($token)->post('https://www.googleapis.com/drive/v3/files?fields=id', $metadata);
        if ($response->successful()) {
            return $response->json()['id'] ?? null;
        }

        return null;
    }

    protected function checkFolderExists(string $token, string $folderId): bool
    {
        $response = Http::withToken($token)->get("https://www.googleapis.com/drive/v3/files/{$folderId}?fields=id,trashed");
        if ($response->successful()) {
            $data = $response->json();
            return !($data['trashed'] ?? false);
        }
        return false;
    }

    protected function createOrFindSubfolder(string $token, string $name, string $parentId): ?string
    {
        $query = "name = '{$name}' and '{$parentId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
        $response = Http::withToken($token)->get('https://www.googleapis.com/drive/v3/files', [
            'q' => $query,
            'fields' => 'files(id, name)',
        ]);

        if ($response->successful()) {
            $files = $response->json()['files'] ?? [];
            if (!empty($files[0]['id'])) {
                return $files[0]['id'];
            }
        }

        return $this->createFolder($token, $name, $parentId);
    }

    protected function fetchConnectedEmail(string $token): string
    {
        try {
            $res = Http::withToken($token)->get('https://www.googleapis.com/oauth2/v2/userinfo');
            if ($res->successful()) {
                return $res->json()['email'] ?? 'Google Drive Connected';
            }
        } catch (\Throwable $e) {}

        return 'Google Drive Connected';
    }
}
