<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reputasi_peminjam', function (Blueprint $table) {
            $table->id('reputasi_peminjam_id');
            $table->unsignedBigInteger('reputasi_peminjam_id_user')->nullable();
            $table->decimal('reputasi_peminjam_skor', 15, 2)->default(100);
            $table->integer('reputasi_peminjam_total_pinjam')->default(0);
            $table->integer('reputasi_peminjam_terlambat')->default(0);
            $table->integer('reputasi_peminjam_limit_pinjam')->default(3);
            $table->integer('reputasi_peminjam_durasi_maks')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reputasi_peminjam');
    }
};
