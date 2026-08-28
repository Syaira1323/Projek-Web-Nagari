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
        if (Schema::hasTable('adat_budaya_nagaris') && !Schema::hasColumn('adat_budaya_nagaris', 'gambar')) {
            Schema::table('adat_budaya_nagaris', function (Blueprint $table) {
                $table->string('gambar')->nullable()->after('kegiatan_adat');
            });
        }

        if (Schema::hasTable('sejarah_nagaris') && !Schema::hasColumn('sejarah_nagaris', 'gambar')) {
            Schema::table('sejarah_nagaris', function (Blueprint $table) {
                $table->string('gambar')->nullable()->after('isi');
            });
        }

        if (Schema::hasTable('struktur_nagari') && !Schema::hasColumn('struktur_nagari', 'foto_bagan')) {
            Schema::table('struktur_nagari', function (Blueprint $table) {
                $table->string('foto_bagan')->nullable()->after('keterangan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('adat_budaya_nagaris') && Schema::hasColumn('adat_budaya_nagaris', 'gambar')) {
            Schema::table('adat_budaya_nagaris', function (Blueprint $table) {
                $table->dropColumn('gambar');
            });
        }

        if (Schema::hasTable('sejarah_nagaris') && Schema::hasColumn('sejarah_nagaris', 'gambar')) {
            Schema::table('sejarah_nagaris', function (Blueprint $table) {
                $table->dropColumn('gambar');
            });
        }

        if (Schema::hasTable('struktur_nagari') && Schema::hasColumn('struktur_nagari', 'foto_bagan')) {
            Schema::table('struktur_nagari', function (Blueprint $table) {
                $table->dropColumn('foto_bagan');
            });
        }
    }
};
