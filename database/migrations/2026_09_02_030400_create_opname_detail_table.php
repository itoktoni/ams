<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opname_detail', function (Blueprint $table) {
            $table->id('opname_detail_id');
            $table->unsignedBigInteger('opname_detail_id_opname')->nullable();
            $table->unsignedBigInteger('opname_detail_id_aset')->nullable();
            $table->string('opname_detail_status_sistem')->nullable();
            $table->string('opname_detail_status_fisik')->nullable();
            $table->string('opname_detail_kondisi')->nullable();
            $table->boolean('opname_detail_ditemukan')->default(false);
            $table->text('opname_detail_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opname_detail');
    }
};
