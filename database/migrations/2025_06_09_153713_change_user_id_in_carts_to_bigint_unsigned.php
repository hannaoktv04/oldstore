<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE carts SET user_id = NULL WHERE user_id::text !~ '^[0-9]+$' OR user_id::text = ''");

            DB::statement("
                ALTER TABLE carts
                ALTER COLUMN user_id TYPE BIGINT
                USING NULLIF(user_id::text,'')::bigint
            ");
        } else {
            Schema::table('carts', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE carts
                ALTER COLUMN user_id TYPE VARCHAR(255)
                USING user_id::text
            ");
        } else {
            Schema::table('carts', function (Blueprint $table) {
                $table->string('user_id')->change();
            });
        }
    }
};
