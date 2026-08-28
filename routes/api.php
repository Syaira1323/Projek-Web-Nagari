<?php

use App\Http\Controllers\Api\ApiAdminController;
use App\Http\Controllers\Api\ApiAdminKontenController;
use App\Http\Controllers\Api\ApiAiController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiPublicContentController;
use App\Http\Controllers\Api\ApiWaliNagariController;
use App\Http\Controllers\Api\ApiWargaSuratController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes — Layanan Surat Nagari Koto Malintang
|--------------------------------------------------------------------------
| Menyediakan JSON API untuk Frontend Warga, Admin, Wali Nagari di D:\
| Terhubung langsung ke MySQL nagari_koto_malintang dan AI Hugging Face
*/

// =========================================================================
// 1. AUTENTIKASI & AKUN PENGGUNA
// =========================================================================
Route::middleware(\Illuminate\Session\Middleware\StartSession::class)->group(function () {
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    Route::post('/password/update', [ApiAuthController::class, 'updatePassword']);
    Route::put('/profile/password', [ApiAuthController::class, 'updatePassword']);
});

// =========================================================================
// 2. KONTEN PUBLIK RESMI NAGARI DARI DATABASE
// =========================================================================
Route::get('/profil-nagari', [ApiPublicContentController::class, 'getProfil']);
Route::get('/perangkat-nagari', [ApiPublicContentController::class, 'getPerangkat']);
Route::get('/visi-misi', [ApiPublicContentController::class, 'getVisiMisi']);
Route::get('/sejarah', [ApiPublicContentController::class, 'getSejarah']);
Route::get('/adat-budaya', [ApiPublicContentController::class, 'getAdatBudaya']);
Route::get('/struktur-nagari', [ApiPublicContentController::class, 'getStruktur']);
Route::get('/umkm', [ApiPublicContentController::class, 'getUmkm']);
Route::get('/objek-wisata', [ApiPublicContentController::class, 'getWisata']);
Route::get('/kaba-nagari', [ApiPublicContentController::class, 'getKaba']);
Route::get('/jenis-surat', [ApiPublicContentController::class, 'getJenisSurat']);
Route::get('/persyaratan-surat', [ApiPublicContentController::class, 'getJenisSurat']);

// =========================================================================
// 3. INTEGRASI AI HUGGING FACE LAYANAN SURAT
// =========================================================================
Route::post('/ai/surat', [ApiAiController::class, 'suratAi']);
Route::post('/ai/chat', [ApiAiController::class, 'chat']);

// =========================================================================
// 4. PENGAJUAN & STATUS SURAT WARGA
// =========================================================================
Route::post('/pengajuan-surat', [ApiWargaSuratController::class, 'store']);
Route::get('/pengajuan-surat', [ApiWargaSuratController::class, 'index']);
Route::get('/pengajuan-surat/{id}', [ApiWargaSuratController::class, 'show']);

// =========================================================================
// 5. NOTIFIKASI SURAT WARGA
// =========================================================================
Route::get('/notifikasi', [ApiWargaSuratController::class, 'getNotifications']);
Route::patch('/notifikasi/{id}/read', [ApiWargaSuratController::class, 'markNotificationRead']);
Route::patch('/notifikasi/read-all', [ApiWargaSuratController::class, 'markAllNotificationsRead']);

// =========================================================================
// 6. MANAJEMEN UMKM WARGA
// =========================================================================
Route::get('/umkm/my', [ApiWargaSuratController::class, 'getMyUmkm']);
Route::post('/umkm', [ApiWargaSuratController::class, 'storeUmkm']);
Route::get('/umkm/{id}', [ApiWargaSuratController::class, 'showUmkm']);
Route::put('/umkm/{id}', [ApiWargaSuratController::class, 'updateUmkm']);
Route::delete('/umkm/{id}', [ApiWargaSuratController::class, 'deleteUmkm']);

