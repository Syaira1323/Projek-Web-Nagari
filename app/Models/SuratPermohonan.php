<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratPermohonan extends Model
{
    protected $table = 'surat_permohonans';

    protected $fillable = [
        'user_id',
        'nomor_surat',
        'jenis_surat',
        'nama_pemohon',
        'nik_pemohon',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'pekerjaan',
        'alamat',
        'keperluan',
        'data_spesifik',
        'isi_surat',
        'status',
        'catatan_admin',
        'catatan_wali',
        'disetujui_wali_at',
        'ttd_wali_path',
        'signed_by_user_id',
        'stempel_digital',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'data_spesifik' => 'array',
            'disetujui_wali_at' => 'datetime',
            'stempel_digital' => 'boolean',
        ];
    }

    protected $appends = [
        'nama_jenis_surat',
        'singkatan_jenis_surat',
        'status_label',
        'status_badge_color',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_user_id');
    }

    public function berkasPersyaratans(): HasMany
    {
        return $this->hasMany(BerkasPersyaratan::class);
    }

    public static function daftarJenisSurat(): array
    {
        return [
            'suket_usaha' => [
                'nama' => 'Surat Keterangan Usaha',
                'singkatan' => 'SKU',
                'deskripsi' => 'Surat keterangan kepemilikan dan legalitas operasional usaha mikro/kecil di Nagari Koto Malintang.',
                'icon' => 'bi-shop',
                'persyaratan' => [
                    'ktp' => 'Foto / Scan KTP Pemohon (Wajib)',
                    'kk' => 'Foto / Scan Kartu Keluarga (Wajib)',
                    'foto_usaha' => 'Foto Tempat / Aktivitas Usaha (Wajib)',
                    'surat_pengantar' => 'Surat Pengantar Jorong (Opsional)',
                ],
            ],
            'suket_meninggal' => [
                'nama' => 'Surat Keterangan Meninggal',
                'singkatan' => 'SKM',
                'deskripsi' => 'Surat keterangan resmi pencatatan kematian/meninggal dunia warga nagari.',
                'icon' => 'bi-heartbreak',
                'persyaratan' => [
                    'ktp' => 'Foto / Scan KTP Pelapor / Ahli Waris (Wajib)',
                    'kk' => 'Foto / Scan Kartu Keluarga Almarhum (Wajib)',
                    'surat_kematian_rs' => 'Surat Keterangan Kematian RS / Bidan / Wali Jorong (Wajib)',
                ],
            ],
            'suket_belum_menikah' => [
                'nama' => 'Surat Keterangan Belum Menikah',
                'singkatan' => 'SKBM',
                'deskripsi' => 'Surat keterangan status lajang/belum pernah menikah untuk keperluan administrasi pekerjaan/pendidikan.',
                'icon' => 'bi-person-check',
                'persyaratan' => [
                    'ktp' => 'Foto / Scan KTP Pemohon (Wajib)',
                    'kk' => 'Foto / Scan Kartu Keluarga (Wajib)',
                    'surat_pengantar' => 'Surat Pengantar Jorong (Opsional)',
                ],
            ],
            'surat_nikah_na' => [
                'nama' => 'Surat Pengantar Nikah (NA)',
                'singkatan' => 'Model NA',
                'deskripsi' => 'Surat pengantar rekomendasi permohonan kehendak nikah ke Kantor Urusan Agama (KUA).',
                'icon' => 'bi-gem',
                'persyaratan' => [
                    'ktp' => 'Foto / Scan KTP Pemohon (Wajib)',
                    'kk' => 'Foto / Scan Kartu Keluarga Pemohon (Wajib)',
                    'ktp_pasangan' => 'Foto / Scan KTP Calon Pasangan (Wajib)',
                    'surat_pengantar' => 'Surat Pengantar Nikah dari Kepala Jorong (Wajib)',
                ],
            ],
            'sket_penghasilan' => [
                'nama' => 'Surat Keterangan Penghasilan',
                'singkatan' => 'SKP',
                'deskripsi' => 'Surat keterangan rincian estimasi penghasilan rata-rata orang tua/pribadi untuk beasiswa/kredit.',
                'icon' => 'bi-cash-coin',
                'persyaratan' => [
                    'ktp' => 'Foto / Scan KTP Pemohon (Wajib)',
                    'kk' => 'Foto / Scan Kartu Keluarga (Wajib)',
                    'slip_penghasilan' => 'Surat Pernyataan Penghasilan / Slip Gaji (Wajib)',
                ],
            ],
            'suket_tidak_mampu' => [
                'nama' => 'Surat Keterangan Tidak Mampu',
                'singkatan' => 'SKTM',
                'deskripsi' => 'Surat keterangan kondisi ekonomi kurang mampu untuk keperluan beasiswa, BPJS, atau bantuan sosial.',
                'icon' => 'bi-people',
                'persyaratan' => [
                    'ktp' => 'Foto / Scan KTP Pemohon (Wajib)',
                    'kk' => 'Foto / Scan Kartu Keluarga (Wajib)',
                    'foto_rumah' => 'Foto Kondisi Rumah / Tempat Tinggal (Wajib)',
                    'surat_pengantar' => 'Surat Keterangan Kurang Mampu dari Kepala Jorong (Wajib)',
                ],
            ],
            'suket_domisili' => [
                'nama' => 'Surat Keterangan Domisili',
                'singkatan' => 'SKD',
                'deskripsi' => 'Surat keterangan tempat tinggal resmi bertempat di wilayah jorong Nagari Koto Malintang.',
                'icon' => 'bi-geo-alt',
                'persyaratan' => [
                    'ktp' => 'Foto / Scan KTP Pemohon (Wajib)',
                    'kk' => 'Foto / Scan Kartu Keluarga (Wajib)',
                    'surat_pengantar' => 'Surat Keterangan Domisili dari Kepala Jorong (Wajib)',
                ],
            ],
        ];
    }

    public function getNamaJenisSuratAttribute(): string
    {
        $daftar = self::daftarJenisSurat();
        return $daftar[$this->jenis_surat]['nama'] ?? ucwords(str_replace('_', ' ', $this->jenis_surat));
    }

    public function getSingkatanJenisSuratAttribute(): string
    {
        $daftar = self::daftarJenisSurat();
        return $daftar[$this->jenis_surat]['singkatan'] ?? 'SURAT';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_admin' => 'Menunggu Verifikasi Admin',
            'menunggu_wali_nagari' => 'Menunggu Stempel Wali Nagari',
            'disetujui' => 'Selesai / Disetujui',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu_admin' => 'warning text-dark',
            'menunggu_wali_nagari' => 'info text-dark',
            'disetujui' => 'success text-white',
            'ditolak' => 'danger text-white',
            default => 'secondary',
        };
    }
}
