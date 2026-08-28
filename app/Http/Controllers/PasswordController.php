<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Tampilkan Halaman Ubah Password Warga
     */
    public function showWarga(): View
    {
        return view('warga.ubah-password', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Tampilkan Halaman Ubah Password Admin
     */
    public function showAdmin(): View
    {
        return view('admin.ubah-password', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Tampilkan Halaman Ubah Password Wali Nagari
     */
    public function showWali(): View
    {
        return view('wali-nagari.ubah-password', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update Password untuk Pengguna yang Sedang Login (Warga, Admin, Wali Nagari)
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Password lama saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.different' => 'Password baru harus berbeda dari password lama.',
        ]);

        $user = Auth::user();

        // Verifikasi password lama
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.']);
        }

        // Update password baru
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Kata sandi akun Anda berhasil diperbarui! Silakan gunakan kata sandi baru untuk login berikutnya.');
    }
}
