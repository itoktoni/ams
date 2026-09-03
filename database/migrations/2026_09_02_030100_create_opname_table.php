<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opname', function (Blueprint $table) {
            $table->id('opname_id');
            $table->string('opname_nomor')->unique();
            $table->unsignedBigInteger('opname_id_lokasi')->nullable();
            $table->date('opname_tanggal');
            $table->unsignedBigInteger('opname_id_petugas')->nullable();
            $table->string('opname_status');
            $table->integer('opname_total_sistem')->default(0);
            $table->integer('opname_total_fisik')->default(0);
            $table->integer('opname_total_selisih')->default(0);
            $table->text('opname_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opname');
    }
};
