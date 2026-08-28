<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrukturNagari extends Model
{
    protected $table = 'struktur_nagari';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'keterangan',
        'foto_bagan',
    ];
}
