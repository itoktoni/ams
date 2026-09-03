<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perpindahan', function (Blueprint $table) {
            $table->id('perpindahan_id');
            $table->string('perpindahan_nomor')->unique();
            $table->unsignedBigInteger('perpindahan_id_aset')->nullable();
            $table->unsignedBigInteger('perpindahan_id_lokasi_asal')->nullable();
            $table->unsignedBigInteger('perpindahan_id_lokasi_tujuan')->nullable();
            $table->text('perpindahan_alasan')->nullable();
            $table->dateTime('perpindahan_tanggal_request');
            $table->date('perpindahan_tanggal_estimasi')->nullable();
            $table->dateTime('perpindahan_tanggal_kirim')->nullable();
            $table->dateTime('perpindahan_tanggal_terima')->nullable();
            $table->string('perpindahan_status');
            $table->string('perpindahan_level_approve')->nullable();
            $table->string('perpindahan_foto_keluar')->nullable();
            $table->string('perpindahan_foto_terima')->nullable();
            $table->string('perpindahan_ttd_hash')->nullable();
            $table->decimal('perpindahan_latitude', 15, 2)->nullable();
            $table->decimal('perpindahan_longitude', 15, 2)->nullable();
            $table->text('perpindahan_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perpindahan');
    }
};
