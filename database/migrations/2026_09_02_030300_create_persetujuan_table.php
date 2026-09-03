<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persetujuan', function (Blueprint $table) {
            $table->id('persetujuan_id');
            $table->string('persetujuan_modul');
            $table->unsignedBigInteger('persetujuan_id_referensi')->nullable();
            $table->string('persetujuan_level');
            $table->unsignedBigInteger('persetujuan_id_user')->nullable();
            $table->string('persetujuan_status');
            $table->text('persetujuan_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persetujuan');
    }
};
