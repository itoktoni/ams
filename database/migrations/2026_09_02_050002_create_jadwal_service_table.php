<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_service', function (Blueprint $table) {
            $table->id('jadwal_service_id');
            $table->unsignedBigInteger('jadwal_service_id_aset');
            $table->unsignedBigInteger('jadwal_service_id_template')->nullable();
            $table->date('jadwal_service_tanggal_mulai')->nullable();
            $table->date('jadwal_service_tanggal_jatuh_tempo')->nullable();
            $table->integer('jadwal_service_interval_bulan')->nullable();
            $table->decimal('jadwal_service_interval_jam', 15, 2)->nullable();
            $table->decimal('jadwal_service_odometer_terakhir', 15, 2)->nullable();
            $table->decimal('jadwal_service_jam_terakhir', 15, 2)->nullable();
            $table->string('jadwal_service_status')->nullable();
            $table->date('jadwal_service_tanggal_terakhir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_service');
    }
};
