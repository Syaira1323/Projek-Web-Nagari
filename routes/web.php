<?php

use App\Http\Controllers\AdatBudayaNagariController;
use App\Http\Controllers\Admin\AdminArsipController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminGoogleDriveController;
use App\Http\Controllers\Admin\AdminKontenController;
use App\Http\Controllers\Admin\AdminPengaturanController;
use App\Http\Controllers\Admin\AdminSuratController;
use App\Http\Controllers\Admin\AdminWargaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KabaNagariController;
use App\Http\Controllers\LayananSuratController;
use App\Http\Controllers\ObjekWisataController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PerangkatNagariController;
use App\Http\Controllers\ProfilNagariController;
use App\Http\Controllers\SejarahNagariController;
use App\Http\Controllers\StrukturNagariController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\VisiMisiController;
use App\Http\Controllers\WaliNagari\WaliNagariDashboardController;
use App\Http\Controllers\WaliNagari\WaliNagariSuratController;
use App\Http\Controllers\WaliNagari\WaliNagariTtdController;
use App\Http\Controllers\Warga\WargaAiController;
use App\Http\Controllers\Warga\WargaDashboardController;
use App\Http\Controllers\Warga\WargaProfilController;
use App\Http\Controllers\Warga\WargaSuratController;
use App\Http\Controllers\Warga\WargaUmkmController;
use App\Models\KabaNagari;
use App\Models\PerangkatNagari;
use App\Models\ProfilNagari;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Layanan Surat Nagari Koto Malintang
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES (PORTAL NAGARI)
// ==========================================
Route::get('/', function () {
    $profil = ProfilNagari::first();
    $kabaTerbaru = KabaNagari::latest()->take(3)->get();
    $totalPerangkat = PerangkatNagari::count();

    return view('welcome', compact('profil', 'kabaTerbaru', 'totalPerangkat'));
})->name('beranda');

Route::get('/profil-nagari', [ProfilNagariController::class, 'index'])->name('profil-nagari');
Route::get('/adat-budaya-nagari', [AdatBudayaNagariController::class, 'index'])->name('adat-budaya-nagari');
Route::get('/sejarah', [SejarahNagariController::class, 'index'])->name('sejarah');
Route::get('/visi-misi', [VisiMisiController::class, 'index'])->name('visi-misi');
Route::get('/perangkat-nagari', [PerangkatNagariController::class, 'index'])->name('perangkat-nagari');
Route::get('/struktur-nagari', [StrukturNagariController::class, 'index'])->name('struktur-nagari');
Route::get('/objek-wisata', [ObjekWisataController::class, 'index'])->name('objek-wisata');
Route::get('/kaba-nagari', [KabaNagariController::class, 'index'])->name('kaba-nagari');
Route::get('/umkm', [UmkmController::class, 'index'])->name('umkm');
Route::get('/layanan-surat', [LayananSuratController::class, 'index'])->name('layanan-surat');

// ==========================================
// 2. AUTHENTICATION & PASSWORD ROUTES
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/password/update', [PasswordController::class, 'update'])->middleware('auth')->name('password.update');

