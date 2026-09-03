<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pergerakan_stok', function (Blueprint $table) {
            $table->id('pergerakan_stok_id');
            $table->unsignedBigInteger('pergerakan_stok_id_suku_cadang')->nullable()->index();
            $table->unsignedBigInteger('pergerakan_stok_id_gudang')->nullable()->index();
            $table->string('pergerakan_stok_tipe', 30)->nullable();
            $table->decimal('pergerakan_stok_jumlah', 15, 2)->default(0);
            $table->string('pergerakan_stok_referensi', 120)->nullable();
            $table->text('pergerakan_stok_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pergerakan_stok');
    }
};
