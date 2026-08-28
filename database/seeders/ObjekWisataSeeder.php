<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ObjekWisata;

class ObjekWisataSeeder extends Seeder
{
    public function run(): void
    {
        ObjekWisata::create([
            'nama' => 'Air Terjun Cikalo',
            'deskripsi' => 'Air terjun cikalo berada di nagari kotomalintang jorong muko-muko kecamatan tanjung raya kabupaten Agam.Air terjun cikalo di eksplorasikan pada  tahun kisaran 2000-an, Untuk jarak tempuh menuju ke lokasi air terjun cikalo dari jalan lintas itu 500 m+/-.',
            'gambar' => 'danau-maninjau.jpg',
            'lokasi' => 'Tanjung Raya, Kabupaten Agam',
            'kategori' => 'Wisata Alam',
            'latitude' => null,
            'longitude' => null,
            'google_maps_url' => null,
        ]);

        ObjekWisata::create([
            'nama' => 'Taman Wisata Muko-Muko',
            'deskripsi' => 'Taman Muko-Muko berada di nagari kotomalintang jorong muko-muko kecamatan tanjung raya kabupaten Agam. Objek wisata yang di bangun sejak tahun 1990-an ',
            'gambar' => 'puncak-lawang.jpg',
            'lokasi' => 'Lawang, Kabupaten Agam',
            'kategori' => 'Wisata Alam',
            'latitude' => null,
            'longitude' => null,
            'google_maps_url' => null,
        ]);

        ObjekWisata::create([
            'nama' => "Aia Tigo Raso",
            'deskripsi' => 'Aia Tigo raso berada di nagari kotomalintang,jorong muko-muko,kecamatan tanjung raya,kabupaten Agam.Objek wisata yang di bangun sejak tahun 1990-an ini berbentuk kolam dengan ukuran sekitar  2x3 meter  tersebut, terlihat biasa ketika didatangi. Namun setelah mencicipi  rasa dari beberapa sumber mata air ini,  akan terasa beberapa sensasi  rasa pada air tersebut. Pertama rasanya sedikit manis, yang kedua terasa sepat dan  agak pahit  dan  ketiga  rasanya  sedikit Asam. ',
            'gambar' => 'masjid-jami.jpg',
            'lokasi' => 'Nagari Koto Malintang',
            'kategori' => 'Wisata Religi',
            'latitude' => null,
            'longitude' => null,
            'google_maps_url' => null,
        ]);

        ObjekWisata::create([
            'nama' => 'Balai Adat Nagari',
            'deskripsi' => 'Salah satu tempat yang berkaitan dengan adat dan budaya masyarakat Nagari Koto Malintang.',
            'gambar' => 'balai-adat.jpg',
            'lokasi' => 'Nagari Koto Malintang',
            'kategori' => 'Wisata Budaya',
            'latitude' => null,
            'longitude' => null,
            'google_maps_url' => null,
        ]);
         ObjekWisata::create([
            'nama' => 'Kayu Gadang',
            'deskripsi' => 'Kayu gadang Berada dinagari kotomalintang , jorong rambai dan ambacang, kecamatan tanjung raya, kabubupaten agam. Objek wisata alam kayu gadang merupakan salah satu icon Di nagari koto malintang yang memiliki situasi alami nan asri. Kayu gadang ini telah dianungrahi penghargaan di tingkat nasional yaitu Kalpataru dari Kementrian lingkungan hidup republik indonesia, diserahkan langsung oleh Presiden Republik Indonesia Susilo bambang Yudhoyono (SBY) pada tahun 2014.',
            'gambar' => 'balai-adat.jpg',
            'lokasi' => 'Nagari Koto Malintang',
            'kategori' => 'Wisata Alam',
            'latitude' => null,
            'longitude' => null,
            'google_maps_url' => null,
        ]);
    }
}