<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SejarahNagari extends Model
{
    protected $fillable = [
        'tipe',
        'judul',
        'periode',
        'isi',
        'gambar',
        'urutan',
    ];
}
