<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('size')->nullable()->after('nama_barang');
            $table->double('harga')->after('size');
            $table->integer('stok')->default(0)->after('harga');

            $table->dropColumn(['stok_minimum', 'satuan_id']);
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->double('stok_minimum')->nullable();
            $table->unsignedBigInteger('satuan_id')->nullable();

            $table->dropColumn(['size', 'harga', 'stok']);
        });
    }

};
