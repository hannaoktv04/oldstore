<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'kode_barang' => 'P001',
                'nama_barang' => 'Buku Tulis',
                'satuan' => 'pcs',
                'stok_minimum' => 10,
                'deskripsi' => 'Buku tulis 40 lembar untuk pelajar.',
                'image' => 'sup-game-box-400.png',
                'kategori' => 'Alat Tulis',
            ],
            [
                'kode_barang' => 'P002',
                'nama_barang' => 'Pulpen',
                'satuan' => 'pcs',
                'stok_minimum' => 20,
                'deskripsi' => 'Pulpen tinta biru standar.',
                'image' => 'samsung-watch-4.png',
                'kategori' => 'Alat Tulis',
            ],
            [
                'kode_barang' => 'P003',
                'nama_barang' => 'Kertas A4',
                'satuan' => 'box',
                'stok_minimum' => 5,
                'deskripsi' => 'Kertas A4 80 gsm isi 500 lembar.',
                'image' => 'samsung-s22.png',
                'kategori' => 'Perlengkapan Kantor',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
