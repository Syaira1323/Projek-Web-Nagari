<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilNagari;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPengaturanController extends Controller
{
    /**
     * Halaman Pengaturan Nagari
     */
    public function index(): View
    {
        $profil = ProfilNagari::first();
        $namaWali = Pengaturan::get('nama_wali_nagari', 'Wali Nagari Koto Malintang');
        $jamSeninKamis = Pengaturan::get('jam_senin_kamis', '08:00 - 16:00 WIB');
        $jamJumat = Pengaturan::get('jam_jumat', '08:00 - 11:30 WIB');
        $nomorDarurat = Pengaturan::get('nomor_darurat', '+62 752 123456');

        return view('admin.pengaturan.index', compact('profil', 'namaWali', 'jamSeninKamis', 'jamJumat', 'nomorDarurat'));
    }

    /**
     * Simpan Pengaturan Nagari
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_wali_nagari' => 'required|string|max:255',
            'jam_senin_kamis' => 'required|string|max:100',
            'jam_jumat' => 'required|string|max:100',
            'nomor_darurat' => 'nullable|string|max:50',
        ]);

        Pengaturan::set('nama_wali_nagari', $validated['nama_wali_nagari'], 'umum');
        Pengaturan::set('jam_senin_kamis', $validated['jam_senin_kamis'], 'umum');
        Pengaturan::set('jam_jumat', $validated['jam_jumat'], 'umum');
        Pengaturan::set('nomor_darurat', $validated['nomor_darurat'], 'kontak');

        return back()->with('success', 'Pengaturan umum dan jam pelayanan nagari berhasil disimpan ke database.');
    }
}
