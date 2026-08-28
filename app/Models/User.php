<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'nik',
        'phone',
        'address',
        'ttd_path',
        'gelar',
        'jabatan',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getTtdUrlAttribute(): ?string
    {
        if (empty($this->ttd_path)) {
            return null;
        }
        return str_starts_with($this->ttd_path, 'http') ? $this->ttd_path : asset($this->ttd_path);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWaliNagari(): bool
    {
        return $this->role === 'wali_nagari';
    }

    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    public function suratPermohonans(): HasMany
    {
        return $this->hasMany(SuratPermohonan::class);
    }

    public function umkms(): HasMany
    {
        return $this->hasMany(Umkm::class);
    }
}
