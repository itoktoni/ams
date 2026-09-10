<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            // Bukti pekerjaan harian — analisa & kesimpulan terpisah
            $table->text('tiket_analisa')->nullable()->after('tiket_catatan');
            $table->text('tiket_kesimpulan')->nullable()->after('tiket_analisa');
            $table->dateTime('tiket_tanggal_analisa')->nullable()->after('tiket_kesimpulan');
            $table->dateTime('tiket_tanggal_kesimpulan')->nullable()->after('tiket_tanggal_analisa');
            // Hash foto untuk integritas bukti
            $table->string('tiket_foto_sebelum_hash', 64)->nullable()->after('tiket_foto_sebelum');
            $table->string('tiket_foto_sesudah_hash', 64)->nullable()->after('tiket_foto_sesudah');
            // Lease untuk auto-release claim yang tidak diproses
            $table->dateTime('tiket_tanggal_lease_expires')->nullable()->after('tiket_tanggal_verifikasi');
            $table->integer('tiket_durasi_menit')->nullable()->after('tiket_tanggal_lease_expires');

            $table->index(['tiket_status', 'tiket_tanggal_lease_expires'], 'idx_tiket_status_lease');
            $table->index(['tiket_id_teknisi', 'tiket_status'], 'idx_tiket_teknisi_status');
            $table->index('tiket_tanggal_selesai', 'idx_tiket_tanggal_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropIndex('idx_tiket_status_lease');
            $table->dropIndex('idx_tiket_teknisi_status');
            $table->dropIndex('idx_tiket_tanggal_selesai');
            $table->dropColumn([
                'tiket_analisa',
                'tiket_kesimpulan',
                'tiket_tanggal_analisa',
                'tiket_tanggal_kesimpulan',
                'tiket_foto_sebelum_hash',
                'tiket_foto_sesudah_hash',
                'tiket_tanggal_lease_expires',
                'tiket_durasi_menit',
            ]);
        });
    }
};
