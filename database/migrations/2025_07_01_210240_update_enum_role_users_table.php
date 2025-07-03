<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateEnumRoleUsersTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('pegawai', 'admin', 'staff_pengiriman') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('pegawai', 'admin') NOT NULL");
    }
}
