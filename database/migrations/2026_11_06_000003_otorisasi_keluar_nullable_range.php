<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otorisasi_keluar', function (Blueprint $table) {
            $table->dateTime('otorisasi_keluar_tanggal_mulai')->nullable()->change();
            $table->dateTime('otorisasi_keluar_tanggal_selesai')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('otorisasi_keluar', function (Blueprint $table) {
            $table->dateTime('otorisasi_keluar_tanggal_mulai')->nullable(false)->change();
            $table->dateTime('otorisasi_keluar_tanggal_selesai')->nullable(false)->change();
        });
    }
};
