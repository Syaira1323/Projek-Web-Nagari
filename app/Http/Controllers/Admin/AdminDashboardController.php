<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KabaNagari;
use App\Models\ObjekWisata;
use App\Models\PerangkatNagari;
use App\Models\SuratPermohonan;
use App\Models\UMKM;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard Utama Administrator Nagari
     */
    public function index(): View
    {
        $suratPending = SuratPermohonan::where('status', 'menunggu_admin')->count();
        $suratDiWali = SuratPermohonan::where('status', 'menunggu_wali_nagari')->count();
        $suratSelesai = SuratPermohonan::where('status', 'disetujui')->count();
        $totalWarga = User::where('role', 'warga')->count();

        $totalKaba = KabaNagari::count();
        $totalWisata = ObjekWisata::count();
        $totalUmkm = UMKM::count();
        $totalPerangkat = PerangkatNagari::count();

        $suratMasukTerbaru = SuratPermohonan::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'suratPending',
            'suratDiWali',
            'suratSelesai',
            'totalWarga',
            'totalKaba',
            'totalWisata',
            'totalUmkm',
            'totalPerangkat',
            'suratMasukTerbaru'
        ));
    }
}
