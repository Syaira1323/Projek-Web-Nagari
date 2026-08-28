<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\BerkasPersyaratan;
use App\Models\SuratPermohonan;
use App\Services\AiSuratService;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class WargaSuratController extends Controller
{
    protected AiSuratService $aiService;
    protected GoogleDriveService $gdriveService;

    public function __construct(AiSuratService $aiService, GoogleDriveService $gdriveService)
    {
        $this->aiService = $aiService;
        $this->gdriveService = $gdriveService;
    }

    /**
     * Daftar permohonan surat milik warga yang login
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = SuratPermohonan::where('user_id', $user->id)->latest();

        if ($status && in_array($status, ['menunggu_admin', 'menunggu_wali_nagari', 'disetujui', 'ditolak'])) {
            $query->where('status', $status);
        }

        $suratList = $query->paginate(10);

        $counts = [
            'semua' => SuratPermohonan::where('user_id', $user->id)->count(),
            'menunggu_admin' => SuratPermohonan::where('user_id', $user->id)->where('status', 'menunggu_admin')->count(),
            'menunggu_wali_nagari' => SuratPermohonan::where('user_id', $user->id)->where('status', 'menunggu_wali_nagari')->count(),
            'disetujui' => SuratPermohonan::where('user_id', $user->id)->where('status', 'disetujui')->count(),
            'ditolak' => SuratPermohonan::where('user_id', $user->id)->where('status', 'ditolak')->count(),
        ];

        return view('warga.surat.index', compact('suratList', 'status', 'counts'));
    }

    /**
     * Form pengajuan surat baru
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $daftarSurat = SuratPermohonan::daftarJenisSurat();
        $selectedJenis = $request->query('jenis', 'suket_usaha');

        if (!array_key_exists($selectedJenis, $daftarSurat)) {
            $selectedJenis = 'suket_usaha';
        }

        return view('warga.surat.create', compact('user', 'daftarSurat', 'selectedJenis'));
    }

    /**
     * Simpan pengajuan surat dan upload berkas persyaratan ke Google Drive / Storage
     */
    public function store(Request $request): RedirectResponse
    {
        $daftarSurat = SuratPermohonan::daftarJenisSurat();

        $validated = $request->validate([
            'jenis_surat' => 'required|string|in:' . implode(',', array_keys($daftarSurat)),
            'nama_pemohon' => 'required|string|max:255',
            'nik_pemohon' => 'nullable|string|max:30',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'agama' => 'required|string|max:50',
            'pekerjaan' => 'required|string|max:100',
            'alamat' => 'required|string|max:500',
            'keperluan' => 'required|string|max:500',
            // Data spesifik
            'nama_usaha' => 'nullable|string|max:255',
            'bidang_usaha' => 'nullable|string|max:255',
            'alamat_usaha' => 'nullable|string|max:255',
            'nama_almarhum' => 'nullable|string|max:255',
            'tanggal_meninggal' => 'nullable|date',
            'tempat_meninggal' => 'nullable|string|max:255',
            'sebab_meninggal' => 'nullable|string|max:255',
            'nama_calon_pasangan' => 'nullable|string|max:255',
            'bin_binti_pasangan' => 'nullable|string|max:255',
            'nominal_penghasilan' => 'nullable|numeric',
            'sumber_penghasilan' => 'nullable|string|max:255',
            'tanggungan_anak' => 'nullable|integer',
            // Berkas Uploads
            'berkas_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_pengantar' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_foto_usaha' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_foto_rumah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_kematian' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_ktp_pasangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_slip' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_lainnya' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'nama_pemohon.required' => 'Nama pemohon wajib diisi.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'agama.required' => 'Agama wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'alamat.required' => 'Alamat jorong wajib diisi.',
            'keperluan.required' => 'Keperluan permohonan surat wajib dijelaskan.',
            'berkas_ktp.max' => 'Ukuran file KTP maksimal 5MB.',
            'berkas_kk.max' => 'Ukuran file KK maksimal 5MB.',
        ]);

        // Kumpulkan data spesifik
        $dataSpesifik = [];
        $jenis = $validated['jenis_surat'];

        if ($jenis === 'suket_usaha') {
            $dataSpesifik = [
                'nama_usaha' => $request->input('nama_usaha', '-'),
                'bidang_usaha' => $request->input('bidang_usaha', '-'),
                'alamat_usaha' => $request->input('alamat_usaha', $validated['alamat']),
            ];
        } elseif ($jenis === 'suket_meninggal') {
            $dataSpesifik = [
                'nama_almarhum' => $request->input('nama_almarhum', '-'),
                'tanggal_meninggal' => $request->input('tanggal_meninggal', date('Y-m-d')),
                'tempat_meninggal' => $request->input('tempat_meninggal', '-'),
                'sebab_meninggal' => $request->input('sebab_meninggal', '-'),
            ];
        } elseif ($jenis === 'suket_belum_menikah') {
            $dataSpesifik = [
                'status_pernikahan' => 'Belum Menikah / Lajang',
            ];
        } elseif ($jenis === 'surat_nikah_na') {
            $dataSpesifik = [
                'nama_calon_pasangan' => $request->input('nama_calon_pasangan', '-'),
                'bin_binti_pasangan' => $request->input('bin_binti_pasangan', '-'),
            ];
        } elseif ($jenis === 'sket_penghasilan') {
            $dataSpesifik = [
                'nominal_penghasilan' => $request->input('nominal_penghasilan', 0),
                'sumber_penghasilan' => $request->input('sumber_penghasilan', '-'),
                'tanggungan_anak' => $request->input('tanggungan_anak', 0),
            ];
        } elseif ($jenis === 'suket_tidak_mampu') {
            $dataSpesifik = [
                'penghasilan_rata_rata' => $request->input('nominal_penghasilan', 0),
                'keterangan_ekonomi' => 'Keluarga Kurang Mampu / Pra-Sejahtera',
            ];
        } elseif ($jenis === 'suket_domisili') {
            $dataSpesifik = [
                'lama_domisili' => $request->input('lama_domisili', 'Sejak Lahir'),
            ];
        }

        $nikPemohon = !empty($validated['nik_pemohon']) ? $validated['nik_pemohon'] : (Auth::user()->nik ?: '-');
        $validated['nik_pemohon'] = $nikPemohon;

        // Generate draft redaksi resmi menggunakan AI / Smart Template
        $isiSurat = $this->aiService->generateDraft($validated, $dataSpesifik);

        $surat = SuratPermohonan::create([
            'user_id' => Auth::id(),
            'jenis_surat' => $validated['jenis_surat'],
            'nama_pemohon' => $validated['nama_pemohon'],
            'nik_pemohon' => $nikPemohon,
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'agama' => $validated['agama'],
            'pekerjaan' => $validated['pekerjaan'],
            'alamat' => $validated['alamat'],
            'keperluan' => $validated['keperluan'],
            'data_spesifik' => $dataSpesifik,
            'isi_surat' => $isiSurat,
            'status' => 'menunggu_admin',
        ]);

        // Upload Berkas Persyaratan ke Google Drive / Storage
        $berkasKeys = [
            'berkas_ktp' => 'ktp',
            'berkas_kk' => 'kk',
            'berkas_pengantar' => 'surat_pengantar',
            'berkas_foto_usaha' => 'foto_usaha',
            'berkas_foto_rumah' => 'foto_rumah',
            'berkas_kematian' => 'surat_kematian_rs',
            'berkas_ktp_pasangan' => 'ktp_pasangan',
            'berkas_slip' => 'slip_penghasilan',
            'berkas_lainnya' => 'dokumen_pendukung',
        ];

        foreach ($berkasKeys as $inputName => $jenisBerkas) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $uploadRes = $this->gdriveService->uploadFile($file, $surat->id, $jenisBerkas);

                if (!empty($uploadRes['success'])) {
                    BerkasPersyaratan::create([
                        'surat_permohonan_id' => $surat->id,
                        'jenis_berkas' => $jenisBerkas,
                        'nama_file_asli' => $uploadRes['nama_file_asli'],
                        'gdrive_file_id' => $uploadRes['gdrive_file_id'],
                        'gdrive_view_link' => $uploadRes['gdrive_view_link'],
                        'file_path_local' => $uploadRes['file_path_local'],
                        'mime_type' => $uploadRes['mime_type'],
                        'file_size' => $uploadRes['file_size'],
                        'folder_path' => $uploadRes['folder_path'],
                    ]);
                }
            }
        }

        return redirect()->route('warga.surat.show', $surat->id)
            ->with('success', 'Permohonan surat dan berkas persyaratan berhasil diajukan! Silakan tunggu verifikasi admin.');
    }

    /**
     * Pratinjau status surat dan berkas terlampir
     */
    public function show(SuratPermohonan $surat): View
    {
        if ($surat->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isWaliNagari()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        $surat->load('berkasPersyaratans');

        return view('warga.surat.show', compact('surat'));
    }

    /**
     * Unduh Berkas Persyaratan secara aman (IDOR Protection)
     */
    public function downloadBerkas(BerkasPersyaratan $berkas): Response
    {
        $surat = $berkas->suratPermohonan;

        if ($surat->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isWaliNagari()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh berkas ini.');
        }

        // 1. Jika ada di Local Storage
        if (!empty($berkas->file_path_local) && Storage::exists($berkas->file_path_local)) {
            return Storage::download($berkas->file_path_local, $berkas->nama_file_asli);
        }

        // 2. Jika ada di Google Drive
        if (!empty($berkas->gdrive_file_id)) {
            $stream = $this->gdriveService->downloadFileStream($berkas->gdrive_file_id);
            if ($stream) {
                return response($stream, 200, [
                    'Content-Type' => $berkas->mime_type ?: 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $berkas->nama_file_asli . '"',
                ]);
            }

            if (!empty($berkas->gdrive_view_link)) {
                return redirect()->away($berkas->gdrive_view_link);
            }
        }

        abort(404, 'File berkas tidak ditemukan.');
    }

    /**
     * Tampilan Cetak Resmi Surat Berstempel
     */
    public function print(SuratPermohonan $surat): View
    {
        if ($surat->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isWaliNagari()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        $ttdPath = Pengaturan::get('tanda_tangan_wali_path');
        $stempelPath = Pengaturan::get('stempel_nagari_path');
        $namaWali = Pengaturan::get('nama_wali_nagari', 'NAZARUDDIN, S.Pd.I');
        $gelarWali = Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro');

        return view('warga.surat.print', compact('surat', 'ttdPath', 'stempelPath', 'namaWali', 'gelarWali'));
    }
}
