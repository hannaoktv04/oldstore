<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'nama' => 'Admin User',
                'nip' => 'admin001',
                'role' => 'admin',
                'jabatan' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Pegawai User',
                'nip' => 'pegawai001',
                'role' => 'pegawai',
                'jabatan' => 'Staff',
                'email' => 'pegawai@example.com',
                'password' => Hash::make('pegawai123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