// ==========================================
// 3. WARGA PORTAL (Role: warga, admin, wali_nagari)
// ==========================================
Route::middleware(['auth', 'role:warga,admin,wali_nagari'])->prefix('warga')->name('warga.')->group(function () {
    Route::get('/dashboard', [WargaDashboardController::class, 'index'])->name('dashboard');

    // Profil Saya
    Route::get('/profil', [WargaProfilController::class, 'index'])->name('profil');
    Route::put('/profil', [WargaProfilController::class, 'update'])->name('profil.update');

    // Bantuan & AI Assistant
    Route::get('/bantuan', [WargaAiController::class, 'index'])->name('bantuan');
    Route::post('/bantuan/chat', [WargaAiController::class, 'chat'])->name('bantuan.chat');

    // Permohonan Surat & Berkas Persyaratan
    Route::get('/surat', [WargaSuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/create', [WargaSuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [WargaSuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/{surat}', [WargaSuratController::class, 'show'])->name('surat.show');
    Route::get('/surat/{surat}/print', [WargaSuratController::class, 'print'])->name('surat.print');
    Route::get('/berkas/{berkas}/download', [WargaSuratController::class, 'downloadBerkas'])->name('berkas.download');

    // Kelola UMKM Milik Warga
    Route::get('/umkm', [WargaUmkmController::class, 'index'])->name('umkm.index');
    Route::get('/umkm/create', [WargaUmkmController::class, 'create'])->name('umkm.create');
    Route::post('/umkm', [WargaUmkmController::class, 'store'])->name('umkm.store');
    Route::get('/umkm/{umkm}/edit', [WargaUmkmController::class, 'edit'])->name('umkm.edit');
    Route::put('/umkm/{umkm}', [WargaUmkmController::class, 'update'])->name('umkm.update');
    Route::delete('/umkm/{umkm}', [WargaUmkmController::class, 'destroy'])->name('umkm.destroy');

    // Ubah Password Warga
    Route::get('/ubah-password', [PasswordController::class, 'showWarga'])->name('password.index');
});

// ==========================================
// 4. ADMIN PANEL (Role: admin)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Verifikasi Surat Masuk
    Route::get('/surat', [AdminSuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/{surat}', [AdminSuratController::class, 'show'])->name('surat.show');
    Route::post('/surat/{surat}/ai-generate', [AdminSuratController::class, 'generateAi'])->name('surat.ai-generate');
    Route::post('/surat/{surat}/verify', [AdminSuratController::class, 'verify'])->name('surat.verify');
    Route::post('/surat/{surat}/reject', [AdminSuratController::class, 'reject'])->name('surat.reject');

    // Data Warga
    Route::get('/warga', [AdminWargaController::class, 'index'])->name('warga.index');

    // Google Drive Integration
    Route::get('/gdrive', [AdminGoogleDriveController::class, 'index'])->name('gdrive.index');
    Route::post('/gdrive/credentials', [AdminGoogleDriveController::class, 'saveCredentials'])->name('gdrive.save-credentials');
    Route::get('/gdrive/callback', [AdminGoogleDriveController::class, 'callback'])->name('gdrive.callback');
    Route::post('/gdrive/test', [AdminGoogleDriveController::class, 'testConnection'])->name('gdrive.test');
    Route::post('/gdrive/disconnect', [AdminGoogleDriveController::class, 'disconnect'])->name('gdrive.disconnect');

    // Pengaturan Nagari
    Route::get('/pengaturan', [AdminPengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan', [AdminPengaturanController::class, 'update'])->name('pengaturan.update');

    // Manajemen Arsip Surat & Cadangan
    Route::get('/arsip', [AdminArsipController::class, 'index'])->name('arsip.index');
    Route::get('/arsip/export-csv', [AdminArsipController::class, 'exportCsv'])->name('arsip.export-csv');
    Route::get('/arsip/backup-json', [AdminArsipController::class, 'backupJson'])->name('arsip.backup-json');

    // Manajemen Konten CMS & Gambar
    Route::prefix('konten')->name('konten.')->group(function () {
        // Profil
        Route::get('/profil', [AdminKontenController::class, 'profil'])->name('profil');
        Route::put('/profil', [AdminKontenController::class, 'updateProfil'])->name('profil.update');

        // Adat & Budaya
        Route::get('/adat', [AdminKontenController::class, 'adat'])->name('adat');
        Route::put('/adat', [AdminKontenController::class, 'updateAdat'])->name('adat.update');

        // Visi & Misi
        Route::get('/visi-misi', [AdminKontenController::class, 'visiMisi'])->name('visi-misi');
        Route::put('/visi-misi', [AdminKontenController::class, 'updateVisiMisi'])->name('visi-misi.update');

        // Sejarah
        Route::get('/sejarah', [AdminKontenController::class, 'sejarah'])->name('sejarah');
        Route::post('/sejarah', [AdminKontenController::class, 'storeSejarah'])->name('sejarah.store');
        Route::put('/sejarah/{sejarah}', [AdminKontenController::class, 'updateSejarah'])->name('sejarah.update');
        Route::delete('/sejarah/{sejarah}', [AdminKontenController::class, 'destroySejarah'])->name('sejarah.destroy');

        // Perangkat
        Route::get('/perangkat', [AdminKontenController::class, 'perangkat'])->name('perangkat');
        Route::post('/perangkat', [AdminKontenController::class, 'storePerangkat'])->name('perangkat.store');
        Route::put('/perangkat/{perangkat}', [AdminKontenController::class, 'updatePerangkat'])->name('perangkat.update');
        Route::delete('/perangkat/{perangkat}', [AdminKontenController::class, 'destroyPerangkat'])->name('perangkat.destroy');

        // Struktur
        Route::get('/struktur', [AdminKontenController::class, 'struktur'])->name('struktur');
        Route::post('/struktur', [AdminKontenController::class, 'storeStruktur'])->name('struktur.store');
        Route::put('/struktur/{struktur}', [AdminKontenController::class, 'updateStruktur'])->name('struktur.update');
        Route::delete('/struktur/{struktur}', [AdminKontenController::class, 'destroyStruktur'])->name('struktur.destroy');

        // Wisata
        Route::get('/wisata', [AdminKontenController::class, 'wisata'])->name('wisata');
        Route::post('/wisata', [AdminKontenController::class, 'storeWisata'])->name('wisata.store');
        Route::put('/wisata/{wisata}', [AdminKontenController::class, 'updateWisata'])->name('wisata.update');
        Route::delete('/wisata/{wisata}', [AdminKontenController::class, 'destroyWisata'])->name('wisata.destroy');

        // Kaba Nagari
        Route::get('/kaba', [AdminKontenController::class, 'kaba'])->name('kaba');
        Route::post('/kaba', [AdminKontenController::class, 'storeKaba'])->name('kaba.store');
        Route::put('/kaba/{kaba}', [AdminKontenController::class, 'updateKaba'])->name('kaba.update');
        Route::delete('/kaba/{kaba}', [AdminKontenController::class, 'destroyKaba'])->name('kaba.destroy');

        // UMKM
        Route::get('/umkm', [AdminKontenController::class, 'umkm'])->name('umkm');
        Route::post('/umkm', [AdminKontenController::class, 'storeUmkm'])->name('umkm.store');
        Route::put('/umkm/{umkm}', [AdminKontenController::class, 'updateUmkm'])->name('umkm.update');
        Route::delete('/umkm/{umkm}', [AdminKontenController::class, 'destroyUmkm'])->name('umkm.destroy');
    });

    // Ubah Password Admin
    Route::get('/ubah-password', [PasswordController::class, 'showAdmin'])->name('password.index');
});

// ==========================================
// 5. WALI NAGARI PANEL (Role: wali_nagari, admin)
// ==========================================
Route::middleware(['auth', 'role:wali_nagari,admin'])->prefix('wali-nagari')->name('wali-nagari.')->group(function () {
    Route::get('/', [WaliNagariDashboardController::class, 'index'])->name('dashboard');

    // Persetujuan & Pembubuhan Stempel
    Route::get('/surat', [WaliNagariSuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/{surat}', [WaliNagariSuratController::class, 'show'])->name('surat.show');
    Route::post('/surat/{surat}/approve', [WaliNagariSuratController::class, 'approve'])->name('surat.approve');
    Route::post('/surat/{surat}/reject', [WaliNagariSuratController::class, 'reject'])->name('surat.reject');

    // Kelola Tanda Tangan & Stempel Resmi
    Route::get('/ttd-stempel', [WaliNagariTtdController::class, 'index'])->name('ttd-stempel.index');
    Route::post('/ttd-stempel/ttd', [WaliNagariTtdController::class, 'updateTtd'])->name('ttd-stempel.update-ttd');
    Route::delete('/ttd-stempel/ttd', [WaliNagariTtdController::class, 'deleteTtd'])->name('ttd-stempel.delete-ttd');
    Route::post('/ttd-stempel/stempel', [WaliNagariTtdController::class, 'updateStempel'])->name('ttd-stempel.update-stempel');
    Route::delete('/ttd-stempel/stempel', [WaliNagariTtdController::class, 'deleteStempel'])->name('ttd-stempel.delete-stempel');

    // Ubah Password Wali Nagari
    Route::get('/ubah-password', [PasswordController::class, 'showWali'])->name('password.index');
});
