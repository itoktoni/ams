<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert', function (Blueprint $table) {
            $table->id('alert_id');
            $table->string('alert_tipe');
            $table->unsignedBigInteger('alert_id_referensi')->nullable();
            $table->string('alert_tipe_referensi')->nullable();
            $table->string('alert_judul');
            $table->text('alert_pesan');
            $table->string('alert_level');
            $table->string('alert_kunci_dedup')->nullable();
            $table->dateTime('alert_jatuh_tempo')->nullable();
            $table->unsignedBigInteger('alert_id_pic')->nullable();
            $table->string('alert_status');
            $table->integer('alert_level_eskalasi')->default(0);
            $table->dateTime('alert_terakhir_kirim')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert');
    }
};
