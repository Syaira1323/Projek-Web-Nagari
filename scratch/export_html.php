<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routes = [
    '/' => 'index.html',
    '/profil-nagari' => 'profil-nagari.html',
    '/visi-misi' => 'visi-misi.html',
    '/sejarah' => 'sejarah.html',
    '/perangkat-nagari' => 'perangkat-nagari.html',
    '/struktur-nagari' => 'struktur-nagari.html',
    '/objek-wisata' => 'objek-wisata.html',
    '/kaba-nagari' => 'kaba-nagari.html',
    '/umkm' => 'umkm.html',
    '/layanan-surat' => 'layanan-surat.html',
    '/login' => 'login.html',
    '/register' => 'register.html',
];

$outputDir = 'D:/MAGANG/Nagari Koto Malintang/frontend/public';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Map of internal links to static html files (order matters: specific routes first)
$linkReplacements = [
    'http://layanan-surat.test/profil-nagari' => 'profil-nagari.html',
    'http://layanan-surat.test/visi-misi' => 'visi-misi.html',
    'http://layanan-surat.test/sejarah' => 'sejarah.html',
    'http://layanan-surat.test/perangkat-nagari' => 'perangkat-nagari.html',
    'http://layanan-surat.test/struktur-nagari' => 'struktur-nagari.html',
    'http://layanan-surat.test/objek-wisata' => 'objek-wisata.html',
    'http://layanan-surat.test/kaba-nagari' => 'kaba-nagari.html',
    'http://layanan-surat.test/umkm' => 'umkm.html',
    'http://layanan-surat.test/layanan-surat' => 'layanan-surat.html',
    'http://layanan-surat.test/login' => 'login.html',
    'http://layanan-surat.test/register' => 'register.html',
    'http://layanan-surat.test/' => 'index.html',
    'http://layanan-surat.test' => 'index.html',
    'http://localhost/profil-nagari' => 'profil-nagari.html',
    'http://localhost/visi-misi' => 'visi-misi.html',
    'http://localhost/sejarah' => 'sejarah.html',
    'http://localhost/perangkat-nagari' => 'perangkat-nagari.html',
    'http://localhost/struktur-nagari' => 'struktur-nagari.html',
    'http://localhost/objek-wisata' => 'objek-wisata.html',
    'http://localhost/kaba-nagari' => 'kaba-nagari.html',
    'http://localhost/umkm' => 'umkm.html',
    'http://localhost/layanan-surat' => 'layanan-surat.html',
    'http://localhost/login' => 'login.html',
    'http://localhost/register' => 'register.html',
    'http://localhost/' => 'index.html',
    'http://localhost' => 'index.html',
    'href="/profil-nagari"' => 'href="profil-nagari.html"',
    'href="/visi-misi"' => 'href="visi-misi.html"',
    'href="/sejarah"' => 'href="sejarah.html"',
    'href="/perangkat-nagari"' => 'href="perangkat-nagari.html"',
    'href="/struktur-nagari"' => 'href="struktur-nagari.html"',
    'href="/objek-wisata"' => 'href="objek-wisata.html"',
    'href="/kaba-nagari"' => 'href="kaba-nagari.html"',
    'href="/umkm"' => 'href="umkm.html"',
    'href="/layanan-surat"' => 'href="layanan-surat.html"',
    'href="/login"' => 'href="login.html"',
    'href="/register"' => 'href="register.html"',
    'href="/"' => 'href="index.html"',
    "href='/'" => 'href="index.html"',
    'action="http://layanan-surat.test/login"' => 'action="login.html"',
    'action="http://layanan-surat.test/register"' => 'action="register.html"',
    'action="/login"' => 'action="login.html"',
    'action="/register"' => 'action="register.html"',
];

foreach ($routes as $uri => $filename) {
    echo "Rendering route: {$uri} -> {$filename}...\n";
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    $html = $response->getContent();

    // Replace asset full URLs with relative paths
    $html = str_replace('http://layanan-surat.test/image/', 'image/', $html);
    $html = str_replace('http://localhost/image/', 'image/', $html);
    $html = str_replace('http://layanan-surat.test/uploads/', 'uploads/', $html);
    $html = str_replace('http://localhost/uploads/', 'uploads/', $html);
    $html = str_replace('http://layanan-surat.test/assets/', 'assets/', $html);
    $html = str_replace('http://localhost/assets/', 'assets/', $html);

    // Apply route link replacements
    foreach ($linkReplacements as $search => $replace) {
        $html = str_replace($search, $replace, $html);
    }
    $html = str_replace('index.htmlfooter-styles.css', 'footer-styles.css', $html);
    $html = str_replace('index.htmlbreadcrumb.js', 'breadcrumb.js', $html);
    $html = str_replace('index.htmlstyle.css', 'style.css', $html);

    $targetFile = $outputDir . '/' . $filename;
    file_put_contents($targetFile, $html);
    echo "  [OK] Saved to {$targetFile} (" . strlen($html) . " bytes)\n";

    // Also copy to original file naming if different
    if ($filename === 'profil-nagari.html') {
        file_put_contents($outputDir . '/Profil Nagari.html', $html);
    } elseif ($filename === 'visi-misi.html') {
        file_put_contents($outputDir . '/Visi Dan Misi.html', $html);
    } elseif ($filename === 'sejarah.html') {
        file_put_contents($outputDir . '/Sejarah.html', $html);
    } elseif ($filename === 'perangkat-nagari.html') {
        file_put_contents($outputDir . '/Perangkat Nagari.html', $html);
    } elseif ($filename === 'struktur-nagari.html') {
        file_put_contents($outputDir . '/Struktur Nagari.html', $html);
    } elseif ($filename === 'objek-wisata.html') {
        file_put_contents($outputDir . '/objek_wisata.html', $html);
    } elseif ($filename === 'kaba-nagari.html') {
        file_put_contents($outputDir . '/kabanagari.html', $html);
    } elseif ($filename === 'layanan-surat.html') {
        file_put_contents($outputDir . '/Layanan Surat.html', $html);
    }
}

echo "\n🎉 ALL HTML FILES EXPORTED SUCCESSFULLY TO {$outputDir}!\n";
