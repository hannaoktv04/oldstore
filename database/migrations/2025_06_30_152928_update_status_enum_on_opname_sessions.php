<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class UpdateStatusEnumOnOpnameSessions extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE opname_sessions MODIFY COLUMN status ENUM('menunggu', 'aktif', 'selesai') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE opname_sessions MODIFY COLUMN status ENUM('aktif', 'selesai') NOT NULL");
    }
}
