<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_penyusutan', function (Blueprint $table) {
            $table->id('buku_penyusutan_id');
            $table->unsignedBigInteger('buku_penyusutan_id_aset')->nullable()->index();
            $table->string('buku_penyusutan_periode', 7)->nullable();
            $table->dateTime('buku_penyusutan_tanggal')->nullable();
            $table->decimal('buku_penyusutan_nilai', 15, 2)->default(0);
            $table->decimal('buku_penyusutan_akumulasi', 15, 2)->default(0);
            $table->decimal('buku_penyusutan_nilai_buku', 15, 2)->default(0);
            $table->unsignedBigInteger('buku_penyusutan_reversalisasi_dari')->nullable()->index();
            $table->string('buku_penyusutan_hash', 64)->nullable();
            $table->string('buku_penyusutan_hash_sebelum', 64)->nullable();
            $table->unsignedBigInteger('buku_penyusutan_dibuat_oleh')->nullable()->index();
            $table->timestamps();

            $table->unique([
                'buku_penyusutan_id_aset',
                'buku_penyusutan_periode',
            ], 'unq_buku_penyusutan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_penyusutan');
    }
};
