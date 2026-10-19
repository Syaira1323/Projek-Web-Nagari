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
| Menyediakan JSON API untuk Frontend Warga, Admin.html, dan Wali Nagari
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
Route::get('/profil', [ApiPublicContentController::class, 'getProfil']);
Route::get('/perangkat-nagari', [ApiPublicContentController::class, 'getPerangkat']);
Route::get('/perangkat', [ApiPublicContentController::class, 'getPerangkat']);
Route::get('/visi-misi', [ApiPublicContentController::class, 'getVisiMisi']);
Route::get('/visi', [ApiPublicContentController::class, 'getVisiMisi']);
Route::get('/sejarah', [ApiPublicContentController::class, 'getSejarah']);
Route::get('/adat-budaya', [ApiPublicContentController::class, 'getAdatBudaya']);
Route::get('/adat-budaya-nagari', [ApiPublicContentController::class, 'getAdatBudaya']);
Route::get('/struktur-nagari', [ApiPublicContentController::class, 'getStruktur']);
Route::get('/struktur', [ApiPublicContentController::class, 'getStruktur']);
Route::get('/umkm', [ApiPublicContentController::class, 'getUmkm']);
Route::get('/objek-wisata', [ApiPublicContentController::class, 'getWisata']);
Route::get('/wisata', [ApiPublicContentController::class, 'getWisata']);
Route::get('/kaba-nagari', [ApiPublicContentController::class, 'getKaba']);
Route::get('/kaba-nagari/{id}', [ApiPublicContentController::class, 'getKabaDetail']);
Route::get('/kaba', [ApiPublicContentController::class, 'getKaba']);
Route::get('/galeri', [ApiPublicContentController::class, 'getGaleri']);
Route::get('/galeri-media', [ApiPublicContentController::class, 'getGaleri']);
Route::get('/jenis-surat', [ApiPublicContentController::class, 'getJenisSurat']);
Route::get('/persyaratan-surat', [ApiPublicContentController::class, 'getJenisSurat']);

// =========================================================================
// 3. INTEGRASI AI (Hugging Face Chat & FastAPI DistilBERT Surat)
// =========================================================================
Route::post('/ai/rekomendasi-surat', [ApiAiController::class, 'rekomendasiSurat']);
Route::post('/ai/surat', [ApiAiController::class, 'suratAi']);
Route::post('/ai/chat', [ApiAiController::class, 'chat']);

// =========================================================================
// 4. PENGAJUAN & STATUS SURAT WARGA (Session Enabled)
// =========================================================================
Route::middleware(\Illuminate\Session\Middleware\StartSession::class)->group(function () {
    Route::post('/pengajuan-surat', [ApiWargaSuratController::class, 'store']);
    Route::post('/pengajuan-surat/{id}/revisi', [ApiWargaSuratController::class, 'revisi']);
    Route::get('/pengajuan-surat', [ApiWargaSuratController::class, 'index']);
    Route::get('/pengajuan-surat/{id}', [ApiWargaSuratController::class, 'show']);
    Route::get('/pengajuan-surat/{id}/download-pdf', [ApiWargaSuratController::class, 'downloadPdf']);

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

    Route::get('/berkas/{berkas}/download', [\App\Http\Controllers\Warga\WargaSuratController::class, 'downloadBerkas']);

    Route::prefix('warga')->group(function () {
        Route::get('/dashboard', [ApiWargaSuratController::class, 'dashboard']);
        Route::get('/profil', [ApiWargaSuratController::class, 'getProfil']);
        Route::put('/profil', [ApiWargaSuratController::class, 'updateProfil']);
        Route::get('/surat', [ApiWargaSuratController::class, 'index']);
        Route::post('/surat', [ApiWargaSuratController::class, 'store']);
        Route::get('/surat/{id}', [ApiWargaSuratController::class, 'show']);
        Route::get('/surat/{id}/download-pdf', [ApiWargaSuratController::class, 'downloadPdf']);
        Route::get('/berkas/{berkas}/download', [\App\Http\Controllers\Warga\WargaSuratController::class, 'downloadBerkas']);
        Route::get('/notifikasi', [ApiWargaSuratController::class, 'getNotifications']);
        Route::get('/umkm', [ApiWargaSuratController::class, 'getMyUmkm']);
        Route::post('/umkm', [ApiWargaSuratController::class, 'storeUmkm']);
    });
});

