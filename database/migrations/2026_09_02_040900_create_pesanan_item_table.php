<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_item', function (Blueprint $table) {
            $table->id('pesanan_item_id');
            $table->unsignedBigInteger('pesanan_item_id_pesanan')->nullable()->index();
            $table->string('pesanan_item_tipe', 30)->nullable();
            $table->unsignedBigInteger('pesanan_item_id_referensi')->nullable();
            $table->string('pesanan_item_nama', 200);
            $table->decimal('pesanan_item_jumlah', 15, 2)->default(1);
            $table->decimal('pesanan_item_harga', 15, 2)->default(0);
            $table->decimal('pesanan_item_subtotal', 15, 2)->default(0);
            $table->decimal('pesanan_item_diterima', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_item');
    }
};
