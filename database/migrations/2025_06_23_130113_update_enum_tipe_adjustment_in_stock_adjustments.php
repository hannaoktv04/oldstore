<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateEnumTipeAdjustmentInStockAdjustments extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE stock_adjustments MODIFY tipe_adjustment ENUM('opname', 'koreksi', 'stok awal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE stock_adjustments MODIFY tipe_adjustment ENUM('opname', 'koreksi', 'lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }
}

