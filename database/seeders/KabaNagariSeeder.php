<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KabaNagari;

class KabaNagariSeeder extends Seeder
{
    public function run(): void
    {
        KabaNagari::create([
            'judul' => 'LPPM Universitas Andalas, PLN Indonesia Power, dan Nagari Koto Malintang Jalin Kerja Sama Pemberdayaan Masyarakat Berbasis Aren',
            'kategori' => 'Pemberdayaan Masyarakat',
            'tanggal' => '2026-08-10',
            'gambar' => null,
            'ringkasan' => 'Nagari Koto Malintang menjalin kerja sama dengan LPPM Universitas Andalas dan PLN Indonesia Power dalam pemberdayaan masyarakat melalui pelatihan pengelolaan tanaman aren.',
            'isi' => 'Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM) Universitas Andalas bersama PT PLN Indonesia Power UBP Bukittinggi dan Pemerintah Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, menandatangani Perjanjian Kerja Sama tentang pemberdayaan masyarakat melalui pelatihan pengelolaan tanaman aren pada 10 Agustus 2026 di Kantor LPPM Universitas Andalas, Padang.

Kerja sama ini menjadi langkah kolaboratif antara dunia usaha, perguruan tinggi, dan pemerintah nagari untuk mengembangkan potensi lokal. Program tersebut diharapkan dapat meningkatkan kapasitas masyarakat sekaligus mendukung peningkatan kesejahteraan masyarakat Nagari Koto Malintang.

Perjanjian kerja sama ditandatangani oleh Budi Murianto selaku Manager PT PLN Indonesia Power UBP Bukittinggi, Prof. Dr. techn. Marzuki selaku Ketua LPPM Universitas Andalas, dan Hendra Yanto selaku Wali Nagari Koto Malintang.',
        ]);

        KabaNagari::create([
            'judul' => 'Boss Kids Koto Malintang Juara Satu Open Turnamen Bola Voli Merpati Cup 2026',
            'kategori' => 'Olahraga',
            'tanggal' => '2026-07-07',
            'gambar' => null,
            'ringkasan' => 'Tim Boss Kids Nagari Koto Malintang berhasil meraih juara pertama Open Turnamen Bola Voli Merpati Cup 2026 setelah mengalahkan Patriot Nagari Maninjau dengan skor 3-1.',
            'isi' => 'Boss Kids Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, berhasil meraih juara pertama Open Turnamen Bola Voli Merpati Cup 2026. Pertandingan final berlangsung di Lapangan Merpati, Jorong Ambacang, Nagari Koto Malintang, pada Senin malam, 6 Juli 2026.

Dalam pertandingan final, Boss Kids Koto Malintang berhasil mengalahkan Patriot Nagari Maninjau dengan skor 3-1. Sementara itu, juara ketiga diraih Sabena Padang Tarok setelah mengalahkan Patagok A Nagari Koto Gadang dengan skor 3-2.

Untuk kategori putri, juara pertama diraih oleh tuan rumah Merpati Koto Malintang. Kegiatan ini juga menjadi wadah silaturahmi masyarakat serta memberikan ruang bagi generasi muda untuk mengembangkan bakat di bidang olahraga.',
        ]);

        KabaNagari::create([
            'judul' => 'Mangaji Tamaik KAN Koto Malintang, Wujud Syukur dan Pelestarian Tradisi Adat Bernuansa Religius',
            'kategori' => 'Adat & Budaya',
            'tanggal' => '2026-06-01',
            'gambar' => null,
            'ringkasan' => 'KAN Koto Malintang menggelar Mangaji Tamaik sebagai bentuk rasa syukur masyarakat sekaligus upaya melestarikan tradisi adat yang telah diwariskan secara turun-temurun.',
            'isi' => 'Kerapatan Adat Nagari (KAN) Koto Malintang menggelar kegiatan Mangaji Tamaik bersama Ust. Ristawardi Dt. Marajo di Masjid Nurul Huda, Jorong Rambai, Nagari Koto Malintang, Kecamatan Tanjung Raya, pada Senin, 1 Juni 2026.

Tradisi Mangaji Tamaik telah diwariskan secara turun-temurun sejak zaman nenek moyang dan masih terus dilestarikan oleh masyarakat hingga saat ini. Kegiatan ini menjadi bentuk ungkapan rasa syukur masyarakat nagari atas berbagai nikmat dan rezeki yang diperoleh selama satu tahun, baik dari usaha di perairan Danau Maninjau maupun usaha di daratan.

Kegiatan tersebut turut dihadiri unsur pemerintah daerah, Camat Tanjung Raya, Wali Nagari Koto Malintang, alim ulama, niniak mamak, serta sejumlah tokoh dan undangan lainnya.',
        ]);
    }
}