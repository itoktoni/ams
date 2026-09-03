<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_status_aset', function (Blueprint $table) {
            $table->id('log_status_aset_id');
            $table->unsignedBigInteger('log_status_aset_id_aset')->nullable()->index();
            $table->string('log_status_aset_status_dari', 30)->nullable();
            $table->string('log_status_aset_status_ke', 30)->nullable();
            $table->unsignedBigInteger('log_status_aset_actor')->nullable()->index();
            $table->text('log_status_aset_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_status_aset');
    }
};
