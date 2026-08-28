<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdatBudayaNagari extends Model
{
    protected $table = 'adat_budaya_nagaris';

    protected $fillable = [
        'uraian',
        'jumlah_suku',
        'kegiatan_adat',
        'gambar',
    ];

    protected $casts = [
        'kegiatan_adat' => 'array',
    ];
}