// =========================================================================
// 7. PANEL ADMINISTRATOR (Untuk Admin.html Frontend)
// =========================================================================
Route::middleware(\Illuminate\Session\Middleware\StartSession::class)->prefix('admin')->group(function () {
    // Auth & Info
    Route::get('/me', [ApiAuthController::class, 'user']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [ApiAdminController::class, 'dashboard']);

    // Verifikasi Surat
    Route::get('/surat', [ApiAdminController::class, 'suratList']);
    Route::get('/surat/{id}', [ApiAdminController::class, 'suratDetail']);
    Route::post('/surat/{id}/verify', [ApiAdminController::class, 'verifySurat']);
    Route::post('/surat/{id}/minta-revisi', [ApiAdminController::class, 'mintaRevisi']);
    Route::post('/surat/{id}/reject', [ApiAdminController::class, 'rejectSurat']);
    Route::post('/surat/{id}/ai-generate', [ApiAdminController::class, 'generateAi']);

    // Warga
    Route::get('/warga', [ApiAdminController::class, 'wargaList']);

    // Arsip
    Route::get('/arsip', [ApiAdminController::class, 'arsipList']);

    // Google Drive
    Route::get('/gdrive', [ApiAdminController::class, 'gdriveStatus']);
    Route::post('/gdrive/disconnect', [ApiAdminController::class, 'gdriveDisconnect']);

    // Pengaturan
    Route::get('/pengaturan', [ApiAdminController::class, 'getPengaturan']);
    Route::post('/pengaturan', [ApiAdminController::class, 'updatePengaturan']);
    Route::put('/pengaturan', [ApiAdminController::class, 'updatePengaturan']);

    // CMS Konten Ringkasan
    Route::get('/konten', [ApiAdminKontenController::class, 'index']);

    // 1. Profil
    Route::get('/konten/profil', [ApiAdminKontenController::class, 'getProfil']);
    Route::post('/konten/profil', [ApiAdminKontenController::class, 'updateProfil']);
    Route::put('/konten/profil', [ApiAdminKontenController::class, 'updateProfil']);

    // 2. Adat & Budaya
    Route::get('/konten/adat', [ApiAdminKontenController::class, 'getAdat']);
    Route::post('/konten/adat', [ApiAdminKontenController::class, 'updateAdat']);
    Route::put('/konten/adat', [ApiAdminKontenController::class, 'updateAdat']);

    // 3. Visi & Misi
    Route::get('/konten/visi-misi', [ApiAdminKontenController::class, 'getVisiMisi']);
    Route::post('/konten/visi-misi', [ApiAdminKontenController::class, 'updateVisiMisi']);
    Route::put('/konten/visi-misi', [ApiAdminKontenController::class, 'updateVisiMisi']);

    // 4. Sejarah Nagari
    Route::get('/konten/sejarah', [ApiAdminKontenController::class, 'getSejarah']);
    Route::post('/konten/sejarah', [ApiAdminKontenController::class, 'storeSejarah']);
    Route::post('/konten/sejarah/{id}', [ApiAdminKontenController::class, 'updateSejarah']);
    Route::put('/konten/sejarah/{id}', [ApiAdminKontenController::class, 'updateSejarah']);
    Route::delete('/konten/sejarah/{id}', [ApiAdminKontenController::class, 'destroySejarah']);

    // 5. Perangkat Nagari
    Route::get('/konten/perangkat', [ApiAdminKontenController::class, 'getPerangkat']);
    Route::post('/konten/perangkat', [ApiAdminKontenController::class, 'storePerangkat']);
    Route::post('/konten/perangkat/{id}', [ApiAdminKontenController::class, 'updatePerangkat']);
    Route::put('/konten/perangkat/{id}', [ApiAdminKontenController::class, 'updatePerangkat']);
    Route::delete('/konten/perangkat/{id}', [ApiAdminKontenController::class, 'destroyPerangkat']);

    // 6. Struktur Nagari
    Route::get('/konten/struktur', [ApiAdminKontenController::class, 'getStruktur']);
    Route::post('/konten/struktur', [ApiAdminKontenController::class, 'storeStruktur']);
    Route::post('/konten/struktur/{id}', [ApiAdminKontenController::class, 'updateStruktur']);
    Route::put('/konten/struktur/{id}', [ApiAdminKontenController::class, 'updateStruktur']);
    Route::delete('/konten/struktur/{id}', [ApiAdminKontenController::class, 'destroyStruktur']);

    // 7. Objek Wisata
    Route::get('/konten/wisata', [ApiAdminKontenController::class, 'getWisata']);
    Route::post('/konten/wisata', [ApiAdminKontenController::class, 'storeWisata']);
    Route::post('/konten/wisata/{id}', [ApiAdminKontenController::class, 'updateWisata']);
    Route::put('/konten/wisata/{id}', [ApiAdminKontenController::class, 'updateWisata']);
    Route::delete('/konten/wisata/{id}', [ApiAdminKontenController::class, 'destroyWisata']);

    // 8. UMKM Nagari
    Route::get('/konten/umkm', [ApiAdminKontenController::class, 'getUmkm']);
    Route::post('/konten/umkm', [ApiAdminKontenController::class, 'storeUmkm']);
    Route::post('/konten/umkm/{id}', [ApiAdminKontenController::class, 'updateUmkm']);
    Route::put('/konten/umkm/{id}', [ApiAdminKontenController::class, 'updateUmkm']);
    Route::delete('/konten/umkm/{id}', [ApiAdminKontenController::class, 'destroyUmkm']);

    // 9. Kaba Nagari (Berita & Pengumuman)
    Route::get('/konten/kaba', [ApiAdminKontenController::class, 'kabaList']);
    Route::get('/kaba-nagari', [ApiAdminKontenController::class, 'kabaList']);
    Route::post('/konten/kaba', [ApiAdminKontenController::class, 'kabaStore']);
    Route::post('/kaba-nagari', [ApiAdminKontenController::class, 'kabaStore']);
    Route::get('/konten/kaba/{id}', [ApiAdminKontenController::class, 'kabaShow']);
    Route::get('/kaba-nagari/{id}', [ApiAdminKontenController::class, 'kabaShow']);
    Route::post('/konten/kaba/{id}', [ApiAdminKontenController::class, 'kabaUpdate']);
    Route::put('/konten/kaba/{id}', [ApiAdminKontenController::class, 'kabaUpdate']);
    Route::put('/kaba-nagari/{id}', [ApiAdminKontenController::class, 'kabaUpdate']);
    Route::delete('/konten/kaba/{id}', [ApiAdminKontenController::class, 'kabaDestroy']);
    Route::delete('/kaba-nagari/{id}', [ApiAdminKontenController::class, 'kabaDestroy']);
    Route::get('/kaba-nagari/{id}/gambar', [ApiAdminKontenController::class, 'kabaGambarList']);
    Route::post('/kaba-nagari/{id}/gambar', [ApiAdminKontenController::class, 'kabaGambarStore']);
    Route::delete('/kaba-nagari/{id}/gambar/{gambarId}', [ApiAdminKontenController::class, 'kabaGambarDestroy']);

    // 10. Galeri & Media
    Route::get('/konten/galeri', [ApiAdminKontenController::class, 'getGaleri']);
    Route::post('/konten/galeri', [ApiAdminKontenController::class, 'storeGaleri']);
    Route::post('/konten/galeri/{id}', [ApiAdminKontenController::class, 'updateGaleri']);
    Route::put('/konten/galeri/{id}', [ApiAdminKontenController::class, 'updateGaleri']);
    Route::delete('/konten/galeri/{id}', [ApiAdminKontenController::class, 'destroyGaleri']);
});

