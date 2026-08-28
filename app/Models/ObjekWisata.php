<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjekWisata extends Model
{
    protected $table = 'objek_wisatas';

    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar',
        'lokasi',
        'kategori',
        'latitude',
        'longitude',
        'google_maps_url',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
}