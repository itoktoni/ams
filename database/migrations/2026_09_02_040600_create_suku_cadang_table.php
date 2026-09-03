<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suku_cadang', function (Blueprint $table) {
            $table->id('suku_cadang_id');
            $table->string('suku_cadang_kode', 40);
            $table->string('suku_cadang_nama', 200);
            $table->text('suku_cadang_spesifikasi')->nullable();
            $table->unsignedBigInteger('suku_cadang_id_vendor')->nullable()->index();
            $table->decimal('suku_cadang_harga', 15, 2)->default(0);
            $table->unsignedBigInteger('suku_cadang_id_gudang')->nullable()->index();
            $table->decimal('suku_cadang_stok_minimum', 15, 2)->default(0);
            $table->decimal('suku_cadang_stok_maksimum', 15, 2)->default(0);
            $table->decimal('suku_cadang_bin_aktif', 15, 2)->default(0);
            $table->decimal('suku_cadang_bin_buffer', 15, 2)->default(0);
            $table->string('suku_cadang_satuan', 30)->nullable();
            $table->json('suku_cadang_kompatibilitas')->nullable();
            $table->string('suku_cadang_foto', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suku_cadang');
    }
};