Route::prefix('warga')->group(function () {
    Route::get('/dashboard', [ApiWargaSuratController::class, 'dashboard']);
    Route::get('/profil', [ApiWargaSuratController::class, 'getProfil']);
    Route::put('/profil', [ApiWargaSuratController::class, 'updateProfil']);
    Route::get('/surat', [ApiWargaSuratController::class, 'index']);
    Route::post('/surat', [ApiWargaSuratController::class, 'store']);
    Route::get('/surat/{id}', [ApiWargaSuratController::class, 'show']);
    Route::get('/notifikasi', [ApiWargaSuratController::class, 'getNotifications']);
    Route::get('/umkm', [ApiWargaSuratController::class, 'getMyUmkm']);
    Route::post('/umkm', [ApiWargaSuratController::class, 'storeUmkm']);
});

// =========================================================================
// 7. PANEL ADMINISTRATOR
// =========================================================================
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [ApiAdminController::class, 'dashboard']);
    Route::get('/surat', [ApiAdminController::class, 'suratList']);
    Route::get('/surat/{id}', [ApiAdminController::class, 'suratDetail']);
    Route::post('/surat/{id}/verify', [ApiAdminController::class, 'verifySurat']);
    Route::post('/surat/{id}/reject', [ApiAdminController::class, 'rejectSurat']);
    Route::post('/surat/{id}/ai-generate', [ApiAdminController::class, 'generateAi']);
    Route::get('/warga', [ApiAdminController::class, 'wargaList']);
    Route::get('/gdrive', [ApiAdminController::class, 'gdriveStatus']);
    Route::post('/gdrive/disconnect', [ApiAdminController::class, 'gdriveDisconnect']);
    Route::get('/pengaturan', [ApiAdminController::class, 'getPengaturan']);
    Route::put('/pengaturan', [ApiAdminController::class, 'updatePengaturan']);
    Route::get('/konten', [ApiAdminKontenController::class, 'index']);
    Route::get('/konten/profil', [ApiAdminKontenController::class, 'getProfil']);
    Route::put('/konten/profil', [ApiAdminKontenController::class, 'updateProfil']);
    Route::middleware([\Illuminate\Session\Middleware\StartSession::class, 'auth', 'role:admin'])->group(function () {
        Route::get('/kaba-nagari', [ApiAdminKontenController::class, 'kabaList']);
        Route::post('/kaba-nagari', [ApiAdminKontenController::class, 'kabaStore']);
        Route::get('/kaba-nagari/{id}', [ApiAdminKontenController::class, 'kabaShow']);
        Route::put('/kaba-nagari/{id}', [ApiAdminKontenController::class, 'kabaUpdate']);
        Route::delete('/kaba-nagari/{id}', [ApiAdminKontenController::class, 'kabaDestroy']);
        Route::get('/kaba-nagari/{id}/gambar', [ApiAdminKontenController::class, 'kabaGambarList']);
        Route::post('/kaba-nagari/{id}/gambar', [ApiAdminKontenController::class, 'kabaGambarStore']);
        Route::delete('/kaba-nagari/{id}/gambar/{gambarId}', [ApiAdminKontenController::class, 'kabaGambarDestroy']);
    });
});

// =========================================================================
// 8. PANEL WALI NAGARI (Persetujuan, TTD & Stempel Resmi)
// =========================================================================
Route::prefix('wali-nagari')->group(function () {
    Route::get('/profile', [ApiWaliNagariController::class, 'getProfile']);
    Route::get('/dashboard', [ApiWaliNagariController::class, 'dashboard']);
    Route::get('/surat', [ApiWaliNagariController::class, 'suratList']);
    Route::get('/surat/{id}', [ApiWaliNagariController::class, 'suratDetail']);
    Route::post('/surat/{id}/approve', [ApiWaliNagariController::class, 'approve']);
    Route::post('/surat/{id}/reject', [ApiWaliNagariController::class, 'reject']);
    Route::get('/ttd-stempel', [ApiWaliNagariController::class, 'getTtdStempel']);
    Route::post('/tanda-tangan', [ApiWaliNagariController::class, 'uploadTtd']);
    Route::delete('/tanda-tangan', [ApiWaliNagariController::class, 'deleteTtd']);
    Route::post('/stempel', [ApiWaliNagariController::class, 'uploadStempel']);
    Route::delete('/stempel', [ApiWaliNagariController::class, 'deleteStempel']);
});
