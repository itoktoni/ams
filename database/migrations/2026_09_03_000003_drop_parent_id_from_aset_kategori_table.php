<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('aset_kategori', 'aset_kategori_parent_id')) {
            Schema::table('aset_kategori', function (Blueprint $table) {
                $table->dropColumn('aset_kategori_parent_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('aset_kategori', 'aset_kategori_parent_id')) {
            Schema::table('aset_kategori', function (Blueprint $table) {
                $table->unsignedBigInteger('aset_kategori_parent_id')->nullable()->index()->after('aset_kategori_kode');
            });
        }
    }
};
