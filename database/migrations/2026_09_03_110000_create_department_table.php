<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department', function (Blueprint $table) {
            $table->id('department_id');
            $table->string('department_kode', 20)->unique();
            $table->string('department_nama', 100);
            $table->decimal('department_budget', 15, 2)->default(0);
            $table->decimal('department_budget_terpakai', 15, 2)->default(0);
            $table->string('department_periode', 20)->default('bulanan');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('role')->index('idx_users_dept');
        });

        Schema::table('permintaan_suku_cadang', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('permintaan_suku_cadang_id_peminta')->index('idx_perm_dept');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_suku_cadang', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::dropIfExists('department');
    }
};
