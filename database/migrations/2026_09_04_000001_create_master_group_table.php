<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_group', function (Blueprint $table) {
            $table->id('master_group_id');
            $table->string('master_group_kode', 40)->unique();
            $table->string('master_group_nama', 100);
            $table->text('master_group_keterangan')->nullable();
            $table->string('master_group_status', 20)->default('aktif');
            $table->timestamps();
        });

        Schema::create('master_group_teknisi', function (Blueprint $table) {
            $table->id('master_group_teknisi_id');
            $table->unsignedBigInteger('master_group_teknisi_id_group')->index();
            $table->unsignedBigInteger('master_group_teknisi_id_teknisi')->index();
            $table->timestamps();

            $table->unique(['master_group_teknisi_id_group', 'master_group_teknisi_id_teknisi'], 'uniq_group_teknisi');
        });

        Schema::table('aset', function (Blueprint $table) {
            $table->unsignedBigInteger('aset_id_group')->nullable()->index()->after('aset_id_vendor');
        });
    }

    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->dropColumn('aset_id_group');
        });
        Schema::dropIfExists('master_group_teknisi');
        Schema::dropIfExists('master_group');
    }
};
