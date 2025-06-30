<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('opname_sessions', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('opname_sessions', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable(false)->change();
        });
    }
};
