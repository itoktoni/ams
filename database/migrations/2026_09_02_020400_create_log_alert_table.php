<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_alert', function (Blueprint $table) {
            $table->id('log_alert_id');
            $table->unsignedBigInteger('log_alert_id_alert')->nullable();
            $table->string('log_alert_kanal');
            $table->string('log_alert_tujuan');
            $table->string('log_alert_status');
            $table->boolean('log_alert_dibuka')->default(false);
            $table->text('log_alert_pesan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_alert');
    }
};
