<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BerkasPersyaratan;
use App\Models\Pengaturan;
use App\Models\SuratPermohonan;
use App\Models\UMKM;
use App\Models\User;
use App\Services\AiSuratService;
use App\Services\GoogleDriveService;
use App\Services\WordSuratService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiWargaSuratController extends Controller
{
    protected GoogleDriveService $gdriveService;
    protected AiSuratService $aiService;
    protected WordSuratService $wordService;

    public function __construct(GoogleDriveService $gdriveService, AiSuratService $aiService, WordSuratService $wordService)
    {
        $this->gdriveService = $gdriveService;
        $this->aiService = $aiService;
        $this->wordService = $wordService;
    }

    /**
     * Helper resolve current authenticated user (Session or Token)
     */
    protected function getAuthenticatedUser(Request $request): ?User
    {
        // 1. Cek sesi cookie web bawaan Laravel
        if (Auth::check()) {
            return Auth::user();
        }

        // 2. Cek token dari header Authorization
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = trim(substr($authHeader, 7));
            if (!empty($token)) {
                // A. Format Crypt token (user_auth|id|timestamp)
                try {
                    $decrypted = Crypt::decryptString($token);
                    $parts = explode('|', $decrypted);
                    if (isset($parts[0], $parts[1]) && $parts[0] === 'user_auth' && is_numeric($parts[1])) {
                        $user = User::find((int)$parts[1]);
                        if ($user) return $user;
                    }
                } catch (\Throwable $e) {
                    // Token bukan format Crypt
                }

                // B. Format base64 fallback (id:token)
                $decoded = base64_decode($token, true);
                if ($decoded !== false) {
                    $parts = explode(':', $decoded);
                    if (isset($parts[0]) && is_numeric($parts[0])) {
                        $user = User::find((int)$parts[0]);
                        if ($user) return $user;
                    }
                }
            }
        }

        return null;
    }

    /**
     * GET /api/warga/dashboard
     * Data statistik ringkas untuk dashboard warga
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $totalSurat = SuratPermohonan::where('user_id', $user->id)->count();
        $suratMenunggu = SuratPermohonan::where('user_id', $user->id)->whereIn('status', ['menunggu_admin', 'menunggu_wali_nagari'])->count();
        $suratSelesai = SuratPermohonan::where('user_id', $user->id)->where('status', 'disetujui')->count();
        $suratDitolak = SuratPermohonan::where('user_id', $user->id)->where('status', 'ditolak')->count();
        $totalUmkm = UMKM::where('user_id', $user->id)->count();

        $suratTerbaru = SuratPermohonan::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'nomor_surat' => $s->nomor_surat,
                    'jenis_surat' => $s->jenis_surat,
                    'nama_jenis_surat' => $s->nama_jenis_surat,
                    'keperluan' => $s->keperluan,
                    'status' => $s->status,
                    'status_label' => $s->status_label,
                    'status_badge_color' => $s->status_badge_color,
                    'tanggal' => $s->created_at ? $s->created_at->toISOString() : null,
                    'tanggal_format' => $s->created_at ? $s->created_at->translatedFormat('d F Y') : '-',
                    'catatan_admin' => $s->catatan_admin,
                    'catatan_wali' => $s->catatan_wali,
                ];
            });

        // Hitung notifikasi belum dibaca
        $notifCount = $this->generateNotificationsList($user)->where('read', false)->count();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'nik' => $user->nik,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
            'stats' => [
                'total_surat' => $totalSurat,
                'menunggu' => $suratMenunggu,
                'selesai' => $suratSelesai,
                'ditolak' => $suratDitolak,
                'total_umkm' => $totalUmkm,
                'unread_notif' => $notifCount,
            ],
            'surat_terbaru' => $suratTerbaru,
        ]);
    }

    /**
     * PUT /api/warga/profil
     * Memperbarui profil milik warga yang sedang terautentikasi.
     */
    public function updateProfil(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
            ], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:users,nik,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui di database.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'nik' => $user->nik,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ]);
    }

    /**
     * GET /api/notifikasi
     * Mengambil daftar notifikasi surat pintar untuk warga
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $notifs = $this->generateNotificationsList($user);

        return response()->json([
            'success' => true,
            'unread_count' => $notifs->where('read', false)->count(),
            'data' => $notifs->values(),
        ]);
    }

    /**
     * Helper generate notifikasi berbasis riwayat surat
     */
    protected function generateNotificationsList(User $user)
    {
        $suratList = SuratPermohonan::where('user_id', $user->id)
            ->latest('updated_at')
            ->take(15)
            ->get();

        $notifications = collect();

        foreach ($suratList as $s) {
            $nomorRef = $s->nomor_surat ?: ('#KM-' . str_pad($s->id, 5, '0', STR_PAD_LEFT));

            if ($s->status === 'disetujui') {
                $notifications->push([
                    'id' => 'notif-appr-' . $s->id,
                    'surat_id' => $s->id,
                    'type' => 'success',
                    'icon' => 'bi-check-circle-fill text-success',
                    'title' => 'Surat Telah Disetujui',
                    'message' => "Permohonan {$s->nama_jenis_surat} ({$nomorRef}) telah disetujui & ditandatangani resmi oleh Wali Nagari. Anda dapat mengunduh atau mencetak dokumen sekarang.",
                    'time' => $s->updated_at ? $s->updated_at->diffForHumans() : 'Baru saja',
                    'created_at' => $s->updated_at ? $s->updated_at->toISOString() : null,
                    'read' => false,
                ]);
            } elseif ($s->status === 'menunggu_wali_nagari') {
                $notifications->push([
                    'id' => 'notif-proc-' . $s->id,
                    'surat_id' => $s->id,
                    'type' => 'info',
                    'icon' => 'bi-hourglass-split text-primary',
                    'title' => 'Surat Sedang Diproses',
                    'message' => "Permohonan {$s->nama_jenis_surat} ({$nomorRef}) telah lolos verifikasi Admin Nagari dan sedang menunggu pembubuhan tanda tangan & stempel Wali Nagari.",
                    'time' => $s->updated_at ? $s->updated_at->diffForHumans() : 'Baru saja',
                    'created_at' => $s->updated_at ? $s->updated_at->toISOString() : null,
                    'read' => false,
                ]);
            } elseif ($s->status === 'dikembalikan_ke_admin') {
                $alasan = $s->catatan_wali ?: 'Sedang dalam penyesuaian naskah oleh Admin Nagari.';
                $notifications->push([
                    'id' => 'notif-ret-' . $s->id,
                    'surat_id' => $s->id,
                    'type' => 'info',
                    'icon' => 'bi-arrow-left-right text-info',
                    'title' => 'Naskah Sedang Disesuaikan',
                    'message' => "Permohonan {$s->nama_jenis_surat} ({$nomorRef}) sedang dalam penyesuaian naskah oleh Admin Nagari sesuai arahan Wali Nagari.",
                    'time' => $s->updated_at ? $s->updated_at->diffForHumans() : 'Baru saja',
                    'created_at' => $s->updated_at ? $s->updated_at->toISOString() : null,
                    'read' => false,
                ]);
            } elseif ($s->status === 'perlu_revisi_warga') {
                $alasan = $s->catatan_admin ?: 'Silakan periksa dan perbaiki data atau berkas persyaratan yang diminta.';
                $notifications->push([
                    'id' => 'notif-rev-' . $s->id,
                    'surat_id' => $s->id,
                    'type' => 'warning',
                    'icon' => 'bi-pencil-square text-warning',
                    'title' => 'Permohonan Memerlukan Perbaikan',
                    'message' => "Permohonan {$s->nama_jenis_surat} ({$nomorRef}) memerlukan perbaikan. Catatan Petugas: \"{$alasan}\"",
                    'time' => $s->updated_at ? $s->updated_at->diffForHumans() : 'Baru saja',
                    'created_at' => $s->updated_at ? $s->updated_at->toISOString() : null,
                    'read' => false,
                ]);
            } elseif ($s->status === 'ditolak') {
                $alasan = $s->catatan_admin ?: $s->catatan_wali ?: 'Mohon periksa kelengkapan berkas persyaratan Anda.';
                $notifications->push([
                    'id' => 'notif-rej-' . $s->id,
                    'surat_id' => $s->id,
                    'type' => 'danger',
                    'icon' => 'bi-exclamation-triangle-fill text-danger',
                    'title' => 'Pengajuan Ditolak',
                    'message' => "Permohonan {$s->nama_jenis_surat} ({$nomorRef}) ditolak. Catatan Petugas: \"{$alasan}\"",
                    'time' => $s->updated_at ? $s->updated_at->diffForHumans() : 'Baru saja',
                    'created_at' => $s->updated_at ? $s->updated_at->toISOString() : null,
                    'read' => false,
                ]);
            } else {
                $notifications->push([
                    'id' => 'notif-sub-' . $s->id,
                    'surat_id' => $s->id,
                    'type' => 'warning',
                    'icon' => 'bi-file-earmark-arrow-up text-warning',
                    'title' => 'Pengajuan Surat Diterima',
                    'message' => "Permohonan {$s->nama_jenis_surat} ({$nomorRef}) telah masuk ke sistem dan menunggu verifikasi Admin Nagari.",
                    'time' => $s->created_at ? $s->created_at->diffForHumans() : 'Baru saja',
                    'created_at' => $s->created_at ? $s->created_at->toISOString() : null,
                    'read' => false,
                ]);
            }
        }

        return $notifications;
    }

    /**
     * PATCH /api/notifikasi/{id}/read
     */
    public function markNotificationRead(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai telah dibaca.',
        ]);
    }

    /**
     * PATCH /api/notifikasi/read-all
     */
    public function markAllNotificationsRead(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai telah dibaca.',
        ]);
    }

    /**
     * GET /api/warga/surat & GET /api/pengajuan-surat
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $query = SuratPermohonan::with('berkasPersyaratans')
            ->where('user_id', $user->id)
            ->latest();

        if ($request->filled('status') && $request->query('status') !== 'semua') {
            $query->where('status', $request->query('status'));
        }

        $suratList = $query->paginate(20);

        $items = collect($suratList->items())->map(function ($s) {
            return [
                'id' => $s->id,
                'nomor_surat' => $s->nomor_surat ?: ('#KM-' . str_pad($s->id, 5, '0', STR_PAD_LEFT)),
                'jenis_surat' => $s->jenis_surat,
                'nama_jenis_surat' => $s->nama_jenis_surat,
                'singkatan_jenis_surat' => $s->singkatan_jenis_surat,
                'nama_pemohon' => $s->nama_pemohon,
                'nik_pemohon' => $s->nik_pemohon,
                'keperluan' => $s->keperluan,
                'status' => $s->status,
                'status_label' => $s->status_label,
                'status_badge_color' => $s->status_badge_color,
                'timeline_step' => match($s->status) {
                    'menunggu_admin' => 1,
                    'perlu_revisi_warga' => 1,
                    'menunggu_wali_nagari' => 2,
                    'dikembalikan_ke_admin' => 2,
                    'disetujui' => 3,
                    'ditolak' => -1,
                    default => 1
                },
                'catatan_admin' => $s->catatan_admin,
                'catatan_wali' => $s->catatan_wali,
                'tanggal' => $s->created_at ? $s->created_at->toISOString() : null,
                'tanggal_format' => $s->created_at ? $s->created_at->translatedFormat('d F Y') : '-',
                'disetujui_pada' => $s->disetujui_wali_at ? $s->disetujui_wali_at->toISOString() : null,
                'disetujui_format' => $s->disetujui_wali_at ? $s->disetujui_wali_at->translatedFormat('d F Y') : null,
                'berkas_count' => $s->berkasPersyaratans->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'current_page' => $suratList->currentPage(),
            'last_page' => $suratList->lastPage(),
            'total' => $suratList->total(),
        ]);
    }

    /**
     * GET /api/warga/surat/{id} & GET /api/pengajuan-surat/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $surat = SuratPermohonan::with('berkasPersyaratans')->find($id);

        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat permohonan tidak ditemukan.'], 404);
        }

        if ($surat->user_id !== $user->id && !$user->isAdmin() && !$user->isWaliNagari()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access (IDOR Protection).'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat ?: ('#KM-' . str_pad($surat->id, 5, '0', STR_PAD_LEFT)),
                'jenis_surat' => $surat->jenis_surat,
                'nama_jenis_surat' => $surat->nama_jenis_surat,
                'nama_pemohon' => $surat->nama_pemohon,
                'nik_pemohon' => $surat->nik_pemohon,
                'tempat_lahir' => $surat->tempat_lahir,
                'tanggal_lahir' => $surat->tanggal_lahir ? $surat->tanggal_lahir->format('Y-m-d') : null,
                'tanggal_lahir_format' => $surat->tanggal_lahir ? $surat->tanggal_lahir->translatedFormat('d F Y') : '-',
                'jenis_kelamin' => $surat->jenis_kelamin,
                'pekerjaan' => $surat->pekerjaan,
                'alamat' => $surat->alamat,
                'keperluan' => $surat->keperluan,
                'data_spesifik' => $surat->data_spesifik,
                'isi_surat' => $surat->isi_surat,
                'status' => $surat->status,
                'status_label' => $surat->status_label,
                'status_badge_color' => $surat->status_badge_color,
                'timeline_step' => match($surat->status) {
                    'menunggu_admin' => 1,
                    'perlu_revisi_warga' => 1,
                    'menunggu_wali_nagari' => 2,
                    'dikembalikan_ke_admin' => 2,
                    'disetujui' => 3,
                    'ditolak' => -1,
                    default => 1
                },
                'catatan_admin' => $surat->catatan_admin,
                'catatan_wali' => $surat->catatan_wali,
                'tanggal' => $surat->created_at ? $surat->created_at->toISOString() : null,
                'tanggal_format' => $surat->created_at ? $surat->created_at->translatedFormat('d F Y, H:i') : '-',
                'disetujui_pada' => $surat->disetujui_wali_at ? $surat->disetujui_wali_at->translatedFormat('d F Y, H:i') : null,
                'ttd_wali_url' => $surat->ttd_wali_path ? asset($surat->ttd_wali_path) : (Pengaturan::get('tanda_tangan_wali_path') ? asset(Pengaturan::get('tanda_tangan_wali_path')) : null),
                'stempel_url' => asset(Pengaturan::get('stempel_nagari_path', 'image/stempel_resmi.svg')),
                'nama_wali' => Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
                'gelar_wali' => Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
                'berkas' => $surat->berkasPersyaratans->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'jenis_berkas' => $b->jenis_berkas,
                        'label_jenis' => $b->label_jenis,
                        'nama_file_asli' => $b->nama_file_asli,
                        'formatted_size' => $b->formatted_size,
                        'download_url' => route('warga.berkas.download', $b->id),
                    ];
                }),
            ]
        ]);
    }

    /**
     * GET /api/pengajuan-surat/{id}/download-pdf & GET /api/warga/surat/{id}/download-pdf
     * Unduh Dokumen PDF Resmi Surat (Hanya untuk Surat yang Telah Selesai / Disetujui)
     */
    public function downloadPdf(Request $request, int $id)
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Silakan login terlebih dahulu.'
            ], 401);
        }

        $surat = SuratPermohonan::find($id);
        if (!$surat) {
            return response()->json([
                'success' => false,
                'message' => 'Surat permohonan tidak ditemukan.'
            ], 404);
        }

        // 1. Otorisasi Keamanan Warga (IDOR Protection)
        if ($surat->user_id !== $user->id && !$user->isAdmin() && !$user->isWaliNagari()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengunduh surat ini.'
            ], 403);
        }

        // 2. Pemeriksaan Status: Dokumen final hanya boleh diunduh jika surat sudah disetujui & berstempel
        if ($surat->status !== 'disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Surat masih dalam proses dan belum dapat diunduh.'
            ], 403);
        }

        // 3. Pemeriksaan Nomor Surat Resmi: Dokumen final wajib memiliki nomor resmi yang diisi oleh Admin
        if (empty(trim((string) $surat->nomor_surat))) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor surat resmi belum diisi oleh Admin. Dokumen PDF final belum dapat dibuat.'
            ], 403);
        }

        try {
            // 4. Generate PDF resmi dari template DOCX perantara
            $pdfPath = $this->wordService->generatePdf($surat);

            // 4. Format Nama File Download Bersih: {JENIS_SURAT}_{NAMA_PEMOHON}.pdf (Contoh: SKTM_HENDRA_KURNIAWAN.pdf)
            $singkatan = $surat->singkatan_jenis_surat ?: 'SURAT';
            $safeName = Str::upper(Str::slug($surat->nama_pemohon ?: 'WARGA', '_'));
            $downloadFilename = $singkatan . '_' . $safeName . '.pdf';

            // 5. Kembalikan PDF sebagai response download dan bersihkan file temp setelah dikirim
            return response()->download($pdfPath, $downloadFilename, [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ])->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            Log::error("API Gagal men-generate PDF Surat ID {$surat->id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Surat belum dapat diunduh. Silakan coba kembali.'
            ], 500);
        }
    }

    /**
     * POST /api/pengajuan-surat & POST /api/warga/surat
     */
    public function store(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated. Silakan login terlebih dahulu.'], 401);
        }

        $validated = $request->validate([
            'jenis_surat' => 'required|string',
            'nama_pemohon' => 'required|string|max:255',
            'nik_pemohon' => 'nullable|string|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|string',
            'pekerjaan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'keperluan' => 'required|string|max:500',
        ]);

        // Validasi Seluruh Berkas Persyaratan yang Diunggah (Format: JPG, JPEG, PNG, PDF | Maks: 5MB)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $maxSizeBytes = 5 * 1024 * 1024; // 5 MB

        foreach ($request->allFiles() as $inputKey => $fileOrArray) {
            $filesList = is_array($fileOrArray) ? $fileOrArray : [$fileOrArray];
            foreach ($filesList as $f) {
                if ($f instanceof \Illuminate\Http\UploadedFile) {
                    $ext = strtolower($f->getClientOriginalExtension());
                    $size = $f->getSize();
                    $origName = $f->getClientOriginalName();

                    if (!in_array($ext, $allowedExtensions)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Format berkas '{$origName}' (.{$ext}) tidak diperbolehkan. Format yang didukung: JPG, JPEG, PNG, atau PDF."
                        ], 422);
                    }

                    if ($size > $maxSizeBytes) {
                        $sizeMb = round($size / 1048576, 2);
                        return response()->json([
                            'success' => false,
                            'message' => "Ukuran berkas '{$origName}' ({$sizeMb} MB) melebihi batas maksimum 5 MB."
                        ], 422);
                    }
                }
            }
        }

        $dataSpesifik = [];
        $allowedSpesifikKeys = [
            'nama_usaha', 'bidang_usaha', 'alamat_usaha',
            'nama_almarhum', 'tanggal_meninggal', 'tempat_meninggal', 'sebab_meninggal',
            'nama_calon_pasangan', 'bin_binti_pasangan',
            'nominal_penghasilan', 'sumber_penghasilan',
            'kondisi_ekonomi', 'estimasi_penghasilan',
        ];

        foreach ($allowedSpesifikKeys as $key) {
            if ($request->filled($key)) {
                $dataSpesifik[$key] = $request->input($key);
            }
        }

        // Sanitasi dan Pemetaan Data Tanggungan Keluarga (Khusus SKTM)
        $rawTanggungan = $request->input('tanggungan_keluarga');
        if (is_string($rawTanggungan)) {
            $decoded = json_decode($rawTanggungan, true);
            if (is_array($decoded)) {
                $rawTanggungan = $decoded;
            }
        }

        if (is_array($rawTanggungan) && count($rawTanggungan) > 0) {
            $sanitizedTanggungan = [];
            foreach ($rawTanggungan as $item) {
                if (is_array($item)) {
                    $nama = trim((string) ($item['nama'] ?? ''));
                    $jk = trim((string) ($item['jenis_kelamin'] ?? ''));
                    $umur = trim((string) ($item['umur'] ?? ''));
                    $hubungan = trim((string) ($item['hubungan'] ?? ($item['hubungan_kk'] ?? '')));

                    if ($nama !== '') {
                        $sanitizedTanggungan[] = [
                            'nama' => $nama,
                            'jenis_kelamin' => in_array($jk, ['Laki-laki', 'Perempuan']) ? $jk : ($jk ?: 'Laki-laki'),
                            'umur' => $umur !== '' ? $umur : '-',
                            'hubungan' => $hubungan !== '' ? $hubungan : 'Keluarga',
                        ];
                    }
                }
            }
            if (count($sanitizedTanggungan) > 0) {
                $dataSpesifik['tanggungan_keluarga'] = $sanitizedTanggungan;
            }
        }

        // Simpan permohonan ke MySQL
        $surat = new SuratPermohonan();
        $surat->user_id = $user->id;
        $surat->jenis_surat = $validated['jenis_surat'];
        $surat->nama_pemohon = $validated['nama_pemohon'];
        $surat->nik_pemohon = $validated['nik_pemohon'] ?? $user->nik ?? '-';
        $surat->tempat_lahir = $validated['tempat_lahir'];
        $surat->tanggal_lahir = $validated['tanggal_lahir'];
        $surat->jenis_kelamin = $validated['jenis_kelamin'];
        $surat->agama = $validated['agama'];
        $surat->pekerjaan = $validated['pekerjaan'];
        $surat->alamat = $validated['alamat'];
        $surat->keperluan = $validated['keperluan'];
        $surat->data_spesifik = $dataSpesifik;
        $surat->status = 'menunggu_admin';

        // Susun draf redaksi awal menggunakan AI Service
        $aiSpesifik = [];
        foreach ($dataSpesifik as $k => $v) {
            if (is_array($v)) {
                if ($k === 'tanggungan_keluarga') {
                    $aiSpesifik[$k] = count($v) . ' orang (' . implode(', ', array_map(fn($item) => ($item['nama'] ?? '') . ' [' . ($item['hubungan'] ?? '') . ']', $v)) . ')';
                } else {
                    $aiSpesifik[$k] = json_encode($v);
                }
            } else {
                $aiSpesifik[$k] = $v;
            }
        }
        $surat->isi_surat = $this->aiService->generateDraft($surat->toArray(), $aiSpesifik);
        $surat->save();

        // Upload Berkas Persyaratan ke Storage / Google Drive & Simpan ke tabel berkas_persyaratans
        $berkasMapping = [
            'ktp' => 'ktp',
            'berkas_ktp' => 'ktp',
            'kk' => 'kk',
            'berkas_kk' => 'kk',
            'foto_rumah' => 'foto_rumah',
            'berkas_foto_rumah' => 'foto_rumah',
            'surat_pengantar' => 'surat_pengantar',
            'berkas_surat_pengantar' => 'surat_pengantar',
            'berkas_pengantar' => 'surat_pengantar',
            'berkas_pengantar_jorong' => 'surat_pengantar',
            'pengantar_jorong' => 'surat_pengantar',
            'foto_usaha' => 'foto_usaha',
            'berkas_foto_usaha' => 'foto_usaha',
            'surat_kematian_rs' => 'surat_kematian_rs',
            'berkas_surat_kematian_rs' => 'surat_kematian_rs',
            'berkas_kematian' => 'surat_kematian_rs',
            'ktp_pasangan' => 'ktp_pasangan',
            'berkas_ktp_pasangan' => 'ktp_pasangan',
            'slip_penghasilan' => 'slip_penghasilan',
            'berkas_slip_penghasilan' => 'slip_penghasilan',
            'berkas_slip' => 'slip_penghasilan',
            'berkas_surat_pernyataan' => 'slip_penghasilan',
            'berkas_pas_foto' => 'dokumen_pendukung',
            'dokumen_pendukung' => 'dokumen_pendukung',
            'berkas_lainnya' => 'dokumen_pendukung',
        ];

        $processedTypes = [];

        foreach ($berkasMapping as $inputName => $jenisBerkas) {
            if ($request->hasFile($inputName) && !in_array($jenisBerkas, $processedTypes)) {
                $file = $request->file($inputName);
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $uploadRes = $this->gdriveService->uploadFile($file, $surat->id, $jenisBerkas);

                    if (!empty($uploadRes['success'])) {
                        BerkasPersyaratan::create([
                            'surat_permohonan_id' => $surat->id,
                            'jenis_berkas' => $jenisBerkas,
                            'nama_file_asli' => $uploadRes['nama_file_asli'] ?? $file->getClientOriginalName(),
                            'gdrive_file_id' => $uploadRes['gdrive_file_id'] ?? null,
                            'gdrive_view_link' => $uploadRes['gdrive_view_link'] ?? null,
                            'file_path_local' => $uploadRes['file_path_local'] ?? null,
                            'mime_type' => $uploadRes['mime_type'] ?? $file->getMimeType(),
                            'file_size' => $uploadRes['file_size'] ?? $file->getSize(),
                            'folder_path' => $uploadRes['folder_path'] ?? null,
                        ]);
                        $processedTypes[] = $jenisBerkas;
                    }
                }
            }
        }

        // Periksa jika ada upload berkas generic dalam files[] jika belum ada berkas terunggah
        if (empty($processedTypes) && $request->hasFile('files')) {
            $files = $request->file('files');
            if (is_array($files)) {
                $idx = 1;
                foreach ($files as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $jenisBerkas = 'dokumen_' . $idx;
                        $uploadRes = $this->gdriveService->uploadFile($file, $surat->id, $jenisBerkas);
                        if (!empty($uploadRes['success'])) {
                            BerkasPersyaratan::create([
                                'surat_permohonan_id' => $surat->id,
                                'jenis_berkas' => $jenisBerkas,
                                'nama_file_asli' => $uploadRes['nama_file_asli'] ?? $file->getClientOriginalName(),
                                'gdrive_file_id' => $uploadRes['gdrive_file_id'] ?? null,
                                'gdrive_view_link' => $uploadRes['gdrive_view_link'] ?? null,
                                'file_path_local' => $uploadRes['file_path_local'] ?? null,
                                'mime_type' => $uploadRes['mime_type'] ?? $file->getMimeType(),
                                'file_size' => $uploadRes['file_size'] ?? $file->getSize(),
                                'folder_path' => $uploadRes['folder_path'] ?? null,
                            ]);
                            $idx++;
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dikirim dan tersimpan di database.',
            'surat' => [
                'id' => $surat->id,
                'nomor_ref' => '#KM-' . str_pad($surat->id, 5, '0', STR_PAD_LEFT),
                'jenis_surat' => $surat->jenis_surat,
                'nama_jenis_surat' => $surat->nama_jenis_surat,
                'status' => $surat->status,
                'status_label' => $surat->status_label,
            ]
        ], 201);
    }

    /**
     * POST /api/pengajuan-surat/{id}/revisi
     * Warga mengirim perbaikan data dan/atau berkas pada permohonan yang berstatus perlu_revisi_warga
     */
    public function revisi(Request $request, int $id): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated. Silakan login terlebih dahulu.'], 401);
        }

        $surat = SuratPermohonan::find($id);
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat permohonan tidak ditemukan.'], 404);
        }

        // Keamanan IDOR: Wajib pemilik surat
        if ($surat->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access (IDOR Protection). Anda tidak memiliki izin untuk merevisi surat ini.'], 403);
        }

        // Validasi Status: Hanya boleh jika status perlu_revisi_warga
        if ($surat->status !== 'perlu_revisi_warga') {
            return response()->json([
                'success' => false,
                'message' => 'Surat ini tidak dalam status memerlukan revisi warga.'
            ], 422);
        }

        // Validasi data identitas (opsional / fallback ke nilai yang sudah ada)
        $validated = $request->validate([
            'nama_pemohon' => 'sometimes|required|string|max:255',
            'nik_pemohon' => 'nullable|string|max:20',
            'tempat_lahir' => 'sometimes|required|string|max:100',
            'tanggal_lahir' => 'sometimes|required|date',
            'jenis_kelamin' => 'sometimes|required|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string',
            'pekerjaan' => 'sometimes|required|string|max:100',
            'alamat' => 'sometimes|required|string',
            'keperluan' => 'sometimes|required|string|max:500',
        ]);

        // Validasi Seluruh Berkas Persyaratan yang Diunggah (Format: JPG, JPEG, PNG, PDF | Maks: 5MB)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $maxSizeBytes = 5 * 1024 * 1024; // 5 MB

        foreach ($request->allFiles() as $inputKey => $fileOrArray) {
            $filesList = is_array($fileOrArray) ? $fileOrArray : [$fileOrArray];
            foreach ($filesList as $f) {
                if ($f instanceof \Illuminate\Http\UploadedFile) {
                    $ext = strtolower($f->getClientOriginalExtension());
                    $size = $f->getSize();
                    $origName = $f->getClientOriginalName();

                    if (!in_array($ext, $allowedExtensions)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Format berkas '{$origName}' (.{$ext}) tidak diperbolehkan. Format yang didukung: JPG, JPEG, PNG, atau PDF."
                        ], 422);
                    }

                    if ($size > $maxSizeBytes) {
                        $sizeMb = round($size / 1048576, 2);
                        return response()->json([
                            'success' => false,
                            'message' => "Ukuran berkas '{$origName}' ({$sizeMb} MB) melebihi batas maksimum 5 MB."
                        ], 422);
                    }
                }
            }
        }

        // Perbarui data identitas jika dikirim
        if ($request->filled('nama_pemohon')) $surat->nama_pemohon = $validated['nama_pemohon'];
        if ($request->filled('nik_pemohon')) $surat->nik_pemohon = $validated['nik_pemohon'];
        if ($request->filled('tempat_lahir')) $surat->tempat_lahir = $validated['tempat_lahir'];
        if ($request->filled('tanggal_lahir')) $surat->tanggal_lahir = $validated['tanggal_lahir'];
        if ($request->filled('jenis_kelamin')) $surat->jenis_kelamin = $validated['jenis_kelamin'];
        if ($request->filled('agama')) $surat->agama = $validated['agama'];
        if ($request->filled('pekerjaan')) $surat->pekerjaan = $validated['pekerjaan'];
        if ($request->filled('alamat')) $surat->alamat = $validated['alamat'];
        if ($request->filled('keperluan')) $surat->keperluan = $validated['keperluan'];

        // Perbarui data spesifik
        $dataSpesifik = $surat->data_spesifik ?: [];
        $allowedSpesifikKeys = [
            'nama_usaha', 'bidang_usaha', 'alamat_usaha',
            'nama_almarhum', 'tanggal_meninggal', 'tempat_meninggal', 'sebab_meninggal',
            'nama_calon_pasangan', 'bin_binti_pasangan',
            'nominal_penghasilan', 'sumber_penghasilan',
            'kondisi_ekonomi', 'estimasi_penghasilan',
        ];

        foreach ($allowedSpesifikKeys as $key) {
            if ($request->filled($key)) {
                $dataSpesifik[$key] = $request->input($key);
            }
        }

        // Tanggungan keluarga jika ada dikirim
        if ($request->has('tanggungan_keluarga')) {
            $rawTanggungan = $request->input('tanggungan_keluarga');
            if (is_string($rawTanggungan)) {
                $decoded = json_decode($rawTanggungan, true);
                if (is_array($decoded)) {
                    $rawTanggungan = $decoded;
                }
            }

            if (is_array($rawTanggungan)) {
                $sanitizedTanggungan = [];
                foreach ($rawTanggungan as $item) {
                    if (is_array($item)) {
                        $nama = trim((string) ($item['nama'] ?? ''));
                        $jk = trim((string) ($item['jenis_kelamin'] ?? ''));
                        $umur = trim((string) ($item['umur'] ?? ''));
                        $hubungan = trim((string) ($item['hubungan'] ?? ($item['hubungan_kk'] ?? '')));

                        if ($nama !== '') {
                            $sanitizedTanggungan[] = [
                                'nama' => $nama,
                                'jenis_kelamin' => in_array($jk, ['Laki-laki', 'Perempuan']) ? $jk : ($jk ?: 'Laki-laki'),
                                'umur' => $umur !== '' ? $umur : '-',
                                'hubungan' => $hubungan !== '' ? $hubungan : 'Keluarga',
                            ];
                        }
                    }
                }
                $dataSpesifik['tanggungan_keluarga'] = $sanitizedTanggungan;
            }
        }

        $surat->data_spesifik = $dataSpesifik;

        // Susun ulang draf redaksi awal menggunakan AI Service
        $aiSpesifik = [];
        foreach ($dataSpesifik as $k => $v) {
            if (is_array($v)) {
                if ($k === 'tanggungan_keluarga') {
                    $aiSpesifik[$k] = count($v) . ' orang (' . implode(', ', array_map(fn($item) => ($item['nama'] ?? '') . ' [' . ($item['hubungan'] ?? '') . ']', $v)) . ')';
                } else {
                    $aiSpesifik[$k] = json_encode($v);
                }
            } else {
                $aiSpesifik[$k] = $v;
            }
        }
        $surat->isi_surat = $this->aiService->generateDraft($surat->toArray(), $aiSpesifik);

        // Upload Berkas Persyaratan Pengganti jika ada file baru diunggah
        $berkasMapping = [
            'ktp' => 'ktp',
            'berkas_ktp' => 'ktp',
            'kk' => 'kk',
            'berkas_kk' => 'kk',
            'foto_rumah' => 'foto_rumah',
            'berkas_foto_rumah' => 'foto_rumah',
            'surat_pengantar' => 'surat_pengantar',
            'berkas_surat_pengantar' => 'surat_pengantar',
            'berkas_pengantar' => 'surat_pengantar',
            'berkas_pengantar_jorong' => 'surat_pengantar',
            'pengantar_jorong' => 'surat_pengantar',
            'foto_usaha' => 'foto_usaha',
            'berkas_foto_usaha' => 'foto_usaha',
            'surat_kematian_rs' => 'surat_kematian_rs',
            'berkas_surat_kematian_rs' => 'surat_kematian_rs',
            'berkas_kematian' => 'surat_kematian_rs',
            'ktp_pasangan' => 'ktp_pasangan',
            'berkas_ktp_pasangan' => 'ktp_pasangan',
            'slip_penghasilan' => 'slip_penghasilan',
            'berkas_slip_penghasilan' => 'slip_penghasilan',
            'berkas_slip' => 'slip_penghasilan',
            'berkas_surat_pernyataan' => 'slip_penghasilan',
            'berkas_pas_foto' => 'dokumen_pendukung',
            'dokumen_pendukung' => 'dokumen_pendukung',
            'berkas_lainnya' => 'dokumen_pendukung',
        ];

        $processedTypes = [];

        foreach ($berkasMapping as $inputName => $jenisBerkas) {
            if ($request->hasFile($inputName) && !in_array($jenisBerkas, $processedTypes)) {
                $file = $request->file($inputName);
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $uploadRes = $this->gdriveService->uploadFile($file, $surat->id, $jenisBerkas);

                    if (!empty($uploadRes['success'])) {
                        // Cari apakah berkas jenis ini sudah ada
                        $existingBerkas = BerkasPersyaratan::where('surat_permohonan_id', $surat->id)
                            ->where('jenis_berkas', $jenisBerkas)
                            ->first();

                        if ($existingBerkas) {
                            // Hapus file fisik lama di local storage jika ada
                            if (!empty($existingBerkas->file_path_local) && file_exists(storage_path('app/' . $existingBerkas->file_path_local))) {
                                @unlink(storage_path('app/' . $existingBerkas->file_path_local));
                            }

                            $existingBerkas->update([
                                'nama_file_asli' => $uploadRes['nama_file_asli'] ?? $file->getClientOriginalName(),
                                'gdrive_file_id' => $uploadRes['gdrive_file_id'] ?? null,
                                'gdrive_view_link' => $uploadRes['gdrive_view_link'] ?? null,
                                'file_path_local' => $uploadRes['file_path_local'] ?? null,
                                'mime_type' => $uploadRes['mime_type'] ?? $file->getMimeType(),
                                'file_size' => $uploadRes['file_size'] ?? $file->getSize(),
                                'folder_path' => $uploadRes['folder_path'] ?? null,
                            ]);
                        } else {
                            BerkasPersyaratan::create([
                                'surat_permohonan_id' => $surat->id,
                                'jenis_berkas' => $jenisBerkas,
                                'nama_file_asli' => $uploadRes['nama_file_asli'] ?? $file->getClientOriginalName(),
                                'gdrive_file_id' => $uploadRes['gdrive_file_id'] ?? null,
                                'gdrive_view_link' => $uploadRes['gdrive_view_link'] ?? null,
                                'file_path_local' => $uploadRes['file_path_local'] ?? null,
                                'mime_type' => $uploadRes['mime_type'] ?? $file->getMimeType(),
                                'file_size' => $uploadRes['file_size'] ?? $file->getSize(),
                                'folder_path' => $uploadRes['folder_path'] ?? null,
                            ]);
                        }
                        $processedTypes[] = $jenisBerkas;
                    }
                }
            }
        }

        // Ubah status permohonan kembali ke menunggu_admin
        $surat->status = 'menunggu_admin';
        $surat->save();

        return response()->json([
            'success' => true,
            'message' => 'Perbaikan permohonan surat berhasil dikirim dan masuk kembali ke antrean verifikasi Admin.',
            'surat' => [
                'id' => $surat->id,
                'nomor_ref' => '#KM-' . str_pad($surat->id, 5, '0', STR_PAD_LEFT),
                'jenis_surat' => $surat->jenis_surat,
                'nama_jenis_surat' => $surat->nama_jenis_surat,
                'status' => $surat->status,
                'status_label' => $surat->status_label,
            ]
        ]);
    }

    /**
     * =========================================================================
     * MODUL UMKM SAYA (CRUD & MANAJEMEN UMKM OLEH WARGA)
     * =========================================================================
     */

    /**
     * GET /api/umkm/my & GET /api/warga/umkm
     */
    public function getMyUmkm(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $umkm = UMKM::where('user_id', $user->id)->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $umkm,
            'total' => $umkm->count(),
        ]);
    }

    /**
     * POST /api/umkm & POST /api/warga/umkm
     */
    public function storeUmkm(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'tahun_berdiri' => 'nullable|integer|min:1900|max:' . date('Y'),
            'jam_operasional' => 'nullable|string|max:100',
            'whatsapp' => 'required|string|max:30',
            'instagram' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|string|max:500',
            'foto' => 'nullable|image|max:5120',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('uploads/umkm', 'public');
        }

        $umkm = UMKM::create([
            'user_id' => $user->id,
            'nama_usaha' => $validated['nama_usaha'],
            'pemilik' => $validated['pemilik'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'tahun_berdiri' => $validated['tahun_berdiri'] ?? date('Y'),
            'jam_operasional' => $validated['jam_operasional'] ?? '08:00 - 17:00 WIB',
            'whatsapp' => $validated['whatsapp'],
            'instagram' => $validated['instagram'] ?? null,
            'google_maps_url' => $validated['google_maps_url'] ?? null,
            'foto' => $fotoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'UMKM berhasil didaftarkan dan tersimpan di database!',
            'data' => $umkm,
        ], 201);
    }

    /**
     * GET /api/umkm/{id}
     */
    public function showUmkm(Request $request, int $id): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        $umkm = UMKM::find($id);

        if (!$umkm) {
            return response()->json(['success' => false, 'message' => 'Data UMKM tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $umkm,
        ]);
    }

    /**
     * PUT /api/umkm/{id}
     */
    public function updateUmkm(Request $request, int $id): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $umkm = UMKM::find($id);
        if (!$umkm) {
            return response()->json(['success' => false, 'message' => 'UMKM tidak ditemukan.'], 404);
        }

        if ($umkm->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'tahun_berdiri' => 'nullable|integer',
            'jam_operasional' => 'nullable|string|max:100',
            'whatsapp' => 'required|string|max:30',
            'instagram' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|string|max:500',
        ]);

        $umkm->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data UMKM berhasil diperbarui!',
            'data' => $umkm,
        ]);
    }

    /**
     * DELETE /api/umkm/{id}
     */
    public function deleteUmkm(Request $request, int $id): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $umkm = UMKM::find($id);
        if (!$umkm) {
            return response()->json(['success' => false, 'message' => 'UMKM tidak ditemukan.'], 404);
        }

        if ($umkm->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $umkm->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data UMKM berhasil dihapus.',
        ]);
    }
}
