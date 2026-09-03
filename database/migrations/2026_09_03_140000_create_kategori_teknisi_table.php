<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_teknisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kategori_id')->index('idx_kt_kategori');
            $table->unsignedBigInteger('teknisi_id')->index('idx_kt_teknisi');
            $table->timestamps();
            $table->unique(['kategori_id', 'teknisi_id'], 'uq_kategori_teknisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_teknisi');
    }
};
