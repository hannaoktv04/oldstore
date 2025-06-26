<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('nama_barang');
                $table->foreign('category_id')->references('id')->on('category')->onDelete('set null');
            }

            if (!Schema::hasColumn('items', 'photo_product')) {
                $table->unsignedBigInteger('photo_product')->nullable()->after('deskripsi');
                $table->foreign('photo_product')->references('id')->on('item_images')->onDelete('set null');
            }

            if (Schema::hasColumn('items', 'image')) {
                $table->dropColumn('image');
            }
        });
    }

};
