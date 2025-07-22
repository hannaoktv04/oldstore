<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'active')) {
                $table->smallInteger('active')->default(1)->after('username');
            }

            if (!Schema::hasColumn('users', 'organisasi')) {
                $table->json('organisasi')->nullable()->after('active');
            }

            if (!Schema::hasColumn('users', 'pangkat_golongan')) {
                $table->string('pangkat_golongan')->nullable()->after('organisasi');
            }

            if (!Schema::hasColumn('users', 'jabatan_struktural_organisasi')) {
                $table->json('jabatan_struktural_organisasi')->nullable()->after('pangkat_golongan');
            }

            if (!Schema::hasColumn('users', 'plt_jabatan_struktural_organisasi')) {
                $table->json('plt_jabatan_struktural_organisasi')->nullable()->after('jabatan_struktural_organisasi');
            }

            if (!Schema::hasColumn('users', 'plh_jabatan_struktural_organisasi')) {
                $table->json('plh_jabatan_struktural_organisasi')->nullable()->after('plt_jabatan_struktural_organisasi');
            }

            if (!Schema::hasColumn('users', 'nama_jabatan_fungsional_umum')) {
                $table->string('nama_jabatan_fungsional_umum')->nullable()->after('plh_jabatan_struktural_organisasi');
            }

            if (!Schema::hasColumn('users', 'nama_jabatan_fungsional_tertentu')) {
                $table->string('nama_jabatan_fungsional_tertentu')->nullable()->after('nama_jabatan_fungsional_umum');
            }

            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('nama_jabatan_fungsional_tertentu');
            }

            if (!Schema::hasColumn('users', 'jabatan_kelompok_substansi')) {
                $table->json('jabatan_kelompok_substansi')->nullable()->after('profile_picture');
            }

            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }

            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'username',
                'active',
                'organisasi',
                'pangkat_golongan',
                'jabatan_struktural_organisasi',
                'plt_jabatan_struktural_organisasi',
                'plh_jabatan_struktural_organisasi',
                'nama_jabatan_fungsional_umum',
                'nama_jabatan_fungsional_tertentu',
                'profile_picture',
                'jabatan_kelompok_substansi',
                'remember_token',
                'email_verified_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
