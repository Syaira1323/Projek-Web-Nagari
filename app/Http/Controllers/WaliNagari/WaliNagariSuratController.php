<?php

namespace App\Http\Controllers\WaliNagari;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\SuratPermohonan;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaliNagariSuratController extends Controller
{
    protected GoogleDriveService $gdriveService;

    public function __construct(GoogleDriveService $gdriveService)
    {
        $this->gdriveService = $gdriveService;
    }

    /**
     * Daftar surat masuk untuk Wali Nagari
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'menunggu_wali_nagari');

        $query = SuratPermohonan::with(['user', 'berkasPersyaratans'])->latest();

        if ($status && in_array($status, ['menunggu_wali_nagari', 'disetujui', 'ditolak', 'semua'])) {
            if ($status !== 'semua') {
                $query->where('status', $status);
            }
        } else {
            $query->where('status', 'menunggu_wali_nagari');
        }

        $suratList = $query->paginate(12);

        $counts = [
            'antrean' => SuratPermohonan::where('status', 'menunggu_wali_nagari')->count(),
            'disetujui' => SuratPermohonan::where('status', 'disetujui')->count(),
            'ditolak' => SuratPermohonan::where('status', 'ditolak')->count(),
            'total' => SuratPermohonan::count(),
        ];

        return view('wali-nagari.surat.index', compact('suratList', 'status', 'counts'));
    }

    /**
     * Pratinjau surat sebelum pembubuhan stempel resmi
     */
    public function show(SuratPermohonan $surat): View
    {
        $surat->load(['user', 'berkasPersyaratans']);
        $ttdPath = Pengaturan::get('tanda_tangan_wali_path');
        $stempelPath = Pengaturan::get('stempel_nagari_path');
        $namaWali = Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I');
        $gelarWali = Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro');

        return view('wali-nagari.surat.show', compact('surat', 'ttdPath', 'stempelPath', 'namaWali', 'gelarWali'));
    }

    /**
     * Berikan Stempel Otomatis & Tanda Tangan Digital Resmi (Status Disetujui / Selesai)
     */
    public function approve(Request $request, SuratPermohonan $surat): RedirectResponse
    {
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
        $surat->catatan_wali = $request->input('catatan_wali', 'Telah diverifikasi, disetujui, dan dibubuhi Tanda Tangan serta Stempel resmi Wali Nagari Koto Malintang.');
        $surat->save();

        return redirect()->route('wali-nagari.surat.index')
            ->with('success', "Surat {$surat->nama_jenis_surat} an. {$surat->nama_pemohon} berhasil dibubuhi Stempel & TTD Digital Resmi! Nomor: {$surat->nomor_surat}");
    }

    /**
     * Tolak Permohonan Surat
     */
    public function reject(Request $request, SuratPermohonan $surat): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_wali' => 'required|string|max:500',
        ], [
            'catatan_wali.required' => 'Mohon berikan alasan penolakan surat.',
        ]);

        $surat->status = 'ditolak';
        $surat->catatan_wali = $validated['catatan_wali'];
        $surat->save();

        return redirect()->route('wali-nagari.surat.index')
            ->with('warning', 'Permohonan surat ditolak.');
    }
}
