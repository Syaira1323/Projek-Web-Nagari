<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UMKM extends Model
{
    protected $table = 'umkms';

    protected $fillable = [
        'user_id',
        'nama_usaha',
        'pemilik',
        'kategori',
        'deskripsi',
        'foto',
        'tahun_berdiri',
        'jam_operasional',
        'whatsapp',
        'instagram',
        'google_maps_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}