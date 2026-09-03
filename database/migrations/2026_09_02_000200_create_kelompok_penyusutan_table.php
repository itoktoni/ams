<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok_penyusutan', function (Blueprint $table) {
            $table->id('kelompok_penyusutan_id');
            $table->string('kelompok_penyusutan_kode', 30);
            $table->string('kelompok_penyusutan_nama', 150);
            $table->integer('kelompok_penyusutan_masa_manfaat')->default(0);
            $table->string('kelompok_penyusutan_metode', 30)->nullable();
            $table->decimal('kelompok_penyusutan_tarif', 15, 2)->default(0);
            $table->text('kelompok_penyusutan_keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok_penyusutan');
    }
};
