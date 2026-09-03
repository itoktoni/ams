<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_kategori', function (Blueprint $table) {
            $table->id('aset_kategori_id');
            $table->string('aset_kategori_nama', 150);
            $table->string('aset_kategori_kode', 30)->unique();
            $table->integer('aset_kategori_masa_manfaat')->default(0);
            $table->string('aset_kategori_metode_penyusutan', 30)->nullable();
            $table->text('aset_kategori_keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_kategori');
    }
};
