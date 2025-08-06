<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('satuan')->truncate();
        Schema::enableForeignKeyConstraints();
        $satuans = [
            ['id' => 1, 'nama_satuan' => 'Pcs'],
            ['id' => 2, 'nama_satuan' => 'Buah'],
            ['id' => 3, 'nama_satuan' => 'Pack'],
            ['id' => 4, 'nama_satuan' => 'Rim'],
            ['id' => 5, 'nama_satuan' => 'Botol'],
            ['id' => 8, 'nama_satuan' => 'Dus'],
            ['id' => 9, 'nama_satuan' => 'Lusin'],
        ];

        foreach ($satuans as $satuan) {
            DB::table('satuan')->insert([
                'id' => $satuan['id'],
                'nama_satuan' => $satuan['nama_satuan'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