// =========================================================================
// 8. PANEL WALI NAGARI (Persetujuan, TTD & Stempel Resmi)
// =========================================================================
Route::prefix('wali-nagari')->group(function () {
    Route::get('/profile', [ApiWaliNagariController::class, 'getProfile']);
    Route::match(['PUT', 'POST', 'PATCH'], '/profile', [ApiWaliNagariController::class, 'forbiddenProfileUpdate']);
    Route::get('/dashboard', [ApiWaliNagariController::class, 'dashboard']);
    Route::get('/surat', [ApiWaliNagariController::class, 'suratList']);
    Route::get('/surat/{id}', [ApiWaliNagariController::class, 'suratDetail']);
    Route::post('/surat/{id}/approve', [ApiWaliNagariController::class, 'approve']);
    Route::post('/surat/{id}/kembalikan', [ApiWaliNagariController::class, 'kembalikan']);
    Route::post('/surat/{id}/reject', [ApiWaliNagariController::class, 'reject']);
    Route::get('/ttd-stempel', [ApiWaliNagariController::class, 'getTtdStempel']);
    Route::post('/tanda-tangan', [ApiWaliNagariController::class, 'uploadTtd']);
    Route::delete('/tanda-tangan', [ApiWaliNagariController::class, 'deleteTtd']);
    Route::post('/stempel', [ApiWaliNagariController::class, 'uploadStempel']);
    Route::delete('/stempel', [ApiWaliNagariController::class, 'deleteStempel']);
});
