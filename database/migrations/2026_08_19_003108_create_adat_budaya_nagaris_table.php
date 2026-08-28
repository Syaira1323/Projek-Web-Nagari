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
    Schema::create('adat_budaya_nagaris', function (Blueprint $table) {
        $table->id();
        $table->text('uraian')->nullable();
        $table->unsignedInteger('jumlah_suku')->nullable();
        $table->json('kegiatan_adat')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adat_budaya_nagaris');
    }
};
