<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $drop = [];
            foreach (['telegram_username','teknisi_kode','teknisi_keahlian','teknisi_zona','teknisi_sertifikasi','teknisi_rating','teknisi_total_tiket','teknisi_total_revisi','teknisi_latitude','teknisi_longitude','teknisi_waktu_posisi','teknisi_status'] as $col) {
                if (Schema::hasColumn('users', $col)) $drop[] = $col;
            }
            if (!empty($drop)) $table->dropColumn($drop);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'telegram_username')) $table->string('telegram_username', 50)->nullable()->after('telegram_id');
            if (!Schema::hasColumn('users', 'teknisi_kode')) $table->string('teknisi_kode')->nullable()->after('telegram_username');
            if (!Schema::hasColumn('users', 'teknisi_keahlian')) $table->json('teknisi_keahlian')->nullable()->after('teknisi_kode');
            if (!Schema::hasColumn('users', 'teknisi_zona')) $table->json('teknisi_zona')->nullable()->after('teknisi_keahlian');
            if (!Schema::hasColumn('users', 'teknisi_sertifikasi')) $table->json('teknisi_sertifikasi')->nullable()->after('teknisi_zona');
            if (!Schema::hasColumn('users', 'teknisi_rating')) $table->decimal('teknisi_rating', 15, 2)->default(0)->after('teknisi_sertifikasi');
            if (!Schema::hasColumn('users', 'teknisi_total_tiket')) $table->integer('teknisi_total_tiket')->default(0)->after('teknisi_rating');
            if (!Schema::hasColumn('users', 'teknisi_total_revisi')) $table->integer('teknisi_total_revisi')->default(0)->after('teknisi_total_tiket');
            if (!Schema::hasColumn('users', 'teknisi_latitude')) $table->decimal('teknisi_latitude', 15, 8)->nullable()->after('teknisi_total_revisi');
            if (!Schema::hasColumn('users', 'teknisi_longitude')) $table->decimal('teknisi_longitude', 15, 8)->nullable()->after('teknisi_latitude');
            if (!Schema::hasColumn('users', 'teknisi_waktu_posisi')) $table->dateTime('teknisi_waktu_posisi')->nullable()->after('teknisi_longitude');
            if (!Schema::hasColumn('users', 'teknisi_status')) $table->string('teknisi_status', 50)->nullable()->after('teknisi_waktu_posisi');
        });
    }
};
