<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visi extends Model
{
    protected $table = 'visis';

    protected $fillable = [
        'judul',
        'isi',
    ];

    public function maknaVisis(): HasMany
    {
        return $this->hasMany(MaknaVisi::class);
    }

    public function misis(): HasMany
    {
        return $this->hasMany(Misi::class);
    }
}