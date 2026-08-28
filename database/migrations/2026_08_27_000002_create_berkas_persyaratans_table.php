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
        Schema::create('berkas_persyaratans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_permohonan_id')->constrained('surat_permohonans')->cascadeOnDelete();
            $table->string('jenis_berkas', 100); // ktp, kk, surat_pengantar, foto_usaha, dokumen_pendukung, dll
            $table->string('nama_file_asli');
            $table->string('gdrive_file_id')->nullable();
            $table->text('gdrive_view_link')->nullable();
            $table->string('file_path_local')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('folder_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_persyaratans');
    }
};
