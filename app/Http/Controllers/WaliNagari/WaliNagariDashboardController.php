<?php

namespace App\Http\Controllers\WaliNagari;

use App\Http\Controllers\Controller;
use App\Models\KabaNagari;
use App\Models\ObjekWisata;
use App\Models\PerangkatNagari;
use App\Models\ProfilNagari;
use App\Models\SuratPermohonan;
use App\Models\UMKM;
use App\Models\User;
use Illuminate\View\View;

class WaliNagariDashboardController extends Controller
{
    /**
     * Dashboard Eksekutif & Statistik Wali Nagari
     */
    public function index(): View
    {
        $profil = ProfilNagari::first();

        $totalSurat = SuratPermohonan::count();
        $suratMenungguStempel = SuratPermohonan::where('status', 'menunggu_wali_nagari')->count();
        $suratSelesai = SuratPermohonan::where('status', 'disetujui')->count();
        $totalWarga = User::where('role', 'warga')->count();

        // Statistik per 7 Jenis Surat
        $daftarJenis = SuratPermohonan::daftarJenisSurat();
        $statistikJenisSurat = [];

        foreach ($daftarJenis as $key => $val) {
            $statistikJenisSurat[$key] = [
                'nama' => $val['nama'],
                'singkatan' => $val['singkatan'],
                'total' => SuratPermohonan::where('jenis_surat', $key)->count(),
                'disetujui' => SuratPermohonan::where('jenis_surat', $key)->where('status', 'disetujui')->count(),
            ];
        }

        $suratAntrean = SuratPermohonan::where('status', 'menunggu_wali_nagari')
            ->latest()
            ->take(6)
            ->get();

        $totalUmkm = UMKM::count();
        $totalWisata = ObjekWisata::count();
        $totalPerangkat = PerangkatNagari::count();

        return view('wali-nagari.dashboard', compact(
            'profil',
            'totalSurat',
            'suratMenungguStempel',
            'suratSelesai',
            'totalWarga',
            'statistikJenisSurat',
            'suratAntrean',
            'totalUmkm',
            'totalWisata',
            'totalPerangkat'
        ));
    }
}
