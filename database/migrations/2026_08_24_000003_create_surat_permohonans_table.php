<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_permohonans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nomor_surat')->nullable();
            $table->string('jenis_surat'); // suket_usaha, suket_meninggal, suket_belum_menikah, surat_nikah_na, sket_penghasilan, suket_tidak_mampu, suket_domisili
            $table->string('nama_pemohon');
            $table->string('nik_pemohon', 20);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('agama', 30)->nullable();
            $table->string('pekerjaan')->nullable();
            $table->text('alamat')->nullable();
            $table->text('keperluan')->nullable();
            $table->json('data_spesifik')->nullable(); // Data khusus sesuai jenis surat (e.g. nama usaha, nama almarhum, nominal penghasilan, calon pasangan, dll)
            $table->longText('isi_surat')->nullable(); // Redaksi resmi surat yang siap dicetak
            $table->string('status', 30)->default('menunggu_admin'); // menunggu_admin, menunggu_wali_nagari, disetujui, ditolak
            $table->text('catatan_admin')->nullable();
            $table->text('catatan_wali')->nullable();
            $table->timestamp('disetujui_wali_at')->nullable();
            $table->boolean('stempel_digital')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_permohonans');
    }
};
