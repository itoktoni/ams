<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('peminjaman_id');
            $table->string('peminjaman_nomor')->unique();
            $table->unsignedBigInteger('peminjaman_id_aset')->nullable();
            $table->unsignedBigInteger('peminjaman_id_peminjam')->nullable();
            $table->text('peminjaman_tujuan')->nullable();
            $table->dateTime('peminjaman_tanggal_pinjam');
            $table->dateTime('peminjaman_jatuh_tempo');
            $table->dateTime('peminjaman_tanggal_kembali')->nullable();
            $table->string('peminjaman_status');
            $table->integer('peminjaman_grace_jam')->default(4);
            $table->decimal('peminjaman_denda', 15, 2)->default(0);
            $table->string('peminjaman_kondisi_kembali')->nullable();
            $table->string('peminjaman_foto_kembali')->nullable();
            $table->unsignedBigInteger('peminjaman_id_approver')->nullable();
            $table->integer('peminjaman_perpanjang_ke')->default(0);
            $table->text('peminjaman_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
