<?php

namespace App\Http\Controllers;

use App\Models\SuratPermohonan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LayananSuratController extends Controller
{
    /**
     * Tampilkan Halaman Publik Layanan Surat Nagari Koto Malintang
     */
    public function index(): View
    {
        $daftarSurat = SuratPermohonan::daftarJenisSurat();

        return view('layanan-surat', compact('daftarSurat'));
    }
}
