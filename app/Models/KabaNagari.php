<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KabaNagari extends Model
{
    protected $table = 'kaba_nagaris';

    protected $fillable = [
        'judul',
        'kategori',
        'tanggal',
        'gambar',
        'ringkasan',
        'isi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function gambarKabaNagaris(): HasMany
    {
        return $this->hasMany(FotoKabaNagari::class, 'kaba_nagari_id');
    }
}
