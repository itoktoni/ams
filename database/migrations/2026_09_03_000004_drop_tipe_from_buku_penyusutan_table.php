<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // drop old unique that includes tipe
        try {
            Schema::table('buku_penyusutan', function (Blueprint $table) {
                $table->dropUnique('unq_buku_penyusutan');
            });
        } catch (\Throwable $e) {
            // fallback for old long name (if migration failed before)
            try { DB::statement('ALTER TABLE buku_penyusutan DROP INDEX unq_buku_penyusutan'); } catch (\Throwable $e2) {}
            try { DB::statement('ALTER TABLE buku_penyusutan DROP INDEX buku_penyusutan_buku_penyusutan_id_aset_buku_penyusutan_periode_buku_penyusutan_tipe_unique'); } catch (\Throwable $e2) {}
        }

        if (Schema::hasColumn('buku_penyusutan', 'buku_penyusutan_tipe')) {
            Schema::table('buku_penyusutan', function (Blueprint $table) {
                $table->dropColumn('buku_penyusutan_tipe');
            });
        }

        // recreate unique on [id_aset, periode] only
        Schema::table('buku_penyusutan', function (Blueprint $table) {
            $table->unique(['buku_penyusutan_id_aset','buku_penyusutan_periode'], 'unq_buku_penyusutan');
        });
    }

    public function down(): void
    {
        try {
            Schema::table('buku_penyusutan', function (Blueprint $table) {
                $table->dropUnique('unq_buku_penyusutan');
            });
        } catch (\Throwable $e) {}

        if (! Schema::hasColumn('buku_penyusutan', 'buku_penyusutan_tipe')) {
            Schema::table('buku_penyusutan', function (Blueprint $table) {
                $table->string('buku_penyusutan_tipe', 30)->nullable()->after('buku_penyusutan_nilai_buku');
            });
        }

        Schema::table('buku_penyusutan', function (Blueprint $table) {
            $table->unique(['buku_penyusutan_id_aset','buku_penyusutan_periode','buku_penyusutan_tipe'], 'unq_buku_penyusutan');
        });
    }
};
