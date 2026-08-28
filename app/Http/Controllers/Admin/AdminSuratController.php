<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratPermohonan;
use App\Services\AiSuratService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSuratController extends Controller
{
    protected AiSuratService $aiService;

    public function __construct(AiSuratService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Daftar seluruh permohonan surat masuk
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = SuratPermohonan::with(['user', 'berkasPersyaratans'])->latest();

        if ($status && in_array($status, ['menunggu_admin', 'menunggu_wali_nagari', 'disetujui', 'ditolak'])) {
            $query->where('status', $status);
        }

        $suratList = $query->paginate(12);

        $counts = [
            'semua' => SuratPermohonan::count(),
            'menunggu_admin' => SuratPermohonan::where('status', 'menunggu_admin')->count(),
            'menunggu_wali_nagari' => SuratPermohonan::where('status', 'menunggu_wali_nagari')->count(),
            'disetujui' => SuratPermohonan::where('status', 'disetujui')->count(),
            'ditolak' => SuratPermohonan::where('status', 'ditolak')->count(),
        ];

        return view('admin.surat.index', compact('suratList', 'status', 'counts'));
    }

    /**
     * Halaman verifikasi dan pemeriksaan surat beserta berkas persyaratan
     */
    public function show(SuratPermohonan $surat): View
    {
        $surat->load(['user', 'berkasPersyaratans']);

        if (!$surat->nomor_surat) {
            $bulanRomawi = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
            ];
            $bulanIni = $bulanRomawi[(int)date('n')];
            $tahunIni = date('Y');
            $nomorUrut = str_pad($surat->id, 3, '0', STR_PAD_LEFT);
            $saranNomorSurat = "470 / {$nomorUrut} / WN-KM / {$bulanIni} / {$tahunIni}";
        } else {
            $saranNomorSurat = $surat->nomor_surat;
        }

        return view('admin.surat.show', compact('surat', 'saranNomorSurat'));
    }

    /**
     * AJAX Generator/Refinement menggunakan AI
     */
    public function generateAi(Request $request, SuratPermohonan $surat): JsonResponse
    {
        $instruksi = $request->input('instruksi');
        $mode = $request->input('mode', 'generate');

        if ($mode === 'refine' && !empty($request->input('isi_surat'))) {
            $hasil = $this->aiService->refineDraft($request->input('isi_surat'), $instruksi ?: 'Perbaiki tata bahasa agar lebih formal dan baku.');
        } else {
            $dataPemohon = [
                'jenis_surat' => $surat->jenis_surat,
                'nama_pemohon' => $surat->nama_pemohon,
                'nik_pemohon' => $surat->nik_pemohon,
                'tempat_lahir' => $surat->tempat_lahir,
                'tanggal_lahir' => $surat->tanggal_lahir ? $surat->tanggal_lahir->format('Y-m-d') : null,
                'jenis_kelamin' => $surat->jenis_kelamin,
                'agama' => $surat->agama,
                'pekerjaan' => $surat->pekerjaan,
                'alamat' => $surat->alamat,
                'keperluan' => $surat->keperluan,
            ];
            $hasil = $this->aiService->generateDraft($dataPemohon, $surat->data_spesifik ?? [], $instruksi);
        }

        return response()->json([
            'success' => true,
            'isi_surat' => $hasil,
            'message' => 'Naskah surat berhasil disusun oleh AI.',
        ]);
    }

    /**
     * Verifikasi surat, simpan nomor surat, dan teruskan ke Wali Nagari
     */
    public function verify(Request $request, SuratPermohonan $surat): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'isi_surat' => 'required|string',
            'catatan_admin' => 'nullable|string|max:500',
        ], [
            'nomor_surat.required' => 'Nomor surat resmi wajib diisi oleh admin sebelum diteruskan ke Wali Nagari.',
            'isi_surat.required' => 'Redaksi isi surat tidak boleh kosong.',
        ]);

        $surat->update([
            'nomor_surat' => $validated['nomor_surat'],
            'isi_surat' => $validated['isi_surat'],
            'catatan_admin' => $validated['catatan_admin'],
            'status' => 'menunggu_wali_nagari',
        ]);

        return redirect()->route('admin.surat.index')
            ->with('success', "Surat No. {$validated['nomor_surat']} berhasil diverifikasi dan diteruskan ke Wali Nagari untuk pembubuhan stempel digital.");
    }

    /**
     * Tolak permohonan surat
     */
    public function reject(Request $request, SuratPermohonan $surat): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Mohon berikan alasan penolakan permohonan surat.',
        ]);

        $surat->update([
            'catatan_admin' => $validated['catatan_admin'],
            'status' => 'ditolak',
        ]);

        return redirect()->route('admin.surat.index')
            ->with('warning', 'Permohonan surat ditolak.');
    }
}
