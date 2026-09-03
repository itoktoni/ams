<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opname', function (Blueprint $table) {
            $table->date('opname_tanggal_mulai')->nullable()->after('opname_tanggal');
            $table->date('opname_tanggal_selesai')->nullable()->after('opname_tanggal_mulai');
        });
        // Copy existing opname_tanggal to new fields for existing rows
        \Illuminate\Support\Facades\DB::statement("UPDATE opname SET opname_tanggal_mulai = opname_tanggal WHERE opname_tanggal_mulai IS NULL");
        \Illuminate\Support\Facades\DB::statement("UPDATE opname SET opname_tanggal_selesai = DATE_ADD(opname_tanggal, INTERVAL 7 DAY) WHERE opname_tanggal_selesai IS NULL");

        Schema::table('opname_detail', function (Blueprint $table) {
            $table->dateTime('opname_detail_waktu_scan')->nullable()->after('opname_detail_ditemukan');
            $table->unsignedBigInteger('opname_detail_id_petugas_scan')->nullable()->after('opname_detail_waktu_scan');
        });
    }

    public function down(): void
    {
        Schema::table('opname_detail', function (Blueprint $table) {
            $table->dropColumn(['opname_detail_waktu_scan', 'opname_detail_id_petugas_scan']);
        });
        Schema::table('opname', function (Blueprint $table) {
            $table->dropColumn(['opname_tanggal_mulai', 'opname_tanggal_selesai']);
        });
    }
};
