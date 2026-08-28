<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gambar_kaba_nagaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kaba_nagari_id')->constrained('kaba_nagaris')->cascadeOnDelete();
            $table->string('gambar');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gambar_kaba_nagaris');
    }
};
