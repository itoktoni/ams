<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_service_item', function (Blueprint $table) {
            $table->id('template_service_item_id');
            $table->unsignedBigInteger('template_service_item_id_template');
            $table->string('template_service_item_nama');
            $table->string('template_service_item_tipe')->nullable();
            $table->unsignedBigInteger('template_service_item_id_suku_cadang')->nullable();
            $table->decimal('template_service_item_jumlah', 15, 2)->default(1);
            $table->integer('template_service_item_urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_service_item');
    }
};
