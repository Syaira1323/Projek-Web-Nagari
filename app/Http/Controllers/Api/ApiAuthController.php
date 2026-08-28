<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    /**
     * POST /api/login
     */
    public function login(Request $request): JsonResponse
    {
        $loginInput = trim($request->input('login') ?? $request->input('login_id') ?? $request->input('username') ?? $request->input('email') ?? $request->input('nik') ?? '');
        $password = $request->input('password', '');

        if (empty($loginInput) || empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username/Email/NIK dan Password wajib diisi.',
            ], 422);
        }

        $user = User::where('username', strtolower($loginInput))
            ->orWhere('username', $loginInput)
            ->orWhere('email', strtolower($loginInput))
            ->orWhere('email', $loginInput)
            ->orWhere('nik', $loginInput)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($request->hasSession()) {
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
            }

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'nik' => $user->nik,
                    'phone' => $user->phone,
                    'address' => $user->address,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Username/Email/NIK atau Kata Sandi yang dimasukkan salah.',
        ], 401);
    }

    /**
     * POST /api/register
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:50|unique:users,username',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email',
            'address' => 'nullable|string|max:500',
            'nik' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $username = strtolower(trim($validated['username']));
        $email = !empty($validated['email']) ? strtolower(trim($validated['email'])) : $username . '@warga.kotomalintang.id';

        if (User::where('email', $email)->exists()) {
            $email = $username . '_' . time() . '@warga.kotomalintang.id';
        }

        $user = User::create([
            'name' => trim($validated['name']),
            'username' => $username,
            'email' => $email,
            'phone' => trim($validated['phone']),
            'address' => $validated['address'] ?? 'Nagari Koto Malintang',
            'nik' => $validated['nik'] ?? null,
            'role' => 'warga',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran akun berhasil!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'nik' => $user->nik,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ], 201);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * GET /api/user
     */
    public function user(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Belum login.',
                'user' => null,
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'nik' => $user->nik,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ]);
    }

    /**
     * POST /api/password/update & PUT /api/profile/password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $curPwd = $request->input('current_password') ?? $request->input('password_lama');
        $newPwd = $request->input('password') ?? $request->input('password_baru');
        $confPwd = $request->input('password_confirmation') ?? $request->input('password_baru_confirmation');

        if (empty($curPwd)) {
            return response()->json(['success' => false, 'message' => 'Password saat ini / password lama wajib diisi.'], 422);
        }

        if (empty($newPwd) || strlen($newPwd) < 6) {
            return response()->json(['success' => false, 'message' => 'Password baru minimal 6 karakter.'], 422);
        }

        if ($newPwd !== $confPwd) {
            return response()->json(['success' => false, 'message' => 'Konfirmasi password baru tidak cocok.'], 422);
        }

        if ($curPwd === $newPwd) {
            return response()->json(['success' => false, 'message' => 'Password baru harus berbeda dari password lama.'], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!Hash::check($curPwd, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini / password lama yang dimasukkan salah.',
            ], 422);
        }

        $user->password = Hash::make($newPwd);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui di database.',
        ]);
    }
}
