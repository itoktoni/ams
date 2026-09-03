<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_service', function (Blueprint $table) {
            $table->id('template_service_id');
            $table->string('template_service_kode');
            $table->string('template_service_nama');
            $table->unsignedBigInteger('template_service_id_kategori')->nullable();
            $table->integer('template_service_interval_bulan')->nullable();
            $table->decimal('template_service_interval_jam', 15, 2)->nullable();
            $table->decimal('template_service_estimasi_jam', 15, 2)->nullable();
            $table->text('template_service_keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_service');
    }
};
