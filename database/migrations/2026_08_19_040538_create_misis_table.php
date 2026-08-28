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
    Schema::create('misis', function (Blueprint $table) {
        $table->id();

        $table->foreignId('visi_id')
              ->constrained('visis')
              ->cascadeOnDelete();

        $table->integer('nomor');
        $table->string('judul');
        $table->text('deskripsi');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('misis');
    }
};
