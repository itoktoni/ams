<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->boolean('aset_bisa_dipinjam')->default(true)->after('aset_kondisi');
        });
        // Data lama dianggap bisa dipinjam (perilaku sekarang: semua masuk list)
        \Illuminate\Support\Facades\DB::table('aset')->whereNull('aset_bisa_dipinjam')->update(['aset_bisa_dipinjam' => true]);
    }

    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->dropColumn('aset_bisa_dipinjam');
        });
    }
};
