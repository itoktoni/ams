<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_service', function (Blueprint $table) {
            $table->id('riwayat_service_id');
            $table->unsignedBigInteger('riwayat_service_id_aset');
            $table->unsignedBigInteger('riwayat_service_id_tiket')->nullable();
            $table->unsignedBigInteger('riwayat_service_id_teknisi')->nullable();
            $table->dateTime('riwayat_service_tanggal')->nullable();
            $table->string('riwayat_service_jenis');
            $table->decimal('riwayat_service_biaya', 15, 2)->default(0);
            $table->text('riwayat_service_catatan')->nullable();
            $table->json('riwayat_service_checklist')->nullable();
            $table->string('riwayat_service_ttd')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_service');
    }
};
