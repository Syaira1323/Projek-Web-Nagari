<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SejarahNagari;

class SejarahNagariSeeder extends Seeder
{
    public function run(): void
    {
        SejarahNagari::create([
            'tipe' => 'singkat',
            'judul' => 'Sejarah Singkat',
            'periode' => null,
            'isi' => 'Nagari Koto Malintang tumbuh dari tradisi Minangkabau yang kuat, berpijak pada nilai adat basandi syarak, syarak basandi Kitabullah.

Seiring berjalannya waktu, nagari ini mengalami dinamika sosial dan pemerintahan, dari era kerajaan, kolonial, hingga era modern saat ini. Namun, kekuatan gotong royong, musyawarah, dan kearifan lokal tetap menjadi jantung kehidupan masyarakat.

Berakar pada sistem adat dan kekerabatan matrilineal. Berperan dalam pendidikan, pertanian, dan budaya lokal. Terus beradaptasi dengan kemajuan zaman tanpa meninggalkan nilai luhur.',
            'urutan' => 0,
        ]);

        SejarahNagari::create([
            'tipe' => 'periode',
            'judul' => 'Era Kerajaan Minangkabau',
            'periode' => '±1800-an',
            'isi' => 'Nagari Koto Malintang berkembang dalam lingkungan masyarakat Minangkabau yang menjunjung tinggi adat, kekerabatan matrilineal, musyawarah, dan kehidupan bersama dalam nagari.',
            'urutan' => 1,
        ]);

        SejarahNagari::create([
            'tipe' => 'periode',
            'judul' => 'Era Kolonial',
            'periode' => '1900–1945',
            'isi' => 'Terjadi perubahan administrasi dan peralihan sistem pemerintahan pada masa kolonial, namun nilai budaya, adat istiadat, dan kehidupan masyarakat tetap terjaga.',
            'urutan' => 2,
        ]);

        SejarahNagari::create([
            'tipe' => 'periode',
            'judul' => 'Era Kemerdekaan',
            'periode' => '1945–1979',
            'isi' => 'Setelah Indonesia merdeka, kehidupan masyarakat Nagari Koto Malintang terus berkembang dengan semangat kebersamaan, gotong royong, serta pembangunan di berbagai bidang.',
            'urutan' => 3,
        ]);

        SejarahNagari::create([
            'tipe' => 'periode',
            'judul' => 'Era Nagari Modern',
            'periode' => '1979–Sekarang',
            'isi' => 'Nagari Koto Malintang terus mengalami perkembangan dalam pemerintahan, pendidikan, pertanian, ekonomi, dan kehidupan sosial masyarakat dengan tetap mempertahankan nilai adat dan budaya Minangkabau.',
            'urutan' => 4,
        ]);
    }
}