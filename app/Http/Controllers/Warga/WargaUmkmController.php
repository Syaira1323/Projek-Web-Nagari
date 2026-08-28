<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\UMKM;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WargaUmkmController extends Controller
{
    /**
     * Tampilkan daftar UMKM milik warga yang login
     */
    public function index(): View
    {
        $user = Auth::user();
        $umkms = UMKM::where('user_id', $user->id)->latest()->get();

        return view('warga.umkm.index', compact('umkms'));
    }

    /**
     * Form tambah UMKM baru
     */
    public function create(): View
    {
        $user = Auth::user();

        return view('warga.umkm.create', compact('user'));
    }

    /**
     * Simpan UMKM baru milik warga
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'whatsapp' => 'nullable|string|max:30',
            'instagram' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|url|max:500',
            'jam_operasional' => 'nullable|string|max:100',
            'tahun_berdiri' => 'nullable|string|max:10',
            'foto' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'nama_usaha.required' => 'Nama usaha wajib diisi.',
            'pemilik.required' => 'Nama pemilik usaha wajib diisi.',
            'kategori.required' => 'Kategori usaha wajib dipilih.',
            'deskripsi.required' => 'Deskripsi produk atau usaha wajib diisi.',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'umkm_warga_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/umkm'), $filename);
            $validated['foto'] = 'uploads/umkm/' . $filename;
        }

        $validated['user_id'] = Auth::id();

        UMKM::create($validated);

        return redirect()->route('warga.umkm.index')
            ->with('success', 'UMKM Anda beserta foto berhasil didaftarkan dan kini tampil di katalog UMKM Nagari!');
    }

    /**
     * Form edit UMKM
     */
    public function edit(UMKM $umkm): View
    {
        if ($umkm->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit UMKM ini.');
        }

        return view('warga.umkm.edit', compact('umkm'));
    }

    /**
     * Update UMKM
     */
    public function update(Request $request, UMKM $umkm): RedirectResponse
    {
        if ($umkm->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit UMKM ini.');
        }

        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'whatsapp' => 'nullable|string|max:30',
            'instagram' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|url|max:500',
            'jam_operasional' => 'nullable|string|max:100',
            'tahun_berdiri' => 'nullable|string|max:10',
            'foto' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'umkm_warga_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/umkm'), $filename);
            $validated['foto'] = 'uploads/umkm/' . $filename;
        } else {
            unset($validated['foto']);
        }

        $umkm->update($validated);

        return redirect()->route('warga.umkm.index')
            ->with('success', 'Data UMKM Anda berhasil diperbarui!');
    }

    /**
     * Hapus UMKM
     */
    public function destroy(UMKM $umkm): RedirectResponse
    {
        if ($umkm->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus UMKM ini.');
        }

        $umkm->delete();

        return redirect()->route('warga.umkm.index')
            ->with('success', 'Data UMKM berhasil dihapus.');
    }
}
