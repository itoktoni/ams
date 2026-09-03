<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('buku_penyusutan', 'buku_penyusutan_debet') && ! Schema::hasColumn('buku_penyusutan', 'buku_penyusutan_nilai')) {
            Schema::table('buku_penyusutan', function (Blueprint $table) {
                $table->renameColumn('buku_penyusutan_debet', 'buku_penyusutan_nilai');
            });
        }
        if (Schema::hasColumn('buku_penyusutan', 'buku_penyusutan_kredit')) {
            Schema::table('buku_penyusutan', function (Blueprint $table) {
                $table->dropColumn('buku_penyusutan_kredit');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('buku_penyusutan', 'buku_penyusutan_kredit')) {
            Schema::table('buku_penyusutan', function (Blueprint $table) {
                $table->decimal('buku_penyusutan_kredit', 15, 2)->default(0)->after('buku_penyusutan_nilai');
            });
        }
        if (Schema::hasColumn('buku_penyusutan', 'buku_penyusutan_nilai') && ! Schema::hasColumn('buku_penyusutan', 'buku_penyusutan_debet')) {
            Schema::table('buku_penyusutan', function (Blueprint $table) {
                $table->renameColumn('buku_penyusutan_nilai', 'buku_penyusutan_debet');
            });
        }
    }
};
