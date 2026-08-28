<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilNagari extends Model
{
    protected $table = 'profil_nagaris';

    protected $fillable = [
        'nama_nagari',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'deskripsi',
        'jumlah_penduduk',
        'jumlah_jorong',
        'luas_wilayah',
        'alamat',
        'kode_pos',
        'email',
        'telepon',
        'website',
        'logo',
        'foto_utama',
    ];
}