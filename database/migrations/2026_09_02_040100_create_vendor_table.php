<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor', function (Blueprint $table) {
            $table->id('vendor_id');
            $table->string('vendor_kode', 40);
            $table->string('vendor_nama', 200);
            $table->string('vendor_telepon', 40)->nullable();
            $table->string('vendor_email', 120)->nullable();
            $table->text('vendor_alamat')->nullable();
            $table->string('vendor_kategori', 60)->nullable();
            $table->decimal('vendor_rating', 15, 2)->default(0);
            $table->text('vendor_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor');
    }
};
