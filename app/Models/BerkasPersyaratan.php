<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BerkasPersyaratan extends Model
{
    protected $table = 'berkas_persyaratans';

    protected $fillable = [
        'surat_permohonan_id',
        'jenis_berkas',
        'nama_file_asli',
        'gdrive_file_id',
        'gdrive_view_link',
        'file_path_local',
        'mime_type',
        'file_size',
        'folder_path',
    ];

    public function suratPermohonan(): BelongsTo
    {
        return $this->belongsTo(SuratPermohonan::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '-';
        }

        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    public function getLabelJenisAttribute(): string
    {
        return match ($this->jenis_berkas) {
            'ktp' => 'KTP Pemohon',
            'kk' => 'Kartu Keluarga (KK)',
            'surat_pengantar' => 'Surat Pengantar RT/Jorong',
            'foto_usaha' => 'Foto Tempat Usaha',
            'foto_rumah' => 'Foto Rumah / Tempat Tinggal',
            'surat_kematian_rs' => 'Surat Keterangan Kematian RS/Dokter',
            'ktp_pasangan' => 'KTP Calon Pasangan',
            'slip_penghasilan' => 'Slip Gaji / Bukti Penghasilan',
            'dokumen_pendukung' => 'Dokumen Pendukung',
            default => ucwords(str_replace('_', ' ', $this->jenis_berkas)),
        };
    }
}
