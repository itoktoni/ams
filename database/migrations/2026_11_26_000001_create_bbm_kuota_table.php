<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bbm_kuota', function (Blueprint $table) {
            $table->id('bbm_kuota_id');
            $table->unsignedBigInteger('bbm_kuota_id_user')->index('idx_bbm_kuota_user');
            $table->string('bbm_kuota_periode', 7); // YYYY-MM
            $table->decimal('bbm_kuota_liter', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['bbm_kuota_id_user', 'bbm_kuota_periode'], 'uq_bbm_kuota_user_periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbm_kuota');
    }
};
