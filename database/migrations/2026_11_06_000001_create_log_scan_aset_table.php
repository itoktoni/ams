<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_scan_aset', function (Blueprint $table) {
            $table->id('log_scan_aset_id');
            $table->unsignedBigInteger('log_scan_aset_id_aset')->nullable()->index();
            $table->string('log_scan_aset_kode', 100)->index();
            $table->string('log_scan_aset_sumber', 20)->default('rfid');
            $table->string('log_scan_aset_ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_scan_aset');
    }
};
