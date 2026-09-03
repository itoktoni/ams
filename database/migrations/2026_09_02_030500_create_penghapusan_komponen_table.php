<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penghapusan_komponen', function (Blueprint $table) {
            $table->id('penghapusan_komponen_id');
            $table->unsignedBigInteger('penghapusan_komponen_id_penghapusan')->nullable();
            $table->string('penghapusan_komponen_nama');
            $table->decimal('penghapusan_komponen_jumlah', 15, 2)->default(1);
            $table->unsignedBigInteger('penghapusan_komponen_id_suku_cadang')->nullable();
            $table->string('penghapusan_komponen_kondisi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penghapusan_komponen');
    }
};
