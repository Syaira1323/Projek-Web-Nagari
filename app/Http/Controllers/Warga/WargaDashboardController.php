<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\SuratPermohonan;
use App\Models\UMKM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WargaDashboardController extends Controller
{
    /**
     * Dashboard Utama Masyarakat / Warga
     */
    public function index(): View
    {
        $user = Auth::user();

        $totalSurat = SuratPermohonan::where('user_id', $user->id)->count();
        $suratMenunggu = SuratPermohonan::where('user_id', $user->id)->whereIn('status', ['menunggu_admin', 'menunggu_wali_nagari'])->count();
        $suratSelesai = SuratPermohonan::where('user_id', $user->id)->where('status', 'disetujui')->count();
        $totalUmkm = UMKM::where('user_id', $user->id)->count();

        $suratTerbaru = SuratPermohonan::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $umkmSaya = UMKM::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        return view('warga.dashboard', compact(
            'user',
            'totalSurat',
            'suratMenunggu',
            'suratSelesai',
            'totalUmkm',
            'suratTerbaru',
            'umkmSaya'
        ));
    }
}
