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
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();

            $table->string('nama_usaha');
            $table->string('pemilik');
            $table->string('kategori');

            $table->text('deskripsi');

            $table->string('foto')->nullable();

            $table->year('tahun_berdiri')->nullable();

            $table->string('jam_operasional')->nullable();

            // Kontak
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();

            // Link Google Maps
            $table->text('google_maps_url')->nullable();

            // Rating
            $table->decimal('rating', 2, 1)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};