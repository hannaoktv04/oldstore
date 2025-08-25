<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateEnumRoleUsersTable extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                DO $$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'role_enum') THEN
                        CREATE TYPE role_enum AS ENUM ('pegawai','admin','staff_pengiriman');
                    END IF;
                END$$;
            ");

            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");

            DB::statement("
                ALTER TABLE users
                ALTER COLUMN role TYPE role_enum
                USING role::role_enum
            ");

            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'pegawai'");
        } else {
            DB::statement("
                ALTER TABLE users
                MODIFY role ENUM('pegawai','admin','staff_pengiriman') NOT NULL
            ");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
            DB::statement("
                ALTER TABLE users
                ALTER COLUMN role TYPE TEXT
                USING role::text
            ");
            DB::statement("DROP TYPE IF EXISTS role_enum");
        } else {
            DB::statement("
                ALTER TABLE users
                MODIFY role ENUM('pegawai','admin') NOT NULL
            ");
        }
    }
}
