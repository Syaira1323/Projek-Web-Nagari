<?php

namespace App\Http\Controllers\WaliNagari;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WaliNagariTtdController extends Controller
{
    /**
     * Halaman Kelola Tanda Tangan & Stempel Resmi Nagari
     */
    public function index(): View
    {
        $ttdPath = Pengaturan::get('tanda_tangan_wali_path');
        $stempelPath = Pengaturan::get('stempel_nagari_path');
        $namaWali = Pengaturan::get('nama_wali_nagari', 'Wali Nagari Koto Malintang');
        $gelarWali = Pengaturan::get('gelar_wali_nagari', 'Datuak Bandaro');

        return view('wali-nagari.ttd-stempel.index', compact('ttdPath', 'stempelPath', 'namaWali', 'gelarWali'));
    }

    /**
     * Upload / Ganti Tanda Tangan Wali Nagari
     */
    public function updateTtd(Request $request): RedirectResponse
    {
        $request->validate([
            'tanda_tangan' => 'required|file|image|mimes:png,jpg,jpeg,webp|max:3072',
            'nama_wali' => 'nullable|string|max:255',
            'gelar_wali' => 'nullable|string|max:100',
        ], [
            'tanda_tangan.required' => 'File tanda tangan wajib diunggah.',
            'tanda_tangan.mimes' => 'Format file tanda tangan harus PNG, JPG, atau JPEG (Disarankan PNG Transparan).',
            'tanda_tangan.max' => 'Ukuran file tanda tangan maksimal 3MB.',
        ]);

        if ($request->hasFile('tanda_tangan')) {
            $file = $request->file('tanda_tangan');
            $path = $file->store('tanda-tangan', 'public');
            Pengaturan::set('tanda_tangan_wali_path', 'storage/' . $path, 'ttd_stempel', 'file', 'Path Tanda Tangan Wali Nagari');
        }

        if ($request->filled('nama_wali')) {
            Pengaturan::set('nama_wali_nagari', trim($request->input('nama_wali')), 'umum');
        }
        if ($request->filled('gelar_wali')) {
            Pengaturan::set('gelar_wali_nagari', trim($request->input('gelar_wali')), 'umum');
        }

        return back()->with('success', 'Tanda Tangan resmi Wali Nagari berhasil diunggah dan disimpan ke database.');
    }

    /**
     * Hapus Tanda Tangan
     */
    public function deleteTtd(): RedirectResponse
    {
        Pengaturan::set('tanda_tangan_wali_path', '', 'ttd_stempel');
        return back()->with('success', 'Tanda Tangan Wali Nagari berhasil dihapus.');
    }

    /**
     * Upload / Ganti Stempel Resmi Nagari
     */
    public function updateStempel(Request $request): RedirectResponse
    {
        $request->validate([
            'stempel' => 'required|file|image|mimes:png,jpg,jpeg,webp|max:3072',
        ], [
            'stempel.required' => 'File stempel nagari wajib diunggah.',
            'stempel.mimes' => 'Format file stempel harus PNG, JPG, atau JPEG (Disarankan PNG Transparan).',
            'stempel.max' => 'Ukuran file stempel maksimal 3MB.',
        ]);

        if ($request->hasFile('stempel')) {
            $file = $request->file('stempel');
            $path = $file->store('stempel', 'public');
            Pengaturan::set('stempel_nagari_path', 'storage/' . $path, 'ttd_stempel', 'file', 'Path Stempel Resmi Nagari');
        }

        return back()->with('success', 'Stempel resmi Nagari Koto Malintang berhasil diunggah dan disimpan.');
    }

    /**
     * Hapus Stempel
     */
    public function deleteStempel(): RedirectResponse
    {
        Pengaturan::set('stempel_nagari_path', '', 'ttd_stempel');
        return back()->with('success', 'Stempel Nagari berhasil dihapus.');
    }
}
