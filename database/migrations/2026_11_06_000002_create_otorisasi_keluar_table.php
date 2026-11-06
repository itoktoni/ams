<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otorisasi_keluar', function (Blueprint $table) {
            $table->id('otorisasi_keluar_id');
            $table->unsignedBigInteger('otorisasi_keluar_id_aset');
            $table->string('otorisasi_keluar_nomor')->unique();
            $table->string('otorisasi_keluar_jenis', 30)->default('lainnya');
            $table->dateTime('otorisasi_keluar_tanggal_mulai');
            $table->dateTime('otorisasi_keluar_tanggal_selesai');
            $table->string('otorisasi_keluar_status', 30)->default('diajukan');
            $table->unsignedBigInteger('otorisasi_keluar_id_approver')->nullable();
            $table->text('otorisasi_keluar_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otorisasi_keluar');
    }
};
