<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penawaran_penjualan', function (Blueprint $table) {
            $table->unsignedBigInteger('penawaran_penjualan_id_user')->nullable()->after('penawaran_penjualan_id_penjualan')->index();
            $table->dateTime('penawaran_penjualan_waktu')->nullable()->after('penawaran_penjualan_tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('penawaran_penjualan', function (Blueprint $table) {
            $table->dropColumn(['penawaran_penjualan_id_user', 'penawaran_penjualan_waktu']);
        });
    }
};