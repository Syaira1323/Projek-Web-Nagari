<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturans';

    protected $fillable = [
        'key',
        'value',
        'kategori',
        'tipe',
        'keterangan',
    ];

    /**
     * Helper untuk mengambil value pengaturan berdasarkan key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        if ($setting->tipe === 'boolean') {
            return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($setting->tipe === 'json') {
            return json_decode($setting->value, true) ?: $default;
        }

        return $setting->value ?? $default;
    }

    /**
     * Helper untuk menyimpan atau memperbarui value pengaturan
     */
    public static function set(string $key, $value, string $kategori = 'umum', string $tipe = 'text', ?string $keterangan = null): self
    {
        $valToStore = is_array($value) ? json_encode($value) : (string) $value;

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $valToStore,
                'kategori' => $kategori,
                'tipe' => $tipe,
                'keterangan' => $keterangan,
            ]
        );
    }
}
