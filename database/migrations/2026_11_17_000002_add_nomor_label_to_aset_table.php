<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->string('aset_nomor_label', 100)->nullable()->unique()->after('aset_kode_qr');
        });
    }

    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->dropColumn('aset_nomor_label');
        });
    }
};
