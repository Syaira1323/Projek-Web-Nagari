<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoKabaNagari extends Model
{
    protected $table = 'gambar_kaba_nagaris';

    protected $fillable = ['kaba_nagari_id', 'gambar', 'caption'];

    public function kabaNagari(): BelongsTo
    {
        return $this->belongsTo(KabaNagari::class);
    }
}
