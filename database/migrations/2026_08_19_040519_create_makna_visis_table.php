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
    Schema::create('makna_visis', function (Blueprint $table) {
        $table->id();

        $table->foreignId('visi_id')
              ->constrained('visis')
              ->cascadeOnDelete();

        $table->string('istilah');
        $table->text('deskripsi');
        $table->string('icon')->nullable();
        $table->integer('urutan')->default(1);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makna_visis');
    }
};
