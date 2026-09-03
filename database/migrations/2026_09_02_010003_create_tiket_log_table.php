<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_log', function (Blueprint $table) {
            $table->id('tiket_log_id');

            $table->unsignedBigInteger('tiket_log_id_tiket')->index();

            $table->string('tiket_log_status_dari')->nullable();
            $table->string('tiket_log_status_ke');
            $table->unsignedBigInteger('tiket_log_actor')->nullable()->index();

            $table->text('tiket_log_catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_log');
    }
};
