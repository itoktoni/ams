<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jenis_kendaraan', 20)->nullable()->after('role'); // mobil / motor
            $table->string('kendaraan', 100)->nullable()->after('jenis_kendaraan'); // nama / no. polisi
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jenis_kendaraan', 'kendaraan']);
        });
    }
};
