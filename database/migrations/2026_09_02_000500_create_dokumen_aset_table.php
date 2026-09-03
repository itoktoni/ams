<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_dokumen', function (Blueprint $table) {
            $table->id('aset_dokumen_id');
            $table->unsignedBigInteger('aset_dokumen_id_aset')->nullable()->index();
            $table->string('aset_dokumen_jenis', 30)->nullable();
            $table->string('aset_dokumen_nomor', 80)->nullable();
            $table->string('aset_dokumen_file', 255)->nullable();
            $table->date('aset_dokumen_tanggal_terbit')->nullable();
            $table->date('aset_dokumen_tanggal_expired')->nullable();
            $table->text('aset_dokumen_keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_dokumen');
    }
};
