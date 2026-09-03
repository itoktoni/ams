<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan', function (Blueprint $table) {
            $table->id('penerimaan_id');
            $table->unsignedBigInteger('penerimaan_id_pesanan')->nullable()->index();
            $table->string('penerimaan_nomor', 60);
            $table->date('penerimaan_tanggal')->nullable();
            $table->string('penerimaan_foto', 255)->nullable();
            $table->string('penerimaan_penerima', 120);
            $table->text('penerimaan_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan');
    }
};
