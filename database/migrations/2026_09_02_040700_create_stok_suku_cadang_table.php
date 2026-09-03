<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_suku_cadang', function (Blueprint $table) {
            $table->id('stok_suku_cadang_id');
            $table->unsignedBigInteger('stok_suku_cadang_id_suku_cadang')->nullable()->index();
            $table->unsignedBigInteger('stok_suku_cadang_id_gudang')->nullable()->index();
            $table->string('stok_suku_cadang_bin', 2)->nullable();
            $table->decimal('stok_suku_cadang_jumlah', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(
                [
                    'stok_suku_cadang_id_suku_cadang',
                    'stok_suku_cadang_id_gudang',
                    'stok_suku_cadang_bin',
                ],
                'uq_stok_suku_cadang'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_suku_cadang');
    }
};
