<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visi;
use App\Models\MaknaVisi;
use App\Models\Misi;

class VisiSeeder extends Seeder
{
    public function run(): void
    {
        // Data Visi
        $visi = Visi::create([
            'judul' => 'VISI',
            'isi' => 'Terwujudnya Nagari Koto Malintang yang Maju, Mandiri, dan Sejahtera Berdasarkan Nilai-nilai Adat Basandi Syarak, Syarak Basandi Kitabullah',
        ]);

        // Data Makna Visi
        MaknaVisi::create([
            'visi_id' => $visi->id,
            'istilah' => 'Maju',
            'deskripsi' => 'Berkembang dalam segala aspek kehidupan',
            'icon' => 'arrow-up',
            'urutan' => 1,
        ]);

        MaknaVisi::create([
            'visi_id' => $visi->id,
            'istilah' => 'Mandiri',
            'deskripsi' => 'Mampu berdikari dan tidak bergantung',
            'icon' => 'shield',
            'urutan' => 2,
        ]);

        MaknaVisi::create([
            'visi_id' => $visi->id,
            'istilah' => 'Sejahtera',
            'deskripsi' => 'Kehidupan yang layak dan bahagia',
            'icon' => 'heart',
            'urutan' => 3,
        ]);

        MaknaVisi::create([
            'visi_id' => $visi->id,
            'istilah' => 'Religius',
            'deskripsi' => 'Berdasarkan nilai-nilai agama dan adat',
            'icon' => 'book',
            'urutan' => 4,
        ]);

        // Data Misi
        Misi::create([
            'visi_id' => $visi->id,
            'nomor' => 1,
            'judul' => 'Meningkatkan Kualitas Sumber Daya Manusia',
            'deskripsi' => 'Melalui pendidikan, pelatihan, dan pengembangan kapasitas masyarakat',
        ]);

        Misi::create([
            'visi_id' => $visi->id,
            'nomor' => 2,
            'judul' => 'Mengembangkan Ekonomi Kerakyatan',
            'deskripsi' => 'Memberdayakan UMKM dan potensi ekonomi lokal untuk kesejahteraan bersama',
        ]);

        Misi::create([
            'visi_id' => $visi->id,
            'nomor' => 3,
            'judul' => 'Memperkuat Infrastruktur Nagari',
            'deskripsi' => 'Membangun dan memelihara sarana prasarana untuk mendukung aktivitas masyarakat',
        ]);

        Misi::create([
            'visi_id' => $visi->id,
            'nomor' => 4,
            'judul' => 'Melestarikan Budaya dan Adat Istiadat',
            'deskripsi' => 'Menjaga dan mengembangkan warisan budaya Minangkabau untuk generasi mendatang',
        ]);
        Misi::create([
            'visi_id' => $visi->id,
            'nomor' => 5,
            'judul' => 'Meningkatkan Pelayanan Publik',
            'deskripsi' => 'Memberikan pelayanan yang prima, transparan, dan akuntabel kepada masyarakat',
        ]);
        Misi::create([
            'visi_id' => $visi->id,
            'nomor' => 6,
            'judul' => 'Menjaga Kelestarian Lingkungan',
            'deskripsi' => 'Mengelola sumber daya alam secara berkelanjutan untuk masa depan yang lebih baik',
        ]);
    }
}