<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Misi extends Model
{
    protected $table = 'misis';

    protected $fillable = [
        'visi_id',
        'nomor',
        'judul',
        'deskripsi',
    ];

    public function visi(): BelongsTo
    {
        return $this->belongsTo(Visi::class);
    }
}