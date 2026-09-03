<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_aset', function (Blueprint $table) {
            $table->id('penjualan_aset_id');
            $table->string('penjualan_aset_nomor')->unique();
            $table->unsignedBigInteger('penjualan_aset_id_aset');
            $table->text('penjualan_aset_alasan');
            $table->decimal('penjualan_aset_nilai_buku', 15, 2)->default(0);
            $table->decimal('penjualan_aset_harga_appraisal', 15, 2)->nullable();
            $table->decimal('penjualan_aset_harga_jual', 15, 2)->nullable();
            $table->string('penjualan_aset_status')->nullable();
            $table->dateTime('penjualan_aset_tanggal_request')->nullable();
            $table->date('penjualan_aset_tanggal_jual')->nullable();
            $table->date('penjualan_aset_tanggal_serah_terima')->nullable();
            $table->string('penjualan_aset_penerima')->nullable();
            $table->string('penjualan_aset_kondisi')->nullable();
            $table->string('penjualan_aset_foto_serah_terima')->nullable();
            $table->decimal('penjualan_aset_gain_loss', 15, 2)->nullable();
            $table->text('penjualan_aset_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_aset');
    }
};
