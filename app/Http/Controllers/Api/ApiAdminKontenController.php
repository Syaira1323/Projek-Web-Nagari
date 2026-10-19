<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdatBudayaNagari;
use App\Models\FotoKabaNagari;
use App\Models\KabaNagari;
use App\Models\MaknaVisi;
use App\Models\Misi;
use App\Models\ObjekWisata;
use App\Models\PerangkatNagari;
use App\Models\ProfilNagari;
use App\Models\SejarahNagari;
use App\Models\StrukturNagari;
use App\Models\UMKM;
use App\Models\Visi;
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
                'misi_count' => Misi::count(),
                'sejarah_count' => SejarahNagari::count(),
                'perangkat_count' => PerangkatNagari::count(),
                'struktur_count' => StrukturNagari::count(),
                'wisata_count' => ObjekWisata::count(),
                'umkm_count' => UMKM::count(),
                'kaba_count' => KabaNagari::count(),
                'galeri_count' => FotoKabaNagari::count(),
            ],
        ]);
    }

    // ==========================================
    // 1. PROFIL NAGARI
    // ==========================================
    public function getProfil(): JsonResponse
    {
        $profil = ProfilNagari::firstOrCreate(['id' => 1], [
            'nama_nagari' => 'Nagari Koto Malintang',
            'kecamatan' => 'Tanjung Raya',
            'kabupaten' => 'Agam',
            'provinsi' => 'Sumatera Barat',
            'jumlah_penduduk' => 2500,
            'jumlah_jorong' => 8,
            'luas_wilayah' => 45,
            'deskripsi' => 'Nagari Koto Malintang adalah salah satu nagari yang terletak di Kecamatan Tanjung Raya, Kabupaten Agam...',
        ]);

        return response()->json([
            'success' => true,
            'data' => $profil,
        ]);
    }

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
            'kode_pos' => 'nullable|string',
            'email' => 'nullable|string',
            'telepon' => 'nullable|string',
            'website' => 'nullable|string',
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

    // ==========================================
    // 2. ADAT & BUDAYA NAGARI
    // ==========================================
    public function getAdat(): JsonResponse
    {
        $adat = AdatBudayaNagari::firstOrCreate(['id' => 1], [
            'uraian' => 'Masyarakat Nagari Koto Malintang menjunjung tinggi falsafah Adat Basandi Syarak, Syarak Basandi Kitabullah...',
            'jumlah_suku' => 7,
            'kegiatan_adat' => ['Wirid Kepayungan', 'Batagak Pangulu', 'Mangaji Tamaik', 'Pesta Budaya Danau Maninjau'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $adat,
        ]);
    }

    public function updateAdat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uraian' => 'required|string',
            'jumlah_suku' => 'required|integer|min:1',
            'kegiatan_adat_raw' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $adat = AdatBudayaNagari::firstOrNew(['id' => 1]);
        $adat->uraian = $validated['uraian'];
        $adat->jumlah_suku = $validated['jumlah_suku'];

        if ($request->filled('kegiatan_adat_raw')) {
            $kegiatanArray = array_filter(array_map('trim', explode("\n", $validated['kegiatan_adat_raw'])));
            $adat->kegiatan_adat = array_values($kegiatanArray);
        }

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/adat', 'public');
            $adat->gambar = 'storage/' . $path;
        }

        $adat->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Adat & Budaya berhasil diperbarui di database.',
            'data' => $adat,
        ]);
    }

    // ==========================================
    // 3. VISI & MISI
    // ==========================================
    public function getVisiMisi(): JsonResponse
    {
        $visi = Visi::with(['maknaVisis', 'misis'])->first();
        if (!$visi) {
            $visi = Visi::create([
                'judul' => 'VISI',
                'isi' => 'Terwujudnya Nagari Koto Malintang yang Maju, Mandiri, dan Sejahtera Berdasarkan Nilai-nilai Adat Basandi Syarak, Syarak Basandi Kitabullah'
            ]);
        }

        $misiList = Misi::where('visi_id', $visi->id)->orderBy('nomor')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'visi' => $visi,
                'makna_visis' => $visi->maknaVisis,
                'misi' => $misiList,
            ],
        ]);
    }

    public function updateVisiMisi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'isi' => 'required|string',
            'misi_raw' => 'required|string',
            'makna_raw' => 'nullable|string',
        ]);

        $visi = Visi::firstOrNew(['id' => 1]);
        $visi->judul = $validated['judul'] ?: 'VISI';
        $visi->isi = $validated['isi'];
        $visi->save();

        // Update Misi
        $lines = array_filter(array_map('trim', explode("\n", $validated['misi_raw'])));
        Misi::where('visi_id', $visi->id)->delete();

        foreach ($lines as $index => $line) {
            $parts = explode(':', $line, 2);
            $judul = trim($parts[0]);
            $deskripsi = isset($parts[1]) ? trim($parts[1]) : $judul;

            Misi::create([
                'visi_id' => $visi->id,
                'nomor' => $index + 1,
                'judul' => $judul,
                'deskripsi' => $deskripsi,
            ]);
        }

        // Update Makna Visi
        if ($request->filled('makna_raw')) {
            MaknaVisi::where('visi_id', $visi->id)->delete();
            $maknaLines = array_filter(array_map('trim', explode("\n", $validated['makna_raw'])));
            $defaultIcons = ['arrow-up', 'shield', 'heart', 'book'];

            foreach ($maknaLines as $mIndex => $mLine) {
                $mParts = explode(':', $mLine);
                $istilah = trim($mParts[0]);
                $deskripsi = isset($mParts[1]) ? trim($mParts[1]) : $istilah;
                $icon = isset($mParts[2]) ? trim($mParts[2]) : ($defaultIcons[$mIndex % 4] ?? 'arrow-up');

                MaknaVisi::create([
                    'visi_id' => $visi->id,
                    'istilah' => $istilah,
                    'deskripsi' => $deskripsi,
                    'icon' => $icon,
                    'urutan' => $mIndex + 1,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Visi & Misi berhasil diperbarui di database.',
            'data' => $visi->load(['maknaVisis', 'misis']),
        ]);
    }

    // ==========================================
    // 4. SEJARAH NAGARI
    // ==========================================
    public function getSejarah(): JsonResponse
    {
        $sejarahList = SejarahNagari::orderBy('tipe')->orderBy('urutan')->get();
        return response()->json([
            'success' => true,
            'data' => $sejarahList,
        ]);
    }

    public function storeSejarah(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipe' => 'required|string|in:singkat,periode',
            'judul' => 'required|string|max:255',
            'periode' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if (empty($validated['urutan'])) {
            $validated['urutan'] = (SejarahNagari::max('urutan') ?? 0) + 1;
        }

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/sejarah', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        $sejarah = SejarahNagari::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Timeline sejarah nagari berhasil ditambahkan.',
            'data' => $sejarah,
        ], 201);
    }

    public function updateSejarah(Request $request, int $id): JsonResponse
    {
        $sejarah = SejarahNagari::find($id);
        if (!$sejarah) return response()->json(['success' => false, 'message' => 'Data sejarah tidak ditemukan.'], 404);

        $validated = $request->validate([
            'tipe' => 'required|string|in:singkat,periode',
            'judul' => 'required|string|max:255',
            'periode' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            if ($sejarah->gambar && str_starts_with($sejarah->gambar, 'storage/')) Storage::disk('public')->delete(substr($sejarah->gambar, 8));
            $path = $request->file('gambar')->store('uploads/sejarah', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        $sejarah->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Timeline sejarah nagari berhasil diperbarui.',
            'data' => $sejarah,
        ]);
    }

    public function destroySejarah(int $id): JsonResponse
    {
        $sejarah = SejarahNagari::find($id);
        if (!$sejarah) return response()->json(['success' => false, 'message' => 'Data sejarah tidak ditemukan.'], 404);

        if ($sejarah->gambar && str_starts_with($sejarah->gambar, 'storage/')) Storage::disk('public')->delete(substr($sejarah->gambar, 8));
        $sejarah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data sejarah berhasil dihapus dari database.',
        ]);
    }

    // ==========================================
    // 5. PERANGKAT NAGARI
    // ==========================================
    public function getPerangkat(): JsonResponse
    {
        $perangkatList = PerangkatNagari::all();
        return response()->json([
            'success' => true,
            'data' => $perangkatList,
        ]);
    }

    public function storePerangkat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'pangkat_gol' => 'nullable|string|max:50',
            'nip' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $validated['tempat_lahir'] = $validated['tempat_lahir'] ?? '-';

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('uploads/perangkat', 'public');
            $validated['foto'] = 'storage/' . $path;
        }

        $perangkat = PerangkatNagari::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat nagari berhasil ditambahkan.',
            'data' => $perangkat,
        ], 201);
    }

    public function updatePerangkat(Request $request, int $id): JsonResponse
    {
        $perangkat = PerangkatNagari::find($id);
        if (!$perangkat) return response()->json(['success' => false, 'message' => 'Perangkat nagari tidak ditemukan.'], 404);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'pangkat_gol' => 'nullable|string|max:50',
            'nip' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('foto')) {
            if ($perangkat->foto && str_starts_with($perangkat->foto, 'storage/')) Storage::disk('public')->delete(substr($perangkat->foto, 8));
            $path = $request->file('foto')->store('uploads/perangkat', 'public');
            $validated['foto'] = 'storage/' . $path;
        }

        $perangkat->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat nagari berhasil diperbarui.',
            'data' => $perangkat,
        ]);
    }

    public function destroyPerangkat(int $id): JsonResponse
    {
        $perangkat = PerangkatNagari::find($id);
        if (!$perangkat) return response()->json(['success' => false, 'message' => 'Perangkat nagari tidak ditemukan.'], 404);

        if ($perangkat->foto && str_starts_with($perangkat->foto, 'storage/')) Storage::disk('public')->delete(substr($perangkat->foto, 8));
        $perangkat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perangkat nagari berhasil dihapus dari database.',
        ]);
    }

    // ==========================================
    // 6. STRUKTUR NAGARI
    // ==========================================
    public function getStruktur(): JsonResponse
    {
        $strukturList = StrukturNagari::all();
        return response()->json([
            'success' => true,
            'data' => $strukturList,
        ]);
    }

    public function storeStruktur(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'foto_bagan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('foto_bagan')) {
            $path = $request->file('foto_bagan')->store('uploads/struktur', 'public');
            $validated['foto_bagan'] = 'storage/' . $path;
        }

        $struktur = StrukturNagari::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil ditambahkan.',
            'data' => $struktur,
        ], 201);
    }

    public function updateStruktur(Request $request, int $id): JsonResponse
    {
        $struktur = StrukturNagari::find($id);
        if (!$struktur) return response()->json(['success' => false, 'message' => 'Struktur organisasi tidak ditemukan.'], 404);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'foto_bagan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('foto_bagan')) {
            if ($struktur->foto_bagan && str_starts_with($struktur->foto_bagan, 'storage/')) Storage::disk('public')->delete(substr($struktur->foto_bagan, 8));
            $path = $request->file('foto_bagan')->store('uploads/struktur', 'public');
            $validated['foto_bagan'] = 'storage/' . $path;
        }

        $struktur->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil diperbarui.',
            'data' => $struktur,
        ]);
    }

    public function destroyStruktur(int $id): JsonResponse
    {
        $struktur = StrukturNagari::find($id);
        if (!$struktur) return response()->json(['success' => false, 'message' => 'Struktur organisasi tidak ditemukan.'], 404);

        if ($struktur->foto_bagan && str_starts_with($struktur->foto_bagan, 'storage/')) Storage::disk('public')->delete(substr($struktur->foto_bagan, 8));
        $struktur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil dihapus dari database.',
        ]);
    }

    // ==========================================
    // 7. OBJEK WISATA
    // ==========================================
    public function getWisata(): JsonResponse
    {
        $wisataList = ObjekWisata::all();
        return response()->json([
            'success' => true,
            'data' => $wisataList,
        ]);
    }

    public function storeWisata(Request $request): JsonResponse
    {
        $request->validate([
            'deskripsi' => 'required|string',
            'lokasi' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'harga_tiket' => 'nullable|string|max:100',
            'jam_buka' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $nama = $request->input('nama', $request->input('nama_objek'));
        if (empty($nama)) {
            return response()->json(['success' => false, 'message' => 'Nama objek wisata wajib diisi.'], 422);
        }

        $data = [
            'nama' => $nama,
            'deskripsi' => $request->input('deskripsi'),
            'lokasi' => $request->input('lokasi'),
            'kategori' => $request->input('kategori'),
            'harga_tiket' => $request->input('harga_tiket'),
            'jam_buka' => $request->input('jam_buka'),
        ];

        if ($request->hasFile('foto') || $request->hasFile('gambar')) {
            $file = $request->file('foto') ?: $request->file('gambar');
            $path = $file->store('uploads/wisata', 'public');
            $data['gambar'] = 'storage/' . $path;
        }

        $wisata = ObjekWisata::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Objek wisata berhasil ditambahkan ke database.',
            'data' => $wisata,
        ], 201);
    }

    public function updateWisata(Request $request, int $id): JsonResponse
    {
        $wisata = ObjekWisata::find($id);
        if (!$wisata) return response()->json(['success' => false, 'message' => 'Objek wisata tidak ditemukan.'], 404);

        $request->validate([
            'deskripsi' => 'required|string',
            'lokasi' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'harga_tiket' => 'nullable|string|max:100',
            'jam_buka' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $nama = $request->input('nama', $request->input('nama_objek', $wisata->nama));

        $data = [
            'nama' => $nama,
            'deskripsi' => $request->input('deskripsi'),
            'lokasi' => $request->input('lokasi'),
            'kategori' => $request->input('kategori'),
            'harga_tiket' => $request->input('harga_tiket'),
            'jam_buka' => $request->input('jam_buka'),
        ];

        if ($request->hasFile('foto') || $request->hasFile('gambar')) {
            if ($wisata->gambar && str_starts_with($wisata->gambar, 'storage/')) Storage::disk('public')->delete(substr($wisata->gambar, 8));
            $file = $request->file('foto') ?: $request->file('gambar');
            $path = $file->store('uploads/wisata', 'public');
            $data['gambar'] = 'storage/' . $path;
        }

        $wisata->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Objek wisata berhasil diperbarui.',
            'data' => $wisata,
        ]);
    }

    public function destroyWisata(int $id): JsonResponse
    {
        $wisata = ObjekWisata::find($id);
        if (!$wisata) return response()->json(['success' => false, 'message' => 'Objek wisata tidak ditemukan.'], 404);

        if ($wisata->foto && str_starts_with($wisata->foto, 'storage/')) Storage::disk('public')->delete(substr($wisata->foto, 8));
        $wisata->delete();

        return response()->json([
            'success' => true,
            'message' => 'Objek wisata berhasil dihapus dari database.',
        ]);
    }

    // ==========================================
    // 8. UMKM NAGARI
    // ==========================================
    public function getUmkm(): JsonResponse
    {
        $umkmList = UMKM::all();
        return response()->json([
            'success' => true,
            'data' => $umkmList,
        ]);
    }

    public function storeUmkm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'alamat' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:100',
            'harga_produk' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('uploads/umkm', 'public');
            $validated['foto'] = 'storage/' . $path;
        }

        $umkm = UMKM::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'UMKM nagari berhasil ditambahkan.',
            'data' => $umkm,
        ], 201);
    }

    public function updateUmkm(Request $request, int $id): JsonResponse
    {
        $umkm = UMKM::find($id);
        if (!$umkm) return response()->json(['success' => false, 'message' => 'UMKM tidak ditemukan.'], 404);

        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'alamat' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:100',
            'harga_produk' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('foto')) {
            if ($umkm->foto && str_starts_with($umkm->foto, 'storage/')) Storage::disk('public')->delete(substr($umkm->foto, 8));
            $path = $request->file('foto')->store('uploads/umkm', 'public');
            $validated['foto'] = 'storage/' . $path;
        }

        $umkm->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'UMKM nagari berhasil diperbarui.',
            'data' => $umkm,
        ]);
    }

    public function destroyUmkm(int $id): JsonResponse
    {
        $umkm = UMKM::find($id);
        if (!$umkm) return response()->json(['success' => false, 'message' => 'UMKM tidak ditemukan.'], 404);

        if ($umkm->foto && str_starts_with($umkm->foto, 'storage/')) Storage::disk('public')->delete(substr($umkm->foto, 8));
        $umkm->delete();

        return response()->json([
            'success' => true,
            'message' => 'UMKM nagari berhasil dihapus dari database.',
        ]);
    }

    // ==========================================
    // 9. KABA NAGARI (BERITA & PENGUMUMAN)
    // ==========================================
    public function kabaList(Request $request): JsonResponse
    {
        $query = KabaNagari::with('gambarKabaNagaris')->latest();

        if ($request->filled('kategori') && $request->query('kategori') !== 'semua') {
            $query->where('kategori', $request->query('kategori'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
            'total' => $query->count(),
        ]);
    }

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

    // ==========================================
    // 10. GALERI & MEDIA NAGARI
    // ==========================================
    public function getGaleri(): JsonResponse
    {
        $galeriList = FotoKabaNagari::with('kabaNagari')->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $galeriList,
        ]);
    }

    public function storeGaleri(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'caption' => 'nullable|string|max:255',
            'kaba_nagari_id' => 'nullable|exists:kaba_nagaris,id',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/galeri', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        $galeri = FotoKabaNagari::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil ditambahkan ke galeri publik.',
            'data' => $galeri,
        ], 201);
    }

    public function updateGaleri(Request $request, int $id): JsonResponse
    {
        $galeri = FotoKabaNagari::find($id);
        if (!$galeri) return response()->json(['success' => false, 'message' => 'Galeri tidak ditemukan.'], 404);

        $validated = $request->validate([
            'caption' => 'nullable|string|max:255',
            'kaba_nagari_id' => 'nullable|exists:kaba_nagaris,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            if ($galeri->gambar && str_starts_with($galeri->gambar, 'storage/')) Storage::disk('public')->delete(substr($galeri->gambar, 8));
            $path = $request->file('gambar')->store('uploads/galeri', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        $galeri->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data galeri berhasil diperbarui.',
            'data' => $galeri,
        ]);
    }

    public function destroyGaleri(int $id): JsonResponse
    {
        $galeri = FotoKabaNagari::find($id);
        if (!$galeri) return response()->json(['success' => false, 'message' => 'Galeri tidak ditemukan.'], 404);

        if ($galeri->gambar && str_starts_with($galeri->gambar, 'storage/')) Storage::disk('public')->delete(substr($galeri->gambar, 8));
        $galeri->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto galeri berhasil dihapus dari database.',
        ]);
    }
}
