<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penawaran_penjualan', function (Blueprint $table) {
            $table->id('penawaran_penjualan_id');
            $table->unsignedBigInteger('penawaran_penjualan_id_penjualan');
            $table->string('penawaran_penjualan_nama_pembeli');
            $table->string('penawaran_penjualan_kontak')->nullable();
            $table->decimal('penawaran_penjualan_harga', 15, 2)->default(0);
            $table->date('penawaran_penjualan_tanggal')->nullable();
            $table->string('penawaran_penjualan_status')->nullable();
            $table->text('penawaran_penjualan_hasil')->nullable();
            $table->text('penawaran_penjualan_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penawaran_penjualan');
    }
};
