<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerangkatNagari extends Model
{
    protected $table = 'perangkat_nagaris';

    protected $fillable = [
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pangkat_gol',
        'nip',
        'pendidikan_terakhir',
        'jabatan',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
}