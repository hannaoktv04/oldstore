<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->renameColumn('tanggal_pengambilan', 'tanggal_pengiriman');
        });

        Schema::table('item_requests', function (Blueprint $table) {
            $table->dateTime('tanggal_pengiriman')->change();
        });
    }

    public function down()
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->renameColumn('tanggal_pengiriman', 'tanggal_pengambilan');
        });

        Schema::table('item_requests', function (Blueprint $table) {
            $table->date('tanggal_pengambilan')->change();
        });
    }
};
