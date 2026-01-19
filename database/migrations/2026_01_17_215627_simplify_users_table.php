<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'nip',
                'jabatan',
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
                'email_verified_at'
            ]);

            $table->string('no_telp', 20)->after('email');
            $table->text('alamat')->after('no_telp');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->smallInteger('active')->default(1);
            $table->json('organisasi')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->json('jabatan_struktural_organisasi')->nullable();
            $table->json('plt_jabatan_struktural_organisasi')->nullable();
            $table->json('plh_jabatan_struktural_organisasi')->nullable();
            $table->string('nama_jabatan_fungsional_umum')->nullable();
            $table->string('nama_jabatan_fungsional_tertentu')->nullable();
            $table->string('profile_picture')->nullable();
            $table->json('jabatan_kelompok_substansi')->nullable();
            $table->timestamp('email_verified_at')->nullable();

            $table->dropColumn(['no_telp', 'alamat']);
        });
    }
};
