<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UMKM;

class UMKMSeeder extends Seeder
{
    public function run(): void
    {
        UMKM::create([
            'nama_usaha' => 'Dapoer Pipin',
            'pemilik' => 'Pipin',
            'kategori' => 'Kuliner',
            'deskripsi' => 'Penjualan olahan ikan khas dari Maninjau, seperti peyek rinuak, rinuak goreng, dan palai rinuak.',
            'foto' => null,
            'tahun_berdiri' => 2024,
            'jam_operasional' => '09.00 - 17.00',
            'whatsapp' => null,
            'instagram' => 'https://www.instagram.com/dapoerpipin_maninjau',
            'google_maps_url' => 'https://maps.app.goo.gl/PEtrSzBQwB8UrA2h9',
        ]);

        UMKM::create([
            'nama_usaha' => 'Sadama Craft',
            'pemilik' => 'PKK Koto Malintang',
            'kategori' => 'Kerajinan',
            'deskripsi' => 'Kerajinan yang terbuat dari eceng gondok.',
            'foto' => null,
            'tahun_berdiri' => 2023,
            'jam_operasional' => '09.00 - 17.00',
            'whatsapp' => null,
            'instagram' => 'https://www.instagram.com/sadamacraff21',
            'google_maps_url' => 'https://maps.app.goo.gl/fEsRYUJcCK9EFKC3A',
        ]);
    }
}