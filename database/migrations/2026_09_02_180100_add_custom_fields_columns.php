<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aset_kategori', function (Blueprint $table) {
            // Per-category custom field DEFINITIONS, e.g.
            // [{"key":"no_stnk","label":"No STNK","type":"text","options":""}, ...]
            $table->json('aset_kategori_custom_fields')->nullable()->after('aset_kategori_keterangan');
        });

        Schema::table('aset', function (Blueprint $table) {
            // Per-asset custom field VALUES keyed by definition key, e.g.
            // {"no_stnk":"B1234XYZ","no_kir":"KIR-0099"}
            $table->json('aset_custom_fields')->nullable()->after('aset_jam_pakai');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('aset_kategori', 'aset_kategori_custom_fields')) {
            Schema::table('aset_kategori', function (Blueprint $table) {
                $table->dropColumn('aset_kategori_custom_fields');
            });
        }

        if (Schema::hasColumn('aset', 'aset_custom_fields')) {
            Schema::table('aset', function (Blueprint $table) {
                $table->dropColumn('aset_custom_fields');
            });
        }
    }
};
