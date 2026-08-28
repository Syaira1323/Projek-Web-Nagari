<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WargaProfilController extends Controller
{
    /**
     * Halaman Profil Saya
     */
    public function index(): View
    {
        $user = Auth::user();
        return view('warga.profil', compact('user'));
    }

    /**
     * Update data profil warga
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'address.required' => 'Alamat jorong domisili wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini telah digunakan oleh akun lain.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Data profil Anda berhasil diperbarui di database nagari.');
    }
}
