<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faktur', function (Blueprint $table) {
            $table->id('faktur_id');
            $table->string('faktur_nomor', 60);
            $table->unsignedBigInteger('faktur_id_pesanan')->nullable()->index();
            $table->date('faktur_tanggal')->nullable();
            $table->decimal('faktur_total', 15, 2)->default(0);
            $table->string('faktur_status', 30)->nullable();
            $table->string('faktur_file', 255)->nullable();
            $table->text('faktur_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faktur');
    }
};
