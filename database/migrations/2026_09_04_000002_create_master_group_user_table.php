<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_group_user', function (Blueprint $table) {
            $table->id('master_group_user_id');
            $table->unsignedBigInteger('master_group_user_id_group')->index();
            $table->unsignedBigInteger('master_group_user_id_user')->index();
            $table->timestamps();
            $table->unique(['master_group_user_id_group', 'master_group_user_id_user'], 'uniq_group_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_group_user');
    }
};
