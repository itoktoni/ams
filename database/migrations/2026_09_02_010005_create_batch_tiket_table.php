<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_tiket', function (Blueprint $table) {
            $table->id('batch_tiket_id');

            $table->string('batch_tiket_kode');
            $table->unsignedBigInteger('batch_tiket_id_teknisi')->index();

            $table->date('batch_tiket_tanggal');
            $table->string('batch_tiket_zona')->nullable();
            $table->string('batch_tiket_mode');
            $table->string('batch_tiket_status');
            $table->json('batch_tiket_urutan')->nullable();
            $table->decimal('batch_tiket_total_eta', 15, 2)->nullable();
            $table->decimal('batch_tiket_total_jarak', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_tiket');
    }
};
