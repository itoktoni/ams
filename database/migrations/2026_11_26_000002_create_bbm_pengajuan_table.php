<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bbm_pengajuan', function (Blueprint $table) {
            $table->id('bbm_pengajuan_id');
            $table->string('bbm_pengajuan_nomor', 50)->unique();
            $table->unsignedBigInteger('bbm_pengajuan_id_user')->index('idx_bbm_pengajuan_user');
            $table->string('bbm_pengajuan_periode', 7)->index('idx_bbm_pengajuan_periode'); // YYYY-MM
            $table->string('bbm_pengajuan_jenis_kendaraan', 20)->default('mobil');
            $table->string('bbm_pengajuan_kendaraan', 100);
            $table->decimal('bbm_pengajuan_liter', 12, 2);
            $table->string('bbm_pengajuan_status', 20)->default('menunggu');
            $table->dateTime('bbm_pengajuan_tanggal')->nullable();
            $table->text('bbm_pengajuan_catatan')->nullable();
            $table->unsignedBigInteger('bbm_pengajuan_id_approver')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbm_pengajuan');
    }
};
