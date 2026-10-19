<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdatBudayaNagari;
use App\Models\FotoKabaNagari;
use App\Models\KabaNagari;
use App\Models\MaknaVisi;
use App\Models\Misi;
use App\Models\ObjekWisata;
use App\Models\Pengaturan;
use App\Models\PerangkatNagari;
use App\Models\ProfilNagari;
use App\Models\SejarahNagari;
use App\Models\StrukturNagari;
use App\Models\UMKM;
use App\Models\Visi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiPublicContentController extends Controller
{
    /**
     * GET /api/profil-nagari
     */
    public function getProfil(): JsonResponse
    {
        try {
            $profil = ProfilNagari::first();
            $pengaturan = [
                'nama_kantor' => Pengaturan::get('nama_kantor', 'Kantor Wali Nagari Koto Malintang'),
                'alamat_kantor' => Pengaturan::get('alamat_kantor', 'Jl. Raya Maninjau - Lubuk Basung, Kec. Tanjung Raya, Kab. Agam'),
                'telepon_kantor' => Pengaturan::get('telepon_kantor', '0812-3456-7890'),
                'email_kantor' => Pengaturan::get('email_kantor', 'layanan@nagari-kotomalintang.desa.id'),
                'jam_layanan' => Pengaturan::get('jam_layanan', 'Senin - Kamis: 08.00 - 16.00 WIB | Jumat: 08.00 - 11.30 WIB'),
                'nama_wali' => Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
                'gelar_wali' => Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
            ];

            if ($profil) {
                $profil->nama = $profil->nama_nagari;
                $profil->judul = $profil->nama_nagari;
                $profil->foto_kantor = $profil->foto_utama;
                $profil->foto = $profil->foto_utama;
            }

            return response()->json([
                'success' => true,
                'data' => $profil,
                'profil' => $profil,
                'pengaturan' => $pengaturan,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data profil nagari: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/perangkat-nagari
     */
    public function getPerangkat(): JsonResponse
    {
        try {
            $perangkat = PerangkatNagari::orderBy('id', 'asc')->get();
            $perangkat->transform(function ($p) {
                $p->foto_url = $p->foto ? (str_starts_with($p->foto, 'http') ? $p->foto : '/' . ltrim($p->foto, '/')) : null;
                return $p;
            });

            return response()->json([
                'success' => true,
                'data' => $perangkat,
                'perangkat' => $perangkat,
                'total' => $perangkat->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data perangkat nagari: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/visi-misi
     */
    public function getVisiMisi(): JsonResponse
    {
        try {
            $visi = Visi::first();
            $misi = Misi::orderBy('nomor', 'asc')->get();
            $maknaVisi = MaknaVisi::orderBy('urutan', 'asc')->get();
            $visiText = ($visi && !empty($visi->visi)) ? $visi->visi : 'Terwujudnya Nagari Koto Malintang yang Religius, Berbudaya, Maju, Mandiri, dan Berkeadilan.';

            return response()->json([
                'success' => true,
                'data' => [
                    'visi' => $visiText,
                    'misi' => $misi,
                    'makna_visi' => $maknaVisi,
                ],
                'visi' => $visiText,
                'misi' => $misi,
                'makna_visi' => $maknaVisi,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat visi & misi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/sejarah
     */
    public function getSejarah(): JsonResponse
    {
        try {
            $allSejarah = SejarahNagari::orderBy('urutan', 'asc')->get();
            $sejarahSingkat = SejarahNagari::where('tipe', 'singkat')->first() ?? $allSejarah->first();
            $sejarahPeriode = SejarahNagari::where('tipe', 'periode')->orderBy('urutan', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $allSejarah,
                'sejarah' => $sejarahSingkat,
                'singkat' => $sejarahSingkat,
                'periode' => $sejarahPeriode,
                'timeline' => $sejarahPeriode,
                'list' => $allSejarah,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data sejarah: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/adat-budaya
     */
    public function getAdatBudaya(): JsonResponse
    {
        try {
            $adat = AdatBudayaNagari::first();
            if ($adat) {
                $adat->deskripsi = $adat->uraian;
                $adat->kegiatan = $adat->kegiatan_adat;
            }

            return response()->json([
                'success' => true,
                'data' => $adat,
                'adat' => $adat,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat adat & budaya: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/struktur-nagari
     */
    public function getStruktur(): JsonResponse
    {
        try {
            $strukturList = StrukturNagari::orderBy('id', 'asc')->get();
            $baganUtama = StrukturNagari::whereNotNull('foto_bagan')->where('foto_bagan', '!=', '')->first() ?? $strukturList->first();

            return response()->json([
                'success' => true,
                'data' => $strukturList,
                'struktur' => $strukturList,
                'bagan' => $baganUtama,
                'foto_bagan' => $baganUtama ? $baganUtama->foto_bagan : null,
                'total' => $strukturList->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat struktur nagari: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/umkm
     */
    public function getUmkm(Request $request): JsonResponse
    {
        try {
            $query = UMKM::latest();
            if ($request->filled('kategori') && $request->query('kategori') !== 'Semua') {
                $query->where('kategori', $request->query('kategori'));
            }
            $umkms = $query->get();
            $umkms->transform(function ($u) {
                $u->nama = $u->nama_usaha;
                $u->wa = $u->whatsapp;
                $u->tahun = $u->tahun_berdiri;
                $u->alamat = $u->alamat ?? 'Nagari Koto Malintang';
                $u->foto_url = $u->foto ? (str_starts_with($u->foto, 'http') ? $u->foto : '/' . ltrim($u->foto, '/')) : null;
                return $u;
            });

            return response()->json([
                'success' => true,
                'data' => $umkms,
                'umkm' => $umkms,
                'total' => $umkms->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data UMKM: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/objek-wisata
     */
    public function getWisata(): JsonResponse
    {
        try {
            $wisata = ObjekWisata::latest()->get();
            $wisata->transform(function ($w) {
                $w->nama_objek = $w->nama;
                $w->foto = $w->gambar;
                $w->foto_url = $w->gambar ? (str_starts_with($w->gambar, 'http') ? $w->gambar : '/' . ltrim($w->gambar, '/')) : null;
                return $w;
            });

            return response()->json([
                'success' => true,
                'data' => $wisata,
                'wisata' => $wisata,
                'total' => $wisata->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data objek wisata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/kaba-nagari
     */
    public function getKaba(Request $request): JsonResponse
    {
        try {
            $query = KabaNagari::with('gambarKabaNagaris')->latest();
            if ($request->filled('kategori') && $request->query('kategori') !== 'Semua') {
                $query->where('kategori', $request->query('kategori'));
            }
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('ringkasan', 'like', "%{$search}%")
                      ->orWhere('isi', 'like', "%{$search}%");
                });
            }

            $paginated = $query->paginate(12);
            $items = collect($paginated->items())->map(function ($k) {
                $k->title = $k->judul;
                $k->excerpt = $k->ringkasan ?? (mb_substr(strip_tags($k->isi), 0, 150) . '...');
                $k->foto = $k->gambar;
                return $k;
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'items' => $items,
                'pagination' => $paginated,
                'total' => $paginated->total(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat kaba nagari: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/kaba-nagari/{id}
     */
    public function getKabaDetail(string $id): JsonResponse
    {
        try {
            $kaba = KabaNagari::with('gambarKabaNagaris')
                ->where('id', $id)
                ->first();

            if (!$kaba) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kaba Nagari tidak ditemukan.',
                ], 404);
            }

            $kaba->title = $kaba->judul;
            $kaba->excerpt = $kaba->ringkasan;
            $kaba->foto = $kaba->gambar;

            return response()->json([
                'success' => true,
                'data' => $kaba,
                'kaba' => $kaba,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail kaba: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/galeri
     */
    public function getGaleri(): JsonResponse
    {
        try {
            $galeri = FotoKabaNagari::with('kabaNagari')->latest()->get();
            $galeri->transform(function ($g) {
                $g->judul = $g->judul ?? ($g->kabaNagari ? $g->kabaNagari->judul : 'Dokumentasi Nagari');
                $g->caption = $g->caption ?? $g->judul;
                $g->foto = $g->gambar;
                $g->foto_url = $g->gambar ? (str_starts_with($g->gambar, 'http') ? $g->gambar : '/' . ltrim($g->gambar, '/')) : null;
                return $g;
            });

            return response()->json([
                'success' => true,
                'data' => $galeri,
                'galeri' => $galeri,
                'total' => $galeri->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat galeri: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/jenis-surat & GET /api/persyaratan-surat
     */
    public function getJenisSurat(): JsonResponse
    {
        try {
            $jenisSurat = [
                [
                    'id' => 'suket_usaha',
                    'kode' => 'SKU',
                    'nama' => 'Surat Keterangan Usaha (SKU)',
                    'deskripsi' => 'Keterangan kepemilikan dan legalitas tempat usaha untuk syarat KUR bank atau izin usaha.',
                    'persyaratan' => ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Foto Tempat Usaha'],
                ],
                [
                    'id' => 'suket_tidak_mampu',
                    'kode' => 'SKTM',
                    'nama' => 'Surat Keterangan Tidak Mampu (SKTM)',
                    'deskripsi' => 'Keterangan kondisi sosial ekonomi keluarga untuk beasiswa KIP atau keringanan BPJS.',
                    'persyaratan' => ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Foto Rumah', 'Surat Pengantar Jorong'],
                ],
                [
                    'id' => 'suket_domisili',
                    'kode' => 'SKD',
                    'nama' => 'Surat Keterangan Domisili (SKD)',
                    'deskripsi' => 'Keterangan tempat tinggal resmi bagi warga di Nagari Koto Malintang.',
                    'persyaratan' => ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Surat Pengantar Jorong'],
                ],
                [
                    'id' => 'suket_meninggal',
                    'kode' => 'SKM',
                    'nama' => 'Surat Keterangan Meninggal (SKM)',
                    'deskripsi' => 'Pencatatan resmi kematian warga untuk pengurusan akta kematian atau klaim asuransi.',
                    'persyaratan' => ['KTP Pelapor', 'KK Almarhum', 'Surat Kematian RS/Bidan/Jorong'],
                ],
                [
                    'id' => 'suket_belum_menikah',
                    'kode' => 'SKBM',
                    'nama' => 'Surat Keterangan Belum Menikah (SKBM)',
                    'deskripsi' => 'Keterangan status lajang/belum pernah menikah untuk melamar kerja atau pendaftaran kedinasan.',
                    'persyaratan' => ['KTP Pemohon', 'Kartu Keluarga (KK)'],
                ],
                [
                    'id' => 'surat_nikah_na',
                    'kode' => 'NA',
                    'nama' => 'Surat Pengantar Nikah (Model NA)',
                    'deskripsi' => 'Pengantar kehendak nikah sebelum didaftarkan ke KUA Kecamatan Tanjung Raya.',
                    'persyaratan' => ['KTP Calon Pengantin', 'Kartu Keluarga (KK)', 'KTP Pasangan', 'Pengantar Nikah Jorong'],
                ],
                [
                    'id' => 'sket_penghasilan',
                    'kode' => 'SKP',
                    'nama' => 'Surat Keterangan Penghasilan (SKP)',
                    'deskripsi' => 'Keterangan estimasi rata-rata pendapatan orang tua/pribadi untuk beasiswa atau kredit.',
                    'persyaratan' => ['KTP', 'Kartu Keluarga (KK)', 'Slip Gaji / Surat Pernyataan'],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $jenisSurat,
                'jenis_surat' => $jenisSurat,
                'persyaratan' => $jenisSurat,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat jenis surat: ' . $e->getMessage(),
            ], 500);
        }
    }
}

