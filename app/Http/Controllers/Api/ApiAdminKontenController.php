<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdatBudayaNagari;
use App\Models\FotoKabaNagari;
use App\Models\KabaNagari;
use App\Models\ObjekWisata;
use App\Models\PerangkatNagari;
use App\Models\ProfilNagari;
use App\Models\SejarahNagari;
use App\Models\StrukturNagari;
use App\Models\UMKM;
use App\Models\Visi;
use App\Models\Misi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiAdminKontenController extends Controller
{
    /**
     * GET /api/admin/konten
     * Mengambil ringkasan konten publik nagari
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'profil' => ProfilNagari::first(),
                'adat_budaya' => AdatBudayaNagari::first(),
                'visi' => Visi::first(),
                'misi' => Misi::get(),
                'sejarah_count' => SejarahNagari::count(),
                'perangkat_count' => PerangkatNagari::count(),
                'struktur_count' => StrukturNagari::count(),
                'wisata_count' => ObjekWisata::count(),
                'umkm_count' => UMKM::count(),
                'kaba_count' => KabaNagari::count(),
            ],
        ]);
    }

    /**
     * GET /api/admin/konten/profil
     */
    public function getProfil(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ProfilNagari::first(),
        ]);
    }

    /**
     * PUT /api/admin/konten/profil
     */
    public function updateProfil(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_nagari' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'jumlah_penduduk' => 'nullable|integer',
            'jumlah_jorong' => 'nullable|integer',
            'luas_wilayah' => 'nullable|numeric',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'email' => 'nullable|string',
            'telepon' => 'nullable|string',
            'foto_utama' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $profil = ProfilNagari::firstOrNew(['id' => 1]);
        $profil->fill($validated);

        if ($request->hasFile('foto_utama')) {
            $path = $request->file('foto_utama')->store('uploads/profil', 'public');
            $profil->foto_utama = 'storage/' . $path;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads/profil', 'public');
            $profil->logo = 'storage/' . $path;
        }

        $profil->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil Nagari berhasil diperbarui di database MySQL.',
            'data' => $profil,
        ]);
    }

    /**
     * GET /api/admin/kaba-nagari
     */
    public function kabaList(Request $request): JsonResponse
    {
        $query = KabaNagari::with('gambarKabaNagaris')->latest();

        if ($request->filled('kategori') && $request->query('kategori') !== 'semua') {
            $query->where('kategori', $request->query('kategori'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
            'total' => $query->count(),
        ]);
    }

    /**
     * POST /api/admin/kaba-nagari
     */
    public function kabaStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'isi' => 'required|string',
            'tanggal' => 'nullable|string',
            'ringkasan' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if (empty($validated['tanggal'])) {
            $validated['tanggal'] = date('Y-m-d');
        }

        if (empty($validated['ringkasan'])) {
            $validated['ringkasan'] = mb_substr(strip_tags($validated['isi']), 0, 150) . '...';
        }

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = 'storage/' . $request->file('gambar')->store('uploads/kaba', 'public');
        }

        $kaba = KabaNagari::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diterbitkan ke database.',
            'data' => $kaba->load('gambarKabaNagaris'),
        ], 201);
    }

    /**
     * GET /api/admin/kaba-nagari/{id}
     */
    public function kabaShow(int $id): JsonResponse
    {
        $kaba = KabaNagari::with('gambarKabaNagaris')->find($id);
        if (!$kaba) {
            return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kaba,
        ]);
    }

    /**
     * PUT /api/admin/kaba-nagari/{id}
     */
    public function kabaUpdate(Request $request, int $id): JsonResponse
    {
        $kaba = KabaNagari::find($id);
        if (!$kaba) {
            return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'isi' => 'required|string',
            'tanggal' => 'nullable|string',
            'ringkasan' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if (empty($validated['ringkasan'])) {
            $validated['ringkasan'] = mb_substr(strip_tags($validated['isi']), 0, 150) . '...';
        }

        if ($request->hasFile('gambar')) {
            if ($kaba->gambar && str_starts_with($kaba->gambar, 'storage/')) Storage::disk('public')->delete(substr($kaba->gambar, 8));
            $validated['gambar'] = 'storage/' . $request->file('gambar')->store('uploads/kaba', 'public');
        } else {
            unset($validated['gambar']);
        }

        $kaba->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diperbarui di database.',
            'data' => $kaba->fresh()->load('gambarKabaNagaris'),
        ]);
    }

    /**
     * DELETE /api/admin/kaba-nagari/{id}
     */
    public function kabaDestroy(int $id): JsonResponse
    {
        $kaba = KabaNagari::with('gambarKabaNagaris')->find($id);
        if (!$kaba) {
            return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
        }

        foreach ($kaba->gambarKabaNagaris as $gambar) {
            if (str_starts_with($gambar->gambar, 'storage/')) Storage::disk('public')->delete(substr($gambar->gambar, 8));
        }
        if ($kaba->gambar && str_starts_with($kaba->gambar, 'storage/')) Storage::disk('public')->delete(substr($kaba->gambar, 8));
        $kaba->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dihapus dari database.',
        ]);
    }

    public function kabaGambarList(int $id): JsonResponse
    {
        $kaba = KabaNagari::find($id);
        if (!$kaba) return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
        return response()->json(['success' => true, 'data' => $kaba->gambarKabaNagaris]);
    }

    public function kabaGambarStore(Request $request, int $id): JsonResponse
    {
        $kaba = KabaNagari::find($id);
        if (!$kaba) return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
        $request->validate(['gambar' => 'required|array|min:1', 'gambar.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096']);
        $foto = collect($request->file('gambar'))->map(fn ($file) => FotoKabaNagari::create([
            'kaba_nagari_id' => $kaba->id,
            'gambar' => 'storage/' . $file->store('uploads/kaba/galeri', 'public'),
        ]));
        return response()->json(['success' => true, 'message' => 'Foto berita berhasil diunggah.', 'data' => $foto], 201);
    }

    public function kabaGambarDestroy(int $id, int $gambarId): JsonResponse
    {
        $foto = FotoKabaNagari::where('kaba_nagari_id', $id)->find($gambarId);
        if (!$foto) return response()->json(['success' => false, 'message' => 'Foto berita tidak ditemukan.'], 404);
        if (str_starts_with($foto->gambar, 'storage/')) Storage::disk('public')->delete(substr($foto->gambar, 8));
        $foto->delete();
        return response()->json(['success' => true, 'message' => 'Foto berita berhasil dihapus.']);
    }
}
