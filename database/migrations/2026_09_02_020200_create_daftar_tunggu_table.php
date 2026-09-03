<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_tunggu', function (Blueprint $table) {
            $table->id('daftar_tunggu_id');
            $table->unsignedBigInteger('daftar_tunggu_id_aset')->nullable();
            $table->unsignedBigInteger('daftar_tunggu_id_peminjam')->nullable();
            $table->dateTime('daftar_tunggu_tanggal_mulai');
            $table->integer('daftar_tunggu_durasi');
            $table->string('daftar_tunggu_status');
            $table->unsignedBigInteger('daftar_tunggu_id_peminjaman')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_tunggu');
    }
};
