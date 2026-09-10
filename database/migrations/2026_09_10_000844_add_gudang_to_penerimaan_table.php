<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penerimaan', function (Blueprint $table) {
            if (!Schema::hasColumn('penerimaan', 'penerimaan_id_gudang')) {
                $table->unsignedBigInteger('penerimaan_id_gudang')->nullable()->after('penerimaan_id_pesanan')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('penerimaan', function (Blueprint $table) {
            if (Schema::hasColumn('penerimaan', 'penerimaan_id_gudang')) {
                $table->dropColumn('penerimaan_id_gudang');
            }
        });
    }
};
