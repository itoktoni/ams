<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guarded so it is safe to re-run on a fresh DB where the column no longer exists.
        if (Schema::hasColumn('aset', 'aset_km')) {
            Schema::table('aset', function ($table) {
                $table->dropColumn('aset_km');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('aset', 'aset_km')) {
            Schema::table('aset', function ($table) {
                $table->decimal('aset_km', 15, 2)->default(0)->after('aset_kode_qr');
            });
        }
    }
};
