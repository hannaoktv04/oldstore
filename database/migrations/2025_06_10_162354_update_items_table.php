<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('nama_barang');
            $table->unsignedBigInteger('photo_product')->nullable()->after('deskripsi');
            $table->dropColumn('image');

            $table->foreign('category_id')->references('id')->on('category')->onDelete('set null');
            $table->foreign('photo_product')->references('id')->on('photo')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {

            $table->dropForeign(['category_id']);
            $table->dropForeign(['photo_product']);
            $table->dropColumn('category_id');
            $table->dropColumn('photo_product');
            $table->string('image')->nullable();
        });
    }
};
