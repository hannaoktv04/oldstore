<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStaffPengirimanToUnsignedbigintInItemDeliveries extends Migration
{
    public function up()
    {
        Schema::table('item_deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_pengiriman')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('item_deliveries', function (Blueprint $table) {
            $table->string('staff_pengiriman', 255)->nullable()->change();
        });
    }
}
