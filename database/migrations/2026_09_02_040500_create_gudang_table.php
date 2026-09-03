<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudang', function (Blueprint $table) {
            $table->id('gudang_id');
            $table->string('gudang_kode', 40);
            $table->string('gudang_nama', 200);
            $table->unsignedBigInteger('gudang_id_lokasi')->nullable()->index();
            $table->text('gudang_alamat')->nullable();
            $table->text('gudang_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gudang');
    }
};
