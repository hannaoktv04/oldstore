<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Carbon;

class ItemRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada user dan item tanpa factory
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'nama' => 'User Default',
                'nip' => '1234567890',
                'role' => 'pegawai',
                'jabatan' => 'Staff',
                'email' => 'user@example.com',
                'password' => 'password',  
            ]);
        }

        $item = Item::first();
        if (!$item) {
            $item = Item::create([
                'kode_barang' => 'P002',
                'nama_barang' => 'Pulpen',
                'satuan' => 'pcs',
                'stok_minimum' => 10,
            ]);
        }

        $statuses = ['submitted', 'revised', 'cancelled'];
        foreach ($statuses as $status) {
            $request = ItemRequest::create([
                'user_id' => $user->id,
                'status' => $status,
                'keterangan' => "Keterangan untuk status: $status",
                'tanggal_permintaan' => Carbon::now()->subDays(rand(1, 5)),
            ]);

            ItemRequestDetail::create([
                'item_request_id' => $request->id,
                'item_id' => $item->id,
                'qty_requested' => rand(5, 15),
                'qty_approved' => null,
            ]);
        }
    }
}