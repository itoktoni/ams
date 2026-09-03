<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_suku_cadang', function (Blueprint $table) {
            $table->id('tiket_suku_cadang_id');

            $table->unsignedBigInteger('tiket_suku_cadang_id_tiket')->index();

            $table->unsignedBigInteger('tiket_suku_cadang_id_suku_cadang')->index();

            $table->decimal('tiket_suku_cadang_jumlah', 15, 2)->default(1);
            $table->decimal('tiket_suku_cadang_harga', 15, 2)->default(0);
            $table->decimal('tiket_suku_cadang_subtotal', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_suku_cadang');
    }
};
