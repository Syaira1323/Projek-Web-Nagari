<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan form Login
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses Login (Mendukung Login via Username, Email, atau NIK)
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_id.required' => 'Email, Username, atau NIK wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($credentials['login_id']);
        $password = $credentials['password'];
        $remember = $request->filled('remember');

        // Cari pengguna berdasarkan Username, Email, atau NIK
        $user = User::where('username', strtolower($loginInput))
            ->orWhere('username', $loginInput)
            ->orWhere('email', strtolower($loginInput))
            ->orWhere('email', $loginInput)
            ->orWhere('nik', $loginInput)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user)
                ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withErrors([
            'login_id' => 'Akun (Username/Email/NIK) atau Password yang Anda masukkan tidak cocok.',
        ])->withInput($request->only('login_id', 'remember'));
    }

    /**
     * Tampilkan form Registrasi Masyarakat
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Proses Registrasi Masyarakat Baru
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:50|unique:users,username',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-) dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan, silakan pilih username lain.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi agar mudah dihubungi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $username = strtolower(trim($validated['username']));
        $email = !empty($validated['email']) ? strtolower(trim($validated['email'])) : $username . '@warga.kotomalintang.id';

        // Cek jika email auto-generated bentrok
        if (User::where('email', $email)->exists()) {
            $email = $username . '_' . time() . '@warga.kotomalintang.id';
        }

        $user = User::create([
            'name' => trim($validated['name']),
            'username' => $username,
            'email' => $email,
            'phone' => trim($validated['phone']),
            'address' => $validated['address'] ?? 'Nagari Koto Malintang',
            'nik' => $request->input('nik'),
            'role' => 'warga',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('warga.dashboard')
            ->with('success', 'Pendaftaran akun berhasil! Selamat datang di Portal Layanan Nagari Koto Malintang.');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('success', 'Anda telah berhasil keluar (logout).');
    }

    /**
     * Redirect helper sesuai Role
     */
    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'wali_nagari' => redirect()->route('wali-nagari.dashboard'),
            default => redirect()->route('warga.dashboard'),
        };
    }
}
