<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateEnumTipeAdjustmentInStockAdjustments extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                UPDATE stock_adjustments
                SET tipe_adjustment = 'opname'
                WHERE tipe_adjustment NOT IN ('opname','koreksi','stok awal')
                   OR tipe_adjustment IS NULL
            ");

            DB::statement("
                DO $$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'tipe_adjustment_enum') THEN
                        CREATE TYPE tipe_adjustment_enum AS ENUM ('opname','koreksi','stok awal');
                    END IF;
                END$$;
            ");

            DB::statement("
                ALTER TABLE stock_adjustments
                ALTER COLUMN tipe_adjustment TYPE tipe_adjustment_enum
                USING tipe_adjustment::tipe_adjustment_enum
            ");
        } else {
            DB::statement("
                ALTER TABLE stock_adjustments
                MODIFY tipe_adjustment ENUM('opname', 'koreksi', 'stok awal')
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
            ");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE stock_adjustments
                ALTER COLUMN tipe_adjustment TYPE TEXT
                USING tipe_adjustment::text
            ");
            DB::statement("DROP TYPE IF EXISTS tipe_adjustment_enum");
        } else {
            DB::statement("
                ALTER TABLE stock_adjustments
                MODIFY tipe_adjustment ENUM('opname', 'koreksi', 'lainnya')
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
            ");
        }
    }
}
