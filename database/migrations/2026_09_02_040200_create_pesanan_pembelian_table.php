<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_pembelian', function (Blueprint $table) {
            $table->id('pesanan_pembelian_id');
            $table->string('pesanan_pembelian_nomor', 60)->unique();
            $table->unsignedBigInteger('pesanan_pembelian_id_vendor')->nullable()->index();
            $table->date('pesanan_pembelian_tanggal')->nullable();
            $table->date('pesanan_pembelian_tanggal_kirim')->nullable();
            $table->string('pesanan_pembelian_tipe', 30)->nullable();
            $table->string('pesanan_pembelian_status', 30)->nullable();
            $table->decimal('pesanan_pembelian_total', 15, 2)->default(0);
            $table->string('pesanan_pembelian_kode_budget', 60)->nullable();
            $table->string('pesanan_pembelian_level_approve', 30)->nullable();
            $table->text('pesanan_pembelian_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_pembelian');
    }
};
