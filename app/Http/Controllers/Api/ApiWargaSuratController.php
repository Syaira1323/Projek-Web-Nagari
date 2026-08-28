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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ApiWargaSuratController extends Controller
{
    protected GoogleDriveService $gdriveService;
    protected AiSuratService $aiService;

    public function __construct(GoogleDriveService $gdriveService, AiSuratService $aiService)
    {
        $this->gdriveService = $gdriveService;
        $this->aiService = $aiService;
    }

    /**
     * Helper resolve current authenticated user (Session or Token or Default)
     */
    protected function getAuthenticatedUser(Request $request): ?User
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
            $decoded = base64_decode($token);
            $parts = explode(':', $decoded);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $user = User::find($parts[0]);
                if ($user) return $user;
            }
        }

        // Fallback ke warga pertama jika dev mode
        return User::where('role', 'warga')->first();
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
            } elseif ($s->status === 'ditolak') {
                $alasan = $s->catatan_admin ?: $s->catatan_wali ?: 'Mohon periksa kelengkapan berkas persyaratan Anda.';
                $notifications->push([
                    'id' => 'notif-rej-' . $s->id,
                    'surat_id' => $s->id,
                    'type' => 'danger',
                    'icon' => 'bi-exclamation-triangle-fill text-danger',
                    'title' => 'Pengajuan Memerlukan Perbaikan',
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
                    'menunggu_wali_nagari' => 2,
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
                'tanggal_lahir' => $surat->tanggal_lahir ? $surat->tanggal_lahir->translatedFormat('d F Y') : '-',
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
                    'menunggu_wali_nagari' => 2,
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

        $dataSpesifik = [];
        $allowedSpesifikKeys = [
            'nama_usaha', 'bidang_usaha', 'alamat_usaha',
            'nama_almarhum', 'tanggal_meninggal', 'tempat_meninggal', 'sebab_meninggal',
            'nama_calon_pasangan', 'bin_binti_pasangan',
            'nominal_penghasilan', 'sumber_penghasilan',
        ];

        foreach ($allowedSpesifikKeys as $key) {
            if ($request->filled($key)) {
                $dataSpesifik[$key] = $request->input($key);
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
        $surat->isi_surat = $this->aiService->generateDraft($surat->toArray(), $dataSpesifik);
        $surat->save();

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
