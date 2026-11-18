<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('pembayaran_id');
            $table->string('pembayaran_jenis', 30)->index();
            $table->string('pembayaran_nama', 200);
            $table->string('pembayaran_nomor_pelanggan', 100)->nullable();
            $table->date('pembayaran_tanggal_tagihan')->nullable();
            $table->date('pembayaran_tanggal_jatuh_tempo')->nullable()->index();
            $table->date('pembayaran_tanggal_bayar')->nullable();
            $table->decimal('pembayaran_nominal', 15, 2)->default(0);
            $table->decimal('pembayaran_denda', 15, 2)->default(0);
            $table->string('pembayaran_status', 30)->default('belum_bayar')->index();
            $table->string('pembayaran_bukti', 255)->nullable();
            $table->text('pembayaran_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
