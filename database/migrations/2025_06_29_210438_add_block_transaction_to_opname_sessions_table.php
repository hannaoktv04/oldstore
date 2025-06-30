<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('opname_sessions', function (Blueprint $table) {
            $table->boolean('block_transaction')->default(true);
        });
    }

    public function down()
    {
        Schema::table('opname_sessions', function (Blueprint $table) {
            $table->dropColumn('block_transaction');
        });
    }
};
