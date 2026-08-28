<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaknaVisi extends Model
{
    protected $table = 'makna_visis';

    protected $fillable = [
        'visi_id',
        'istilah',
        'deskripsi',
        'icon',
        'urutan',
    ];

    public function visi(): BelongsTo
    {
        return $this->belongsTo(Visi::class);
    }
}