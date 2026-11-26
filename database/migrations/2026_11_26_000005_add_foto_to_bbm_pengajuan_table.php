<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bbm_pengajuan', function (Blueprint $table) {
            $table->string('bbm_pengajuan_foto_sebelum', 255)->nullable()->after('bbm_pengajuan_catatan');
            $table->string('bbm_pengajuan_foto_sesudah', 255)->nullable()->after('bbm_pengajuan_foto_sebelum');
            $table->string('bbm_pengajuan_foto_struk', 255)->nullable()->after('bbm_pengajuan_foto_sesudah');
        });
    }

    public function down(): void
    {
        Schema::table('bbm_pengajuan', function (Blueprint $table) {
            $table->dropColumn(['bbm_pengajuan_foto_sebelum', 'bbm_pengajuan_foto_sesudah', 'bbm_pengajuan_foto_struk']);
        });
    }
};
