<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BerkasPersyaratan;
use App\Models\Pengaturan;
use App\Models\SuratPermohonan;
use App\Models\User;
use App\Services\AiSuratService;
use App\Services\GoogleDriveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAdminController extends Controller
{
    protected AiSuratService $aiService;
    protected GoogleDriveService $gdriveService;

    public function __construct(AiSuratService $aiService, GoogleDriveService $gdriveService)
    {
        $this->aiService = $aiService;
        $this->gdriveService = $gdriveService;
    }

    /**
     * GET /api/admin/dashboard
     */
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'stats' => [
                'surat_menunggu' => SuratPermohonan::where('status', 'menunggu_admin')->count(),
                'surat_wali' => SuratPermohonan::where('status', 'menunggu_wali_nagari')->count(),
                'surat_disetujui' => SuratPermohonan::where('status', 'disetujui')->count(),
                'surat_ditolak' => SuratPermohonan::where('status', 'ditolak')->count(),
                'total_surat' => SuratPermohonan::count(),
                'total_warga' => User::where('role', 'warga')->count(),
                'total_kaba' => \App\Models\KabaNagari::count(),
                'total_umkm' => \App\Models\UMKM::count(),
                'total_wisata' => \App\Models\ObjekWisata::count(),
                'total_perangkat' => \App\Models\PerangkatNagari::count(),
                'gdrive_status' => $this->gdriveService->isConnected(),
            ],
            'surat_terbaru' => SuratPermohonan::with('user')->latest()->take(6)->get(),
        ]);
    }

    /**
     * GET /api/admin/arsip
     */
    public function arsipList(Request $request): JsonResponse
    {
        $query = SuratPermohonan::whereIn('status', ['disetujui', 'ditolak'])->with(['user', 'berkasPersyaratans'])->latest();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nik_pemohon', 'like', "%{$search}%")
                  ->orWhere('nomor_surat', 'like', "%{$search}%");
            });
        }

        $list = $query->get();

        return response()->json([
            'success' => true,
            'data' => $list,
            'total' => $list->count(),
        ]);
    }

    /**
     * GET /api/admin/surat
     */
    public function suratList(Request $request): JsonResponse
    {
        $query = SuratPermohonan::with(['user', 'berkasPersyaratans'])->latest();

        if ($request->filled('status') && $request->query('status') !== 'semua') {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nik_pemohon', 'like', "%{$search}%")
                  ->orWhere('nomor_surat', 'like', "%{$search}%");
            });
        }

        $suratList = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $suratList->items(),
            'current_page' => $suratList->currentPage(),
            'last_page' => $suratList->lastPage(),
            'total' => $suratList->total(),
        ]);
    }

    /**
     * GET /api/admin/surat/{id}
     */
    public function suratDetail(int $id): JsonResponse
    {
        $surat = SuratPermohonan::with(['user', 'berkasPersyaratans'])->find($id);

        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $surat,
            'berkas' => $surat->berkasPersyaratans->map(function ($b) {
                return [
                    'id' => $b->id,
                    'jenis_berkas' => $b->jenis_berkas,
                    'label_jenis' => $b->label_jenis,
                    'nama_file_asli' => $b->nama_file_asli,
                    'formatted_size' => $b->formatted_size,
                    'is_stored_in_gdrive' => !empty($b->gdrive_file_id),
                    'download_url' => route('warga.berkas.download', $b->id),
                ];
            }),
        ]);
    }

    /**
     * POST /api/admin/surat/{id}/verify
     */
    public function verifySurat(Request $request, int $id): JsonResponse
    {
        $surat = SuratPermohonan::find($id);
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        if (!in_array($surat->status, ['menunggu_admin', 'dikembalikan_ke_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Surat dengan status ini tidak dapat diverifikasi/diteruskan ke Meja Wali Nagari.'
            ], 422);
        }

        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'isi_surat' => 'required|string',
            'catatan_admin' => 'nullable|string',
        ]);

        $surat->nomor_surat = $validated['nomor_surat'];
        $surat->isi_surat = $validated['isi_surat'];
        $surat->catatan_admin = $validated['catatan_admin'] ?? null;
        $surat->status = 'menunggu_wali_nagari';
        $surat->save();

        return response()->json([
            'success' => true,
            'message' => "Surat berhasil diverifikasi & diteruskan ke Meja Wali Nagari.",
            'data' => $surat,
        ]);
    }

    /**
     * POST /api/admin/surat/{id}/minta-revisi
     */
    public function mintaRevisi(Request $request, int $id): JsonResponse
    {
        $surat = SuratPermohonan::find($id);
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        if (!in_array($surat->status, ['menunggu_admin', 'dikembalikan_ke_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan revisi hanya dapat dilakukan untuk surat yang berstatus menunggu verifikasi Admin atau dikembalikan oleh Wali Nagari.'
            ], 422);
        }

        $validated = $request->validate([
            'catatan_admin' => 'required|string|min:5|max:1000',
        ], [
            'catatan_admin.required' => 'Catatan revisi untuk warga wajib diisi.',
            'catatan_admin.min' => 'Catatan revisi minimal 5 karakter.',
            'catatan_admin.max' => 'Catatan revisi maksimal 1000 karakter.',
        ]);

        $surat->status = 'perlu_revisi_warga';
        $surat->catatan_admin = $validated['catatan_admin'];
        $surat->save();

        return response()->json([
            'success' => true,
            'message' => 'Permintaan perbaikan berkas/data berhasil dikirim ke warga.',
            'data' => $surat,
        ]);
    }

    /**
     * POST /api/admin/surat/{id}/reject
     */
    public function rejectSurat(Request $request, int $id): JsonResponse
    {
        $surat = SuratPermohonan::find($id);
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        if (!in_array($surat->status, ['menunggu_admin', 'dikembalikan_ke_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Surat dengan status ini tidak dapat ditolak oleh Admin.'
            ], 422);
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|min:5|max:500',
        ]);

        $surat->status = 'ditolak';
        $surat->catatan_admin = $validated['alasan_penolakan'];
        $surat->save();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat ditolak dan alasan telah disampaikan ke warga.',
            'data' => $surat,
        ]);
    }

    /**
     * POST /api/admin/surat/{id}/ai-generate
     */
    public function generateAi(Request $request, int $id): JsonResponse
    {
        $surat = SuratPermohonan::find($id);
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        $instruksi = $request->input('instruksi', 'Perbaiki tata bahasa dinas baku');
        $generatedText = $this->aiService->refineDraft($surat->isi_surat, $instruksi);

        return response()->json([
            'success' => true,
            'naskah' => $generatedText,
        ]);
    }

    /**
     * GET /api/admin/warga
     */
    public function wargaList(Request $request): JsonResponse
    {
        $query = User::where('role', 'warga')->withCount('suratPermohonans')->latest();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $wargaList = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $wargaList->items(),
            'total' => $wargaList->total(),
        ]);
    }

    /**
     * GET /api/admin/gdrive
     */
    public function gdriveStatus(): JsonResponse
    {
        $redirectUri = route('admin.gdrive.callback');
        $isConnected = $this->gdriveService->isConnected();
        $authUrl = !$isConnected ? $this->gdriveService->getAuthUrl($redirectUri) : null;

        return response()->json([
            'success' => true,
            'is_connected' => $isConnected,
            'auth_url' => $authUrl,
            'client_id' => Pengaturan::get('gdrive_client_id', '') ?: config('services.google.client_id', env('GOOGLE_CLIENT_ID', '')),
            'email' => Pengaturan::get('gdrive_account_email', ''),
            'folder_url' => config('services.google.gdrive_arsip_url', 'https://drive.google.com/'),
            'redirect_uri' => $redirectUri,
            'total_berkas_cloud' => BerkasPersyaratan::whereNotNull('gdrive_file_id')->count(),
            'total_berkas_lokal' => BerkasPersyaratan::whereNull('gdrive_file_id')->count(),
        ]);
    }

    /**
     * POST /api/admin/gdrive/disconnect
     */
    public function gdriveDisconnect(): JsonResponse
    {
        $this->gdriveService->disconnect();

        return response()->json([
            'success' => true,
            'message' => 'Koneksi Google Drive berhasil diputuskan.',
        ]);
    }

    /**
     * GET /api/admin/pengaturan
     */
    public function getPengaturan(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'nama_kantor' => Pengaturan::get('nama_kantor', 'Kantor Wali Nagari Koto Malintang'),
                'alamat_kantor' => Pengaturan::get('alamat_kantor', 'Jl. Raya Maninjau - Lubuk Basung, Koto Malintang, Kec. Tanjung Raya, Kab. Agam'),
                'telepon_kantor' => Pengaturan::get('telepon_kantor', '0812-3456-7890'),
                'email_kantor' => Pengaturan::get('email_kantor', 'layanan@nagari-kotomalintang.desa.id'),
                'jam_layanan' => Pengaturan::get('jam_layanan', 'Senin - Kamis: 08.00 - 16.00 WIB | Jumat: 08.00 - 11.30 WIB'),
                'nama_wali' => Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
                'nama_wali_nagari' => Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
                'gelar_wali' => Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
                'gelar_wali_nagari' => Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
                'jabatan_wali' => Pengaturan::get('jabatan_wali_nagari', 'Wali Nagari Koto Malintang'),
                'jabatan_wali_nagari' => Pengaturan::get('jabatan_wali_nagari', 'Wali Nagari Koto Malintang'),
            ],
        ]);
    }

    /**
     * PUT /api/admin/pengaturan
     */
    public function updatePengaturan(Request $request): JsonResponse
    {
        $fields = [
            'nama_kantor', 'alamat_kantor', 'telepon_kantor', 'email_kantor',
            'jam_layanan', 'nama_wali', 'nama_wali_nagari', 'gelar_wali', 'gelar_wali_nagari',
            'jabatan_wali', 'jabatan_wali_nagari',
        ];

        $userSyncData = [];

        foreach ($fields as $f) {
            if ($request->filled($f)) {
                $val = trim($request->input($f));
                if (in_array($f, ['nama_wali', 'nama_wali_nagari'])) {
                    $dbKey = 'nama_wali_nagari';
                    $userSyncData['name'] = $val;
                } elseif (in_array($f, ['gelar_wali', 'gelar_wali_nagari'])) {
                    $dbKey = 'gelar_wali_nagari';
                    $userSyncData['gelar'] = $val;
                } elseif (in_array($f, ['jabatan_wali', 'jabatan_wali_nagari'])) {
                    $dbKey = 'jabatan_wali_nagari';
                    $userSyncData['jabatan'] = $val;
                } else {
                    $dbKey = $f;
                }
                Pengaturan::set($dbKey, $val, 'umum');
            }
        }

        // Sync with Wali Nagari user model if any identity fields were updated
        if (!empty($userSyncData)) {
            User::where('role', 'wali_nagari')->update($userSyncData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan kantor dan identitas Wali Nagari berhasil diperbarui di database.',
        ]);
    }
}
