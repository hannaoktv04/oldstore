<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('users_role')) {
            Schema::dropIfExists('users_role');
        }

        if (Schema::hasTable('roles')) {
            Schema::dropIfExists('roles');
        }
    }

    public function down()
    {
        Schema::create('roles', function ($table) {
            $table->id();
            $table->string('nama_role');
            $table->timestamps();
        });

        Schema::create('users_role', function ($table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
        });
    }
};
