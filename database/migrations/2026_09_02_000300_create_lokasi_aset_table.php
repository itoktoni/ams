<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_lokasi', function (Blueprint $table) {
            $table->id('aset_lokasi_id');
            $table->string('aset_lokasi_nama', 150);
            $table->string('aset_lokasi_kode', 30);
            $table->text('aset_lokasi_alamat')->nullable();
            $table->string('aset_lokasi_zona', 50)->nullable();
            $table->decimal('aset_lokasi_latitude', 15, 8)->nullable();
            $table->decimal('aset_lokasi_longitude', 15, 8)->nullable();
            $table->unsignedBigInteger('aset_lokasi_parent_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_lokasi');
    }
};
