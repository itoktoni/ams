<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teknisi', function (Blueprint $table) {
            $table->id('teknisi_id');

            $table->unsignedBigInteger('teknisi_id_user')->nullable()->index();

            $table->string('teknisi_kode');
            $table->string('teknisi_nama');
            $table->string('teknisi_telepon')->nullable();
            $table->json('teknisi_keahlian')->nullable();
            $table->json('teknisi_zona')->nullable();
            $table->json('teknisi_sertifikasi')->nullable();
            $table->decimal('teknisi_rating', 15, 2)->default(0);
            $table->integer('teknisi_total_tiket')->default(0);
            $table->integer('teknisi_total_revisi')->default(0);
            $table->decimal('teknisi_latitude', 15, 8)->nullable();
            $table->decimal('teknisi_longitude', 15, 8)->nullable();
            $table->dateTime('teknisi_waktu_posisi')->nullable();
            $table->string('teknisi_status');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teknisi');
    }
};
