<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('category')->truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [
            ['id' => 1, 'categori_name' => 'ATK'],
            ['id' => 2, 'categori_name' => 'Tinta/Toner Printer'],
            ['id' => 3, 'categori_name' => 'Batu Baterai'],
            ['id' => 4, 'categori_name' => 'USB/Flash Disk'],
            ['id' => 5, 'categori_name' => 'Penjepit Kertas'],
            ['id' => 6, 'categori_name' => 'Penghapus/Korektor'],
            ['id' => 7, 'categori_name' => 'Buku Tulis'],
            ['id' => 9, 'categori_name' => 'Penggaris'],
            ['id' => 10, 'categori_name' => 'Alat Perekat'],
            ['id' => 11, 'categori_name' => 'Barang Cetakan'],
        ];

        foreach ($categories as $category) {
            DB::table('category')->insert([
                'id' => $category['id'],
                'categori_name' => $category['categori_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
