<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('aset_kategori', 'aset_kategori_kelompok_pajak')) {
            Schema::table('aset_kategori', function (Blueprint $table) {
                $table->dropColumn('aset_kategori_kelompok_pajak');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('aset_kategori', 'aset_kategori_kelompok_pajak')) {
            Schema::table('aset_kategori', function (Blueprint $table) {
                $table->string('aset_kategori_kelompok_pajak', 50)->nullable()->after('aset_kategori_metode_penyusutan');
            });
        }
    }
};
