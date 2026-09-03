<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket', function (Blueprint $table) {
            $table->id('tiket_id');

            $table->string('tiket_nomor')->unique();
            $table->unsignedBigInteger('tiket_id_aset')->index();

            $table->unsignedBigInteger('tiket_id_pelapor')->index();

            $table->unsignedBigInteger('tiket_id_teknisi')->nullable()->index();

            $table->string('tiket_judul');
            $table->text('tiket_deskripsi')->nullable();
            $table->string('tiket_tingkat_urgensi');
            $table->string('tiket_status');

            $table->unsignedBigInteger('tiket_id_lokasi')->nullable()->index();

            $table->decimal('tiket_latitude', 15, 8)->nullable();
            $table->decimal('tiket_longitude', 15, 8)->nullable();
            $table->string('tiket_foto_sebelum')->nullable();
            $table->string('tiket_foto_sesudah')->nullable();

            $table->dateTime('tiket_tanggal_lapor');
            $table->dateTime('tiket_tanggal_tugas')->nullable();
            $table->dateTime('tiket_tanggal_mulai')->nullable();
            $table->dateTime('tiket_tanggal_selesai')->nullable();
            $table->dateTime('tiket_tanggal_verifikasi')->nullable();
            $table->dateTime('tiket_jatuh_tempo')->nullable();
            $table->boolean('tiket_terlambat_sla')->default(false);
            $table->integer('tiket_level_eskalasi')->default(0);

            $table->unsignedBigInteger('tiket_id_batch')->nullable()->index();

            $table->decimal('tiket_biaya', 15, 2)->default(0);
            $table->decimal('tiket_rating', 15, 2)->nullable();
            $table->text('tiket_catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket');
    }
};
