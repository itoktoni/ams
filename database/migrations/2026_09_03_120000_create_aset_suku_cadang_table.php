<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_suku_cadang', function (Blueprint $table) {
            $table->id('aset_suku_cadang_id');
            $table->unsignedBigInteger('aset_id')->index('idx_asc_aset');
            $table->unsignedBigInteger('suku_cadang_id')->index('idx_asc_sc');
            $table->decimal('jumlah', 15, 2)->default(1);
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
            $table->unique(['aset_id', 'suku_cadang_id'], 'uq_aset_sc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_suku_cadang');
    }
};
