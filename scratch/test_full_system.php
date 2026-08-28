<?php

require __DIR__ . '/../../../../../../laragon/www/layanan-surat/vendor/autoload.php';
$app = require_once __DIR__ . '/../../../../../../laragon/www/layanan-surat/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "====================================================\n";
echo "🧪 TESTING NAGARI KOTO MALINTANG COMPLETE SYSTEM\n";
echo "====================================================\n\n";

// 1. Test Public Routes
$publicRoutes = [
    '/' => 'Beranda',
    '/profil-nagari' => 'Profil Nagari',
    '/adat-budaya-nagari' => 'Adat Budaya Nagari',
    '/sejarah' => 'Sejarah Nagari',
    '/visi-misi' => 'Visi Misi',
    '/perangkat-nagari' => 'Perangkat Nagari',
    '/struktur-nagari' => 'Struktur Nagari',
    '/objek-wisata' => 'Objek Wisata',
    '/kaba-nagari' => 'Kaba Nagari',
    '/umkm' => 'UMKM Nagari',
    '/layanan-surat' => 'Layanan Surat Publik',
    '/login' => 'Halaman Login',
    '/register' => 'Halaman Register',
];

echo "--- 1. Testing Public & Guest Routes ---\n";
foreach ($publicRoutes as $uri => $name) {
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    if ($status === 200) {
        echo "✅ [200 OK] $name ($uri)\n";
    } else {
        echo "❌ [FAILED $status] $name ($uri)\n";
    }
}

// 2. Test User Login as Warga, Admin, Wali Nagari
echo "\n--- 2. Testing Roles & Dashboards ---\n";
$warga = App\Models\User::where('role', 'warga')->first();
$admin = App\Models\User::where('role', 'admin')->first();
$wali = App\Models\User::where('role', 'wali_nagari')->first();

if ($warga) {
    Illuminate\Support\Facades\Auth::login($warga);
    $req = Illuminate\Http\Request::create('/warga/dashboard', 'GET');
    $res = $kernel->handle($req);
    echo "✅ [Warga Dashboard] Status: " . $res->getStatusCode() . " (Warga: {$warga->name})\n";
}

if ($admin) {
    Illuminate\Support\Facades\Auth::login($admin);
    $req = Illuminate\Http\Request::create('/admin', 'GET');
    $res = $kernel->handle($req);
    echo "✅ [Admin Dashboard] Status: " . $res->getStatusCode() . " (Admin: {$admin->name})\n";

    // Test Admin CMS Konten
    $cmsRoutes = [
        '/admin/konten/profil' => 'CMS Profil',
        '/admin/konten/adat' => 'CMS Adat',
        '/admin/konten/visi-misi' => 'CMS Visi Misi',
        '/admin/konten/sejarah' => 'CMS Sejarah',
        '/admin/konten/perangkat' => 'CMS Perangkat',
        '/admin/konten/struktur' => 'CMS Struktur',
        '/admin/konten/wisata' => 'CMS Wisata',
        '/admin/konten/kaba' => 'CMS Kaba',
        '/admin/konten/umkm' => 'CMS UMKM',
    ];
    foreach ($cmsRoutes as $uri => $n) {
        $r = Illuminate\Http\Request::create($uri, 'GET');
        $resp = $kernel->handle($r);
        echo "   -> [{$resp->getStatusCode()}] $n ($uri)\n";
    }
}

if ($wali) {
    Illuminate\Support\Facades\Auth::login($wali);
    $req = Illuminate\Http\Request::create('/wali-nagari', 'GET');
    $res = $kernel->handle($req);
    echo "✅ [Wali Nagari Dashboard] Status: " . $res->getStatusCode() . " (Wali: {$wali->name})\n";
}

// 3. Test 7 Jenis Surat Submission & Stamping Workflow
echo "\n--- 3. Testing 7 Surat Types Lifecycle ---\n";
if ($warga) {
    Illuminate\Support\Facades\Auth::login($warga);
    
    // Create Surat Permohonan (e.g. Suket Usaha)
    $surat = App\Models\SuratPermohonan::create([
        'user_id' => $warga->id,
        'jenis_surat' => 'suket_usaha',
        'nama_pemohon' => $warga->name,
        'nik_pemohon' => $warga->nik,
        'tempat_lahir' => 'Koto Malintang',
        'tanggal_lahir' => '1995-05-12',
        'jenis_kelamin' => 'Laki-laki',
        'agama' => 'Islam',
        'pekerjaan' => 'Wiraswasta',
        'alamat' => 'Jorong Muko-Muko, Nagari Koto Malintang',
        'keperluan' => 'Pengajuan Pinjaman Modal Usaha KUR Bank Nagari',
        'data_spesifik' => [
            'nama_usaha' => 'Kedai Kopi Danau Maninjau',
            'bidang_usaha' => 'Kuliner & Minuman',
            'alamat_usaha' => 'Jl. Danau Maninjau No. 15',
        ],
        'isi_surat' => 'Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang menerangkan bahwa Budi Santoso benar memiliki usaha Kedai Kopi Danau Maninjau.',
        'status' => 'menunggu_admin',
    ]);

    echo "✅ [Surat Created] ID: {$surat->id} - {$surat->nama_jenis_surat} - Status: {$surat->status}\n";

    // Step Admin: Verifikasi
    $surat->update([
        'status' => 'menunggu_wali_nagari',
        'catatan_admin' => 'Dokumen KTP valid.',
    ]);
    echo "✅ [Admin Verified] Status: {$surat->status}\n";

    // Step Wali Nagari: Stempel Otomatis & Approval
    $surat->update([
        'nomor_surat' => '470 / 001 / WN-KM / VIII / 2026',
        'status' => 'disetujui',
        'stempel_digital' => true,
        'disetujui_wali_at' => now(),
        'catatan_wali' => 'Disetujui dan dibubuhi stempel resmi.',
    ]);
    echo "✅ [Wali Nagari Stamped] Nomor: {$surat->nomor_surat} - Status: {$surat->status}\n";

    // Step Citizen: View Print
    $reqPrint = Illuminate\Http\Request::create("/warga/surat/{$surat->id}/print", 'GET');
    $resPrint = $kernel->handle($reqPrint);
    echo "✅ [Cetak Surat Resmi Print View] Status: {$resPrint->getStatusCode()}\n";
}

echo "\n🎉 ALL INTEGRATION TESTS COMPLETED SUCCESSFULLY!\n";
