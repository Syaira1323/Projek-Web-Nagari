<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdatBudayaNagari;
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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminKontenController extends Controller
{
    // ==========================================
    // 1. PROFIL NAGARI
    // ==========================================
    public function profil(): View
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

        return view('admin.konten.profil', compact('profil'));
    }

    public function updateProfil(Request $request): RedirectResponse
    {
        $profil = ProfilNagari::first();
        if (!$profil) {
            $profil = new ProfilNagari();
        }

        $validated = $request->validate([
            'nama_nagari' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'jumlah_penduduk' => 'required|integer|min:1',
            'jumlah_jorong' => 'required|integer|min:1',
            'luas_wilayah' => 'required|numeric|min:0.1',
            'deskripsi' => 'required|string',
            'alamat' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:150',
            'telepon' => 'nullable|string|max:50',
            'foto_utama' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
            'logo' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto_utama')) {
            $path = $request->file('foto_utama')->store('uploads/profil', 'public');
            $validated['foto_utama'] = 'storage/' . $path;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads/profil', 'public');
            $validated['logo'] = 'storage/' . $path;
        }

        $profil->fill($validated)->save();

        return back()->with('success', 'Data Profil Nagari & Foto Utama berhasil diperbarui di database.');
    }

    // ==========================================
    // 2. ADAT & BUDAYA NAGARI
    // ==========================================
    public function adat(): View
    {
        $adat = AdatBudayaNagari::firstOrCreate(['id' => 1], [
            'uraian' => 'Masyarakat Nagari Koto Malintang menjunjung tinggi falsafah Adat Basandi Syarak, Syarak Basandi Kitabullah...',
            'jumlah_suku' => 7,
            'kegiatan_adat' => ['Wirid Kepayungan', 'Batagak Pangulu', 'Mangaji Tamaik', 'Pesta Budaya Danau Maninjau'],
        ]);

        return view('admin.konten.adat', compact('adat'));
    }

    public function updateAdat(Request $request): RedirectResponse
    {
        $adat = AdatBudayaNagari::first();
        if (!$adat) {
            $adat = new AdatBudayaNagari();
        }

        $validated = $request->validate([
            'uraian' => 'required|string',
            'jumlah_suku' => 'required|integer|min:1',
            'kegiatan_adat_raw' => 'required|string',
            'gambar' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $kegiatanArray = array_filter(array_map('trim', explode("\n", $validated['kegiatan_adat_raw'])));

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/adat', 'public');
            $adat->gambar = 'storage/' . $path;
        }

        $adat->uraian = $validated['uraian'];
        $adat->jumlah_suku = $validated['jumlah_suku'];
        $adat->kegiatan_adat = array_values($kegiatanArray);
        $adat->save();

        return back()->with('success', 'Data Adat dan Budaya Nagari beserta dokumentasi gambar berhasil diperbarui.');
    }

    // ==========================================
    // 3. VISI & MISI
    // ==========================================
    public function visiMisi(): View
    {
        $visi = Visi::with(['maknaVisis', 'misis'])->first();

        return view('admin.konten.visi-misi', compact('visi'));
    }

    public function updateVisiMisi(Request $request): RedirectResponse
    {
        $visi = Visi::first();
        if (!$visi) {
            $visi = Visi::create(['judul' => 'VISI', 'isi' => 'Terwujudnya Nagari Koto Malintang yang Maju, Mandiri, dan Sejahtera...']);
        }

        $validated = $request->validate([
            'isi' => 'required|string',
            'misi_raw' => 'required|string',
        ]);

        $visi->isi = $validated['isi'];
        $visi->save();

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

        return back()->with('success', 'Data Visi dan Misi Nagari berhasil diperbarui.');
    }

    // ==========================================
    // 4. SEJARAH NAGARI (CRUD + GAMBAR)
    // ==========================================
    public function sejarah(): View
    {
        $sejarahList = SejarahNagari::orderBy('tipe')->orderBy('urutan')->get();

        return view('admin.konten.sejarah', compact('sejarahList'));
    }

    public function storeSejarah(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tipe' => 'required|string|in:singkat,periode',
            'judul' => 'required|string|max:255',
            'periode' => 'nullable|string|max:100',
            'urutan' => 'required|integer',
            'isi' => 'required|string',
            'gambar' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/sejarah', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        SejarahNagari::create($validated);

        return back()->with('success', 'Data sejarah nagari beserta foto dokumentasi berhasil ditambahkan.');
    }

    public function updateSejarah(Request $request, SejarahNagari $sejarah): RedirectResponse
    {
        $validated = $request->validate([
            'tipe' => 'required|string|in:singkat,periode',
            'judul' => 'required|string|max:255',
            'periode' => 'nullable|string|max:100',
            'urutan' => 'required|integer',
            'isi' => 'required|string',
            'gambar' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/sejarah', 'public');
            $validated['gambar'] = 'storage/' . $path;
        } else {
            unset($validated['gambar']);
        }

        $sejarah->update($validated);

        return back()->with('success', 'Data sejarah berhasil diperbarui.');
    }

    public function destroySejarah(SejarahNagari $sejarah): RedirectResponse
    {
        $sejarah->delete();

        return back()->with('success', 'Data sejarah berhasil dihapus.');
    }

    // ==========================================
    // 5. PERANGKAT NAGARI (CRUD + UPLOAD FOTO)
    // ==========================================
    public function perangkat(): View
    {
        $perangkatList = PerangkatNagari::all();

        return view('admin.konten.perangkat', compact('perangkatList'));
    }

    public function storePerangkat(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'status_keaktifan' => 'required|string|in:Aktif,Nonaktif',
            'foto' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('uploads/perangkat', 'public');
            $validated['foto'] = 'storage/' . $path;
        }

        PerangkatNagari::create($validated);

        return back()->with('success', 'Data aparatur perangkat nagari beserta foto berhasil ditambahkan.');
    }

    public function updatePerangkat(Request $request, PerangkatNagari $perangkat): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'status_keaktifan' => 'required|string|in:Aktif,Nonaktif',
            'foto' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('uploads/perangkat', 'public');
            $validated['foto'] = 'storage/' . $path;
        } else {
            unset($validated['foto']);
        }

        $perangkat->update($validated);

        return back()->with('success', 'Data perangkat nagari berhasil diperbarui.');
    }

    public function destroyPerangkat(PerangkatNagari $perangkat): RedirectResponse
    {
        $perangkat->delete();

        return back()->with('success', 'Data perangkat nagari berhasil dihapus.');
    }

    // ==========================================
    // 6. STRUKTUR ORGANISASI NAGARI (CRUD + BAGAN)
    // ==========================================
    public function struktur(): View
    {
        $strukturList = StrukturNagari::all();

        return view('admin.konten.struktur', compact('strukturList'));
    }

    public function storeStruktur(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'foto_bagan' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('foto_bagan')) {
            $path = $request->file('foto_bagan')->store('uploads/struktur', 'public');
            $validated['foto_bagan'] = 'storage/' . $path;
        }

        StrukturNagari::create($validated);

        return back()->with('success', 'Posisi struktur organisasi berhasil ditambahkan.');
    }

    public function updateStruktur(Request $request, StrukturNagari $struktur): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'foto_bagan' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('foto_bagan')) {
            $path = $request->file('foto_bagan')->store('uploads/struktur', 'public');
            $validated['foto_bagan'] = 'storage/' . $path;
        } else {
            unset($validated['foto_bagan']);
        }

        $struktur->update($validated);

        return back()->with('success', 'Posisi struktur organisasi berhasil diperbarui.');
    }

    public function destroyStruktur(StrukturNagari $struktur): RedirectResponse
    {
        $struktur->delete();

        return back()->with('success', 'Posisi struktur organisasi berhasil dihapus.');
    }

    // ==========================================
    // 7. OBJEK WISATA NAGARI (CRUD + UPLOAD GAMBAR)
    // ==========================================
    public function wisata(): View
    {
        $wisataList = ObjekWisata::all();

        return view('admin.konten.wisata', compact('wisataList'));
    }

    public function storeWisata(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'lokasi' => 'nullable|string|max:255',
            'google_maps_url' => 'nullable|url|max:500',
            'gambar' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/wisata', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        ObjekWisata::create($validated);

        return back()->with('success', 'Destinasi objek wisata beserta foto berhasil ditambahkan.');
    }

    public function updateWisata(Request $request, ObjekWisata $wisata): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'lokasi' => 'nullable|string|max:255',
            'google_maps_url' => 'nullable|url|max:500',
            'gambar' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/wisata', 'public');
            $validated['gambar'] = 'storage/' . $path;
        } else {
            unset($validated['gambar']);
        }

        $wisata->update($validated);

        return back()->with('success', 'Destinasi objek wisata berhasil diperbarui.');
    }

    public function destroyWisata(ObjekWisata $wisata): RedirectResponse
    {
        $wisata->delete();

        return back()->with('success', 'Destinasi objek wisata berhasil dihapus.');
    }

    // ==========================================
    // 8. KABA NAGARI / BERITA (CRUD + UPLOAD GAMBAR)
    // ==========================================
    public function kaba(): View
    {
        $kabaList = KabaNagari::latest()->paginate(10);

        return view('admin.konten.kaba', compact('kabaList'));
    }

    public function storeKaba(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'ringkasan' => 'required|string|max:500',
            'isi' => 'required|string',
            'gambar' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/kaba', 'public');
            $validated['gambar'] = 'storage/' . $path;
        }

        KabaNagari::create($validated);

        return back()->with('success', 'Kaba nagari beserta gambar berhasil dipublikasikan.');
    }

    public function updateKaba(Request $request, KabaNagari $kaba): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'ringkasan' => 'required|string|max:500',
            'isi' => 'required|string',
            'gambar' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('uploads/kaba', 'public');
            $validated['gambar'] = 'storage/' . $path;
        } else {
            unset($validated['gambar']);
        }

        $kaba->update($validated);

        return back()->with('success', 'Kaba nagari berhasil diperbarui.');
    }

    public function destroyKaba(KabaNagari $kaba): RedirectResponse
    {
        $kaba->delete();

        return back()->with('success', 'Kaba nagari berhasil dihapus.');
    }

    // ==========================================
    // 9. UMKM NAGARI (CRUD + UPLOAD FOTO)
    // ==========================================
    public function umkm(): View
    {
        $umkmList = UMKM::latest()->paginate(10);

        return view('admin.konten.umkm', compact('umkmList'));
    }

    public function storeUmkm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'whatsapp' => 'nullable|string|max:30',
            'instagram' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|url|max:500',
            'jam_operasional' => 'nullable|string|max:100',
            'tahun_berdiri' => 'nullable|string|max:10',
            'foto' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('uploads/umkm', 'public');
            $validated['foto'] = 'storage/' . $path;
        }

        UMKM::create($validated);

        return back()->with('success', 'Data UMKM beserta foto berhasil ditambahkan ke katalog.');
    }

    public function updateUmkm(Request $request, UMKM $umkm): RedirectResponse
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'whatsapp' => 'nullable|string|max:30',
            'instagram' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|url|max:500',
            'jam_operasional' => 'nullable|string|max:100',
            'tahun_berdiri' => 'nullable|string|max:10',
            'foto' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('uploads/umkm', 'public');
            $validated['foto'] = 'storage/' . $path;
        } else {
            unset($validated['foto']);
        }

        $umkm->update($validated);

        return back()->with('success', 'Data UMKM berhasil diperbarui.');
    }

    public function destroyUmkm(UMKM $umkm): RedirectResponse
    {
        $umkm->delete();

        return back()->with('success', 'Data UMKM berhasil dihapus.');
    }
}
