<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset', function (Blueprint $table) {
            $table->id('aset_id');
            $table->string('aset_kode', 40)->unique();
            $table->string('aset_nama', 200);
            $table->unsignedBigInteger('aset_id_kategori')->nullable()->index();
            $table->unsignedBigInteger('aset_id_lokasi')->nullable()->index();
            $table->unsignedBigInteger('aset_id_penanggung_jawab')->nullable()->index();
            $table->unsignedBigInteger('aset_id_vendor')->nullable()->index();
            $table->string('aset_merek', 80)->nullable();
            $table->string('aset_model', 80)->nullable();
            $table->string('aset_nomor_seri', 100)->nullable();
            $table->date('aset_tanggal_perolehan')->nullable();
            $table->decimal('aset_harga_perolehan', 15, 2)->default(0);
            $table->decimal('aset_nilai_sisa', 15, 2)->default(0);
            $table->integer('aset_masa_manfaat')->default(0);
            $table->string('aset_metode_penyusutan', 30)->nullable();
            $table->date('aset_tanggal_mulai_susut')->nullable();
            $table->string('aset_status', 30)->nullable();
            $table->string('aset_kondisi', 30)->nullable();
            $table->string('aset_foto', 255)->nullable();
            $table->string('aset_kode_qr', 100)->nullable();
            $table->decimal('aset_jam_pakai', 15, 2)->default(0);
            $table->text('aset_catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
