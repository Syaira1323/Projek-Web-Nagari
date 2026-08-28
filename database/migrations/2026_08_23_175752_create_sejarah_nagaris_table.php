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
        Schema::create('sejarah_nagaris', function (Blueprint $table) {
    $table->id();
    $table->string('tipe');
    $table->string('judul');
    $table->string('periode')->nullable();
    $table->text('isi');
    $table->integer('urutan')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sejarah_nagaris');
    }
};
