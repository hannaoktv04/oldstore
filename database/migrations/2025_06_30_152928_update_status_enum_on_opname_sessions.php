<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStatusEnumOnOpnameSessions extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                DO $$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'status_enum') THEN
                        CREATE TYPE status_enum AS ENUM ('menunggu', 'aktif', 'selesai');
                    END IF;
                END$$;
            ");

            DB::statement("ALTER TABLE opname_sessions ALTER COLUMN status DROP DEFAULT");

            DB::statement("
                ALTER TABLE opname_sessions
                ALTER COLUMN status TYPE status_enum
                USING status::status_enum
            ");

            DB::statement("ALTER TABLE opname_sessions ALTER COLUMN status SET DEFAULT 'menunggu'");
        } else {
            DB::statement("
                ALTER TABLE opname_sessions
                MODIFY COLUMN status ENUM('menunggu', 'aktif', 'selesai') NOT NULL
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE opname_sessions ALTER COLUMN status DROP DEFAULT");
            DB::statement("
                ALTER TABLE opname_sessions
                ALTER COLUMN status TYPE TEXT
                USING status::text
            ");
            DB::statement("DROP TYPE IF EXISTS status_enum");
        } else {
            DB::statement("
                ALTER TABLE opname_sessions
                MODIFY COLUMN status ENUM('aktif','selesai') NOT NULL
            ");
        }
    }
}
