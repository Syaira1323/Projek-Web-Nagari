<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\SuratPermohonan;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminArsipController extends Controller
{
    protected GoogleDriveService $gdriveService;

    public function __construct(GoogleDriveService $gdriveService)
    {
        $this->gdriveService = $gdriveService;
    }

    /**
     * Tampilkan halaman utama manajemen arsip surat resmi
     */
    public function index(Request $request): View
    {
        $query = SuratPermohonan::with('user')
            ->where('status', 'disetujui');

        // Filter Jenis Surat
        if ($request->filled('jenis_surat')) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        // Filter Bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('disetujui_wali_at', $request->bulan);
        }

        // Filter Tahun
        if ($request->filled('tahun')) {
            $query->whereYear('disetujui_wali_at', $request->tahun);
        }

        // Pencarian (Nomor surat, nama pemohon, NIK, keperluan)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_surat', 'like', "%{$q}%")
                    ->orWhere('nama_pemohon', 'like', "%{$q}%")
                    ->orWhere('nik_pemohon', 'like', "%{$q}%")
                    ->orWhere('keperluan', 'like', "%{$q}%");
            });
        }

        $arsipList = $query->latest('disetujui_wali_at')->paginate(15)->withQueryString();

        // Statistik ringkas arsip
        $totalArsip = SuratPermohonan::where('status', 'disetujui')->count();
        $arsipBulanIni = SuratPermohonan::where('status', 'disetujui')
            ->whereMonth('disetujui_wali_at', date('m'))
            ->whereYear('disetujui_wali_at', date('Y'))
            ->count();

        $arsipPerJenis = SuratPermohonan::where('status', 'disetujui')
            ->selectRaw('jenis_surat, count(*) as total')
            ->groupBy('jenis_surat')
            ->pluck('total', 'jenis_surat')
            ->toArray();

        $daftarSurat = [
            'suket_usaha' => 'Surat Keterangan Usaha (SKU)',
            'suket_meninggal' => 'Surat Keterangan Meninggal (SKM)',
            'suket_belum_menikah' => 'Surat Keterangan Belum Menikah (SKBM)',
            'surat_nikah_na' => 'Surat Pengantar Nikah (Model NA)',
            'sket_penghasilan' => 'Surat Keterangan Penghasilan (SKP)',
            'suket_tidak_mampu' => 'Surat Keterangan Tidak Mampu (SKTM)',
            'suket_domisili' => 'Surat Keterangan Domisili (SKD)',
        ];

        // Google Drive Status & Auth
        $isConnected = $this->gdriveService->isConnected();
        $isConfigured = $this->gdriveService->isConfigured();
        $connectedEmail = Pengaturan::get('gdrive_account_email', '-');
        $redirectUri = route('admin.gdrive.callback');
        $authUrl = $isConfigured ? $this->gdriveService->getAuthUrl($redirectUri) : null;
        $gdriveFolderUrl = config('services.gdrive_arsip_url', 'https://drive.google.com/');

        return view('admin.arsip.index', compact(
            'arsipList',
            'totalArsip',
            'arsipBulanIni',
            'arsipPerJenis',
            'daftarSurat',
            'isConnected',
            'isConfigured',
            'connectedEmail',
            'authUrl',
            'redirectUri',
            'gdriveFolderUrl'
        ));
    }

    /**
     * Unduh rekapitulasi data arsip surat dalam format CSV/Excel
     */
    public function exportCsv(Request $request)
    {
        $query = SuratPermohonan::with('user')->where('status', 'disetujui');

        if ($request->filled('jenis_surat')) {
            $query->where('jenis_surat', $request->jenis_surat);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('disetujui_wali_at', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('disetujui_wali_at', $request->tahun);
        }

        $records = $query->latest('disetujui_wali_at')->get();

        $filename = 'Rekap_Arsip_Surat_Koto_Malintang_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            // Menambahkan UTF-8 BOM agar terbaca rapi di Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            // Header Kolom
            fputcsv($file, [
                'NO',
                'NOMOR SURAT RESMI',
                'JENIS SURAT',
                'NAMA PEMOHON',
                'NIK',
                'TEMPAT, TGL LAHIR',
                'JENIS KELAMIN',
                'ALAMAT / JORONG',
                'KEPERLUAN',
                'TANGGAL PERSETUJUAN WALI NAGARI',
                'STATUS STEMPEL DIGITAL',
            ]);

            $no = 1;
            foreach ($records as $row) {
                fputcsv($file, [
                    $no++,
                    $row->nomor_surat ?: '-',
                    $row->nama_jenis_surat,
                    $row->nama_pemohon,
                    "'" . ($row->nik_pemohon ?: '-'),
                    $row->tempat_lahir . ', ' . ($row->tanggal_lahir ? $row->tanggal_lahir->format('d/m/Y') : '-'),
                    $row->jenis_kelamin,
                    $row->alamat,
                    $row->keperluan,
                    $row->disetujui_wali_at ? $row->disetujui_wali_at->format('d/m/Y H:i') : '-',
                    $row->stempel_digital ? 'RESMI TERSTEMPEL' : 'BELUM',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Unduh paket data arsip digital JSON lengkap untuk cadangan (backup)
     */
    public function backupJson()
    {
        $records = SuratPermohonan::where('status', 'disetujui')->get();

        $backupData = [
            'nagari' => 'Pemerintah Nagari Koto Malintang',
            'kecamatan' => 'Tanjung Raya',
            'kabupaten' => 'Agam',
            'exported_at' => now()->toIso8601String(),
            'total_surat' => $records->count(),
            'arsip' => $records->toArray(),
        ];

        $jsonString = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $filename = 'Backup_Arsip_Digital_Koto_Malintang_' . date('Ymd_His') . '.json';

        return response($jsonString, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
