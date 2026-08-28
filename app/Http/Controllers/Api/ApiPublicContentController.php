<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdatBudayaNagari;
use App\Models\KabaNagari;
use App\Models\ObjekWisata;
use App\Models\Pengaturan;
use App\Models\PerangkatNagari;
use App\Models\ProfilNagari;
use App\Models\SejarahNagari;
use App\Models\StrukturNagari;
use App\Models\UMKM;
use App\Models\Visi;
use App\Models\Misi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiPublicContentController extends Controller
{
    /**
     * GET /api/profil-nagari
     */
    public function getProfil(): JsonResponse
    {
        $profil = ProfilNagari::first();
        return response()->json([
            'success' => true,
            'data' => $profil,
            'pengaturan' => [
                'nama_kantor' => Pengaturan::get('nama_kantor', 'Kantor Wali Nagari Koto Malintang'),
                'alamat_kantor' => Pengaturan::get('alamat_kantor', 'Jl. Raya Maninjau - Lubuk Basung, Kec. Tanjung Raya, Kab. Agam'),
                'telepon_kantor' => Pengaturan::get('telepon_kantor', '0812-3456-7890'),
                'email_kantor' => Pengaturan::get('email_kantor', 'layanan@nagari-kotomalintang.desa.id'),
                'jam_layanan' => Pengaturan::get('jam_layanan', 'Senin - Kamis: 08.00 - 16.00 WIB | Jumat: 08.00 - 11.30 WIB'),
                'nama_wali' => Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I'),
                'gelar_wali' => Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro'),
            ],
        ]);
    }

    /**
     * GET /api/perangkat-nagari
     */
    public function getPerangkat(): JsonResponse
    {
        $perangkat = PerangkatNagari::orderBy('id', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $perangkat,
        ]);
    }

    /**
     * GET /api/visi-misi
     */
    public function getVisiMisi(): JsonResponse
    {
        $visi = Visi::first();
        $misi = Misi::orderBy('nomor', 'asc')->get();

        return response()->json([
            'success' => true,
            'visi' => $visi ? $visi->visi : 'Terwujudnya Nagari Koto Malintang yang Religius, Berbudaya, Maju, Mandiri, dan Berkeadilan.',
            'misi' => $misi,
        ]);
    }

    /**
     * GET /api/sejarah
     */
    public function getSejarah(): JsonResponse
    {
        $sejarah = SejarahNagari::first();
        return response()->json([
            'success' => true,
            'data' => $sejarah,
        ]);
    }

    /**
     * GET /api/adat-budaya
     */
    public function getAdatBudaya(): JsonResponse
    {
        $adat = AdatBudayaNagari::first();
        return response()->json([
            'success' => true,
            'data' => $adat,
        ]);
    }

    /**
     * GET /api/struktur-nagari
     */
    public function getStruktur(): JsonResponse
    {
        $struktur = StrukturNagari::first();
        return response()->json([
            'success' => true,
            'data' => $struktur,
        ]);
    }

    /**
     * GET /api/umkm
     */
    public function getUmkm(Request $request): JsonResponse
    {
        $query = UMKM::latest();
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->query('kategori'));
        }
        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/objek-wisata
     */
    public function getWisata(): JsonResponse
    {
        $wisata = ObjekWisata::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $wisata,
        ]);
    }

    /**
     * GET /api/kaba-nagari
     */
    public function getKaba(Request $request): JsonResponse
    {
        $query = KabaNagari::with('gambarKabaNagaris')->latest();
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->query('kategori'));
        }
        return response()->json([
            'success' => true,
            'data' => $query->paginate(12),
        ]);
    }

    /**
     * GET /api/jenis-surat
     */
    public function getJenisSurat(): JsonResponse
    {
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
        ]);
    }
}
