<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Serah terima — awal peminjaman
            $table->decimal('peminjaman_km_awal', 15, 2)->nullable()->after('peminjaman_tujuan');
            $table->string('peminjaman_foto_km_awal', 255)->nullable()->after('peminjaman_km_awal');
            $table->string('peminjaman_bensin_awal', 20)->nullable()->after('peminjaman_foto_km_awal');
            $table->string('peminjaman_foto_bensin_awal', 255)->nullable()->after('peminjaman_bensin_awal');
            $table->string('peminjaman_kondisi_awal', 100)->nullable()->after('peminjaman_foto_bensin_awal');
            $table->string('peminjaman_foto_rusak_awal', 255)->nullable()->after('peminjaman_kondisi_awal');
            // Pengembalian — akhir peminjaman
            $table->decimal('peminjaman_km_akhir', 15, 2)->nullable()->after('peminjaman_kondisi_kembali');
            $table->string('peminjaman_foto_km_akhir', 255)->nullable()->after('peminjaman_km_akhir');
            $table->string('peminjaman_bensin_akhir', 20)->nullable()->after('peminjaman_foto_km_akhir');
            $table->string('peminjaman_foto_bensin_akhir', 255)->nullable()->after('peminjaman_bensin_akhir');
            $table->string('peminjaman_foto_rusak_akhir', 255)->nullable()->after('peminjaman_foto_bensin_akhir');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'peminjaman_km_awal',
                'peminjaman_foto_km_awal',
                'peminjaman_bensin_awal',
                'peminjaman_foto_bensin_awal',
                'peminjaman_kondisi_awal',
                'peminjaman_foto_rusak_awal',
                'peminjaman_km_akhir',
                'peminjaman_foto_km_akhir',
                'peminjaman_bensin_akhir',
                'peminjaman_foto_bensin_akhir',
                'peminjaman_foto_rusak_akhir',
            ]);
        });
    }
};
