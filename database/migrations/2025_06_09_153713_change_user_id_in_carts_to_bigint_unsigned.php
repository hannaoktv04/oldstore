<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });
    }

    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            // Ubah kembali ke tipe sebelumnya kalau perlu, misal varchar
            $table->string('user_id')->change();
        });
    }
};
