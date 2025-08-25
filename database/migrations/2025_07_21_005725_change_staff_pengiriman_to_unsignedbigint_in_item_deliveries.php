<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeStaffPengirimanToUnsignedbigintInItemDeliveries extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE item_deliveries SET staff_pengiriman = NULL WHERE staff_pengiriman !~ '^[0-9]+$'");

            DB::statement("
                ALTER TABLE item_deliveries
                ALTER COLUMN staff_pengiriman TYPE BIGINT
                USING NULLIF(staff_pengiriman, '')::bigint
            ");
        } else {
            Schema::table('item_deliveries', function ($table) {
                $table->unsignedBigInteger('staff_pengiriman')->nullable()->change();
            });
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE item_deliveries
                ALTER COLUMN staff_pengiriman TYPE VARCHAR(255)
                USING staff_pengiriman::text
            ");
        } else {
            Schema::table('item_deliveries', function ($table) {
                $table->string('staff_pengiriman', 255)->nullable()->change();
            });
        }
    }
}
