<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_suku_cadang', function (Blueprint $table) {
            $table->id('permintaan_suku_cadang_id');
            $table->string('permintaan_suku_cadang_nomor', 50)->unique();
            $table->unsignedBigInteger('permintaan_suku_cadang_id_tiket')->nullable()->index('idx_permintaan_tiket');
            $table->unsignedBigInteger('permintaan_suku_cadang_id_suku_cadang')->index('idx_permintaan_sc');
            $table->unsignedBigInteger('permintaan_suku_cadang_id_peminta')->index('idx_permintaan_peminta');
            $table->decimal('permintaan_suku_cadang_jumlah', 15, 2)->default(1);
            $table->decimal('permintaan_suku_cadang_harga', 15, 2)->default(0);
            $table->decimal('permintaan_suku_cadang_subtotal', 15, 2)->default(0);
            $table->string('permintaan_suku_cadang_status', 50)->default('menunggu');
            $table->dateTime('permintaan_suku_cadang_tanggal_permintaan')->nullable();
            $table->text('permintaan_suku_cadang_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_suku_cadang');
    }
};
