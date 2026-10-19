<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGoogleDriveController extends Controller
{
    protected GoogleDriveService $gdriveService;

    public function __construct(GoogleDriveService $gdriveService)
    {
        $this->gdriveService = $gdriveService;
    }

    /**
     * Halaman Pengaturan Google Drive
     */
    public function index(): View
    {
        $clientId = Pengaturan::get('gdrive_client_id') ?: config('services.google.client_id', env('GOOGLE_CLIENT_ID', ''));
        $clientSecret = Pengaturan::get('gdrive_client_secret') ?: config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET', ''));
        $isConnected = $this->gdriveService->isConnected();
        $connectedEmail = Pengaturan::get('gdrive_account_email', '-');
        $rootFolderId = Pengaturan::get('gdrive_root_folder_id', '-');

        $redirectUri = route('admin.gdrive.callback');
        $authUrl = (!empty($clientId) && !empty($clientSecret)) ? $this->gdriveService->getAuthUrl($redirectUri) : null;

        return view('admin.gdrive.index', compact(
            'clientId',
            'clientSecret',
            'isConnected',
            'connectedEmail',
            'rootFolderId',
            'redirectUri',
            'authUrl'
        ));
    }

    /**
     * Simpan Kredensial OAuth Client ID & Secret
     */
    public function saveCredentials(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|string|max:500',
            'client_secret' => 'required|string|max:500',
        ], [
            'client_id.required' => 'Client ID Google OAuth wajib diisi.',
            'client_secret.required' => 'Client Secret Google OAuth wajib diisi.',
        ]);

        Pengaturan::set('gdrive_client_id', trim($validated['client_id']), 'gdrive');
        Pengaturan::set('gdrive_client_secret', trim($validated['client_secret']), 'gdrive');

        return back()->with('success', 'Kredensial Google OAuth berhasil disimpan. Silakan klik tombol "Hubungkan Akun Google Drive".');
    }

    /**
     * Callback dari Google OAuth
     */
    public function callback(Request $request): RedirectResponse
    {
        $code = $request->query('code');
        $error = $request->query('error');

        $frontendUrl = '/layanan-surat/frontend/public/Admin.html';

        if ($error) {
            return redirect($frontendUrl . '?gdrive_error=' . urlencode('Otorisasi Google Drive dibatalkan atau ditolak: ' . $error) . '#arsip');
        }

        if (!$code) {
            return redirect($frontendUrl . '?gdrive_error=' . urlencode('Kode otorisasi Google tidak ditemukan.') . '#arsip');
        }

        $redirectUri = route('admin.gdrive.callback');
        $result = $this->gdriveService->exchangeCode($code, $redirectUri);

        if ($result['success']) {
            return redirect($frontendUrl . '?gdrive=connected#arsip');
        }

        return redirect($frontendUrl . '?gdrive_error=' . urlencode('Gagal menghubungkan Google Drive: ' . $result['message']) . '#arsip');
    }

    /**
     * Uji Koneksi Google Drive
     */
    public function testConnection(): RedirectResponse
    {
        $res = $this->gdriveService->testConnection();

        if ($res['success']) {
            return back()->with('success', "Uji Koneksi Berhasil! Akun: {$res['email']} ({$res['displayName']}) - Penggunaan Kuota: {$res['used']} / {$res['total']}.");
        }

        return back()->with('warning', $res['message']);
    }

    /**
     * Putus Koneksi Google Drive
     */
    public function disconnect(): RedirectResponse
    {
        $this->gdriveService->disconnect();
        return back()->with('success', 'Koneksi Google Drive berhasil diputuskan.');
    }
}
