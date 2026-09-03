<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penghapusan', function (Blueprint $table) {
            $table->id('penghapusan_id');
            $table->string('penghapusan_nomor')->unique();
            $table->unsignedBigInteger('penghapusan_id_aset')->nullable();
            $table->text('penghapusan_alasan');
            $table->dateTime('penghapusan_tanggal_request');
            $table->decimal('penghapusan_nilai_buku', 15, 2)->default(0);
            $table->decimal('penghapusan_nilai_sisa', 15, 2)->default(0);
            $table->string('penghapusan_status');
            $table->string('penghapusan_triase')->nullable();
            $table->date('penghapusan_tanggal_akhir_karantina')->nullable();
            $table->string('penghapusan_foto')->nullable();
            $table->string('penghapusan_berita_acara')->nullable();
            $table->decimal('penghapusan_gain_loss', 15, 2)->nullable();
            $table->text('penghapusan_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penghapusan');
    }
};
