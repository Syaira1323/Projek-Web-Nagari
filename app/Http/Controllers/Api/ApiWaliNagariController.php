<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\SuratPermohonan;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApiWaliNagariController extends Controller
{
    /**
     * Helper to retrieve currently authenticated Wali Nagari
     */
    protected function getAuthenticatedWali(Request $request): ?User
    {
        $user = Auth::user();
        if (!$user) {
            $authHeader = $request->header('Authorization');
            if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
                $token = substr($authHeader, 7);
                $decoded = base64_decode($token);
                $parts = explode(':', $decoded);
                if (isset($parts[0]) && is_numeric($parts[0])) {
                    $user = User::find($parts[0]);
                }
            }
        }

        if (!$user) {
            $user = User::where('role', 'wali_nagari')->first();
        }

        return $user;
    }

    /**
     * GET /api/wali-nagari/profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedWali($request);
        $ttdPath = $user?->ttd_path ?: Pengaturan::get('tanda_tangan_wali_path');
        $stempelPath = Pengaturan::get('stempel_nagari_path', 'image/stempel_resmi.svg');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user?->id,
                'name' => $user?->name ?? Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
                'username' => $user?->username ?? 'walinagari',
                'role' => $user?->role ?? 'wali_nagari',
                'jabatan' => $user?->jabatan ?? 'Wali Nagari Koto Malintang',
                'gelar' => $user?->gelar ?? Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
                'ttd_path' => $ttdPath,
                'ttd_url' => $ttdPath ? asset($ttdPath) : null,
            ],
            'has_ttd' => !empty($ttdPath),
            'stempel_path' => $stempelPath,
            'stempel_url' => asset($stempelPath),
        ]);
    }

    /**
     * GET /api/wali-nagari/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedWali($request);
        $ttdPath = $user?->ttd_path ?: Pengaturan::get('tanda_tangan_wali_path');

        $antrean = SuratPermohonan::where('status', 'menunggu_wali_nagari')->count();
        $disetujui = SuratPermohonan::where('status', 'disetujui')->count();
        $ditolak = SuratPermohonan::where('status', 'ditolak')->count();

        $suratAntrean = SuratPermohonan::with(['user', 'berkasPersyaratans'])
            ->where('status', 'menunggu_wali_nagari')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => [
                'antrean' => $antrean,
                'disetujui' => $disetujui,
                'ditolak' => $ditolak,
                'total' => SuratPermohonan::count(),
            ],
            'wali' => [
                'nama' => $user?->name ?? Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
                'gelar' => $user?->gelar ?? Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
                'jabatan' => $user?->jabatan ?? 'Wali Nagari Koto Malintang',
                'ttd_url' => $ttdPath ? asset($ttdPath) : null,
                'has_ttd' => !empty($ttdPath),
            ],
            'ttd_configured' => !empty($ttdPath),
            'stempel_configured' => true,
            'surat_antrean' => $suratAntrean,
        ]);
    }

    /**
     * GET /api/wali-nagari/surat
     */
    public function suratList(Request $request): JsonResponse
    {
        $status = $request->query('status', 'menunggu_wali_nagari');
        $query = SuratPermohonan::with(['user', 'berkasPersyaratans'])->latest();

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        $suratList = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $suratList->items(),
            'current_page' => $suratList->currentPage(),
            'last_page' => $suratList->lastPage(),
            'total' => $suratList->total(),
            'counts' => [
                'antrean' => SuratPermohonan::where('status', 'menunggu_wali_nagari')->count(),
                'disetujui' => SuratPermohonan::where('status', 'disetujui')->count(),
                'ditolak' => SuratPermohonan::where('status', 'ditolak')->count(),
                'total' => SuratPermohonan::count(),
            ],
        ]);
    }

    /**
     * GET /api/wali-nagari/surat/{id}
     */
    public function suratDetail(Request $request, int $id): JsonResponse
    {
        $surat = SuratPermohonan::with(['user', 'berkasPersyaratans', 'signedByUser'])->find($id);
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        $wali = $this->getAuthenticatedWali($request);
        $ttdPath = $surat->ttd_wali_path ?: ($wali?->ttd_path ?: Pengaturan::get('tanda_tangan_wali_path'));
        $stempelPath = Pengaturan::get('stempel_nagari_path', 'image/stempel_resmi.svg');

        return response()->json([
            'success' => true,
            'data' => $surat,
            'has_ttd' => !empty($ttdPath),
            'ttd_path' => $ttdPath,
            'ttd_url' => $ttdPath ? asset($ttdPath) : null,
            'stempel_path' => $stempelPath,
            'stempel_url' => asset($stempelPath),
            'nama_wali' => $wali?->name ?? Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
            'gelar_wali' => $wali?->gelar ?? Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
            'jabatan_wali' => $wali?->jabatan ?? 'Wali Nagari Koto Malintang',
            'berkas' => $surat->berkasPersyaratans->map(function ($b) {
                return [
                    'id' => $b->id,
                    'jenis_berkas' => $b->jenis_berkas,
                    'label_jenis' => $b->label_jenis,
                    'nama_file_asli' => $b->nama_file_asli,
                    'formatted_size' => $b->formatted_size,
                    'is_stored_in_gdrive' => $b->is_stored_in_gdrive,
                ];
            }),
        ]);
    }

    /**
     * GET /api/wali-nagari/ttd-stempel
     */
    public function getTtdStempel(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedWali($request);
        $ttdPath = $user?->ttd_path ?: Pengaturan::get('tanda_tangan_wali_path');
        $stempelPath = Pengaturan::get('stempel_nagari_path', 'image/stempel_resmi.svg');

        return response()->json([
            'success' => true,
            'nama_wali' => $user?->name ?? Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
            'gelar_wali' => $user?->gelar ?? Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
            'jabatan' => $user?->jabatan ?? 'Wali Nagari Koto Malintang',
            'ttd_path' => $ttdPath,
            'ttd_url' => $ttdPath ? asset($ttdPath) : null,
            'has_ttd' => !empty($ttdPath),
            'stempel_path' => $stempelPath,
            'stempel_url' => asset($stempelPath),
        ]);
    }

    /**
     * PUT/POST /api/wali-nagari/profile
     * Wali Nagari is forbidden from changing their name or position directly.
     */
    public function forbiddenProfileUpdate(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Wali Nagari tidak memiliki izin untuk mengubah nama atau jabatan. Identitas Wali Nagari hanya dapat diubah oleh Administrator.',
        ], 403);
    }

    /**
     * POST /api/wali-nagari/tanda-tangan
     */
    public function uploadTtd(Request $request): JsonResponse
    {
        $request->validate([
            'tanda_tangan' => 'required|file|image|mimes:png,jpg,jpeg,webp|max:3072',
        ], [
            'tanda_tangan.required' => 'File gambar tanda tangan wajib diunggah.',
            'tanda_tangan.image' => 'File harus berupa gambar (PNG/JPG/WEBP).',
            'tanda_tangan.mimes' => 'Format file yang diperbolehkan adalah PNG, JPG, JPEG, atau WEBP.',
            'tanda_tangan.max' => 'Ukuran file tanda tangan maksimal 3 MB.',
        ]);

        $user = $this->getAuthenticatedWali($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User Wali Nagari tidak ditemukan.'], 401);
        }

        $file = $request->file('tanda_tangan');
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'ttd_wali_' . $user->id . '_' . time() . '.' . $extension;

        // Ensure storage folder exists
        Storage::disk('public')->makeDirectory('ttd/wali-nagari');

        // Store file safely in storage/app/public/ttd/wali-nagari/
        $storedPath = $file->storeAs('ttd/wali-nagari', $filename, 'public');
        $publicPath = 'storage/' . $storedPath;

        // Delete old TTD file if exists and different
        if ($user->ttd_path && str_starts_with($user->ttd_path, 'storage/ttd/wali-nagari/')) {
            $oldRelPath = str_replace('storage/', '', $user->ttd_path);
            if (Storage::disk('public')->exists($oldRelPath)) {
                Storage::disk('public')->delete($oldRelPath);
            }
        }

        // Save to user record (TTD only)
        $user->ttd_path = $publicPath;
        $user->save();

        // Update global fallback in Pengaturan
        Pengaturan::set('tanda_tangan_wali_path', $publicPath, 'ttd_stempel', 'file', 'Path Tanda Tangan Wali Nagari');

        return response()->json([
            'success' => true,
            'message' => 'Tanda Tangan Wali Nagari berhasil diunggah dan disimpan di database & storage!',
            'ttd_path' => $publicPath,
            'ttd_url' => asset($publicPath),
            'nama_wali' => $user->name ?? Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
            'gelar_wali' => $user->gelar ?? Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
            'jabatan' => $user->jabatan ?? Pengaturan::get('jabatan_wali_nagari', 'Wali Nagari Koto Malintang'),
        ]);
    }

    /**
     * DELETE /api/wali-nagari/tanda-tangan
     */
    public function deleteTtd(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedWali($request);
        if ($user && $user->ttd_path) {
            $oldRelPath = str_replace('storage/', '', $user->ttd_path);
            if (Storage::disk('public')->exists($oldRelPath)) {
                Storage::disk('public')->delete($oldRelPath);
            }
            $user->ttd_path = null;
            $user->save();
        }

        Pengaturan::set('tanda_tangan_wali_path', '', 'ttd_stempel');

        return response()->json([
            'success' => true,
            'message' => 'Tanda tangan berhasil dihapus dari sistem.',
        ]);
    }

    /**
     * POST /api/wali-nagari/stempel
     */
    public function uploadStempel(Request $request): JsonResponse
    {
        $request->validate([
            'stempel' => 'required|file|image|mimes:png,jpg,jpeg,webp,svg|max:3072',
        ], [
            'stempel.required' => 'File stempel resmi wajib diunggah.',
            'stempel.image' => 'File harus berupa gambar.',
        ]);

        $file = $request->file('stempel');
        $ext = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'stempel_nagari_' . time() . '.' . $ext;

        Storage::disk('public')->makeDirectory('stempel');
        $storedPath = $file->storeAs('stempel', $filename, 'public');
        $publicPath = 'storage/' . $storedPath;

        Pengaturan::set('stempel_nagari_path', $publicPath, 'ttd_stempel', 'file', 'Path Stempel Resmi Nagari');

        return response()->json([
            'success' => true,
            'message' => 'Stempel resmi Nagari berhasil disimpan di Storage & MySQL.',
            'path' => $publicPath,
            'stempel_url' => asset($publicPath),
        ]);
    }

    /**
     * DELETE /api/wali-nagari/stempel
     */
    public function deleteStempel(): JsonResponse
    {
        Pengaturan::set('stempel_nagari_path', '', 'ttd_stempel');
        return response()->json([
            'success' => true,
            'message' => 'Stempel berhasil dihapus.',
        ]);
    }

    /**
     * POST /api/wali-nagari/surat/{id}/approve
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $surat = SuratPermohonan::find($id);

        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        if ($surat->status !== 'menunggu_wali_nagari') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya surat dengan status menunggu persetujuan Wali Nagari yang dapat disetujui/ditandatangani.'
            ], 422);
        }

        $user = $this->getAuthenticatedWali($request);
        $ttdPath = $user?->ttd_path ?: Pengaturan::get('tanda_tangan_wali_path');

        // Validation: Wali Nagari must have an uploaded signature
        if (empty($ttdPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanda tangan Wali Nagari belum tersedia. Silakan upload tanda tangan terlebih dahulu pada menu Pengaturan Tanda Tangan sebelum menandatangani surat.',
                'needs_ttd' => true,
            ], 422);
        }

        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $bulanIni = $bulanRomawi[(int)date('n')];
        $tahunIni = date('Y');

        if (!$surat->nomor_surat) {
            $nomorUrut = str_pad($surat->id, 3, '0', STR_PAD_LEFT);
            $surat->nomor_surat = "470 / {$nomorUrut} / WN-KM / {$bulanIni} / {$tahunIni}";
        }

        $surat->status = 'disetujui';
        $surat->stempel_digital = true;
        $surat->disetujui_wali_at = now();
        $surat->ttd_wali_path = $ttdPath;
        $surat->signed_by_user_id = $user?->id;
        $surat->catatan_wali = $request->input('catatan_wali', 'Disetujui dan dibubuhi Tanda Tangan & Stempel Resmi Wali Nagari.');

        // Snapshot identitas penandatangan aktif ke data_spesifik surat agar historis surat tersimpan permanen
        $ds = $surat->data_spesifik ?? [];
        if (!is_array($ds)) {
            $ds = json_decode((string) $ds, true) ?? [];
        }
        $namaWaliAktif = Pengaturan::get('nama_wali_nagari', $user?->name ?? 'NAZARUDDIN, S.Pd.I');
        $jabatanWaliAktif = Pengaturan::get('jabatan_wali_nagari', $user?->jabatan ?? 'WALI NAGARI KOTO MALINTANG');
        $ds['penandatangan_nama'] = $namaWaliAktif;
        $ds['penandatangan_jabatan'] = $jabatanWaliAktif;
        $surat->data_spesifik = $ds;

        $surat->save();

        // Trigger Arsip PDF Final ke Google Drive (Idempotent & Aman)
        try {
            $gdriveService = app(GoogleDriveService::class);
            $gdriveService->archiveSuratSelesai($surat);
        } catch (\Throwable $e) {
            Log::error("Gagal memicu arsip Google Drive pada approval Wali Nagari (Surat ID {$surat->id}): " . $e->getMessage(), [
                'surat_id' => $surat->id,
                'jenis_surat' => $surat->jenis_surat,
                'status' => $surat->status,
                'tahap' => 'approval_wali_nagari_api',
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Surat berhasil dibubuhi Stempel & TTD Digital Resmi! Nomor: {$surat->nomor_surat}",
            'data' => $surat,
            'ttd_url' => asset($ttdPath),
            'signed_at' => $surat->disetujui_wali_at->format('d-m-Y H:i:s'),
            'signer' => $user?->name ?? 'Wali Nagari',
        ]);
    }

    /**
     * POST /api/wali-nagari/surat/{id}/kembalikan
     */
    public function kembalikan(Request $request, int $id): JsonResponse
    {
        $surat = SuratPermohonan::find($id);

        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        if ($surat->status !== 'menunggu_wali_nagari') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya surat dengan status menunggu persetujuan Wali Nagari yang dapat dikembalikan ke Meja Admin.'
            ], 422);
        }

        $validated = $request->validate([
            'catatan_wali' => 'required|string|min:5|max:1000',
        ], [
            'catatan_wali.required' => 'Catatan koreksi/pengembalian naskah wajib diisi.',
            'catatan_wali.min' => 'Catatan koreksi minimal 5 karakter.',
            'catatan_wali.max' => 'Catatan koreksi maksimal 1000 karakter.',
        ]);

        $surat->status = 'dikembalikan_ke_admin';
        $surat->catatan_wali = $validated['catatan_wali'];
        $surat->save();

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil dikembalikan ke Meja Admin Nagari beserta catatan perbaikan.',
            'data' => $surat,
        ]);
    }

    /**
     * POST /api/wali-nagari/surat/{id}/reject
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $surat = SuratPermohonan::find($id);

        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan.'], 404);
        }

        if ($surat->status !== 'menunggu_wali_nagari') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya surat dengan status menunggu persetujuan Wali Nagari yang dapat ditolak oleh Wali Nagari.'
            ], 422);
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|min:5|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan surat wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $surat->status = 'ditolak';
        $surat->catatan_wali = $validated['alasan_penolakan'];
        $surat->save();

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil ditolak oleh Wali Nagari dan catatan dikirim ke pemohon.',
            'data' => $surat,
        ]);
    }
}