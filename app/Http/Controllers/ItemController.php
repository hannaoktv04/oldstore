<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Photo;
use App\Models\ItemStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{

    public function create()
    {
        return view('admin.addItem');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'kode_barang' => 'required|string|max:255|unique:items,kode_barang',
                'stok' => 'required|numeric',
                'satuan' => 'required|string',
                'deskripsi' => 'required|string',
                'category_id' => 'nullable|exists:category,id',
                'photo_Item'   => 'required|array|min:1|max:5',
                'photo_Item.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $item = Item::create([
                'nama_barang' => $validated['nama_barang'],
                'kode_barang' => $validated['kode_barang'],
                'stok_minimum' => $validated['stok'],
                'satuan' => $validated['satuan'],
                'deskripsi' => $validated['deskripsi'],
                'category_id' => $validated['category_id'],
            ]);

            ItemStock::create([
            'item_id' => $item->id,
            'qty' => $validated['stok'],
            ]);

            $slots = [null, null, null, null, null];

            if ($request->hasFile('photo_Item')) {
                foreach ($request->file('photo_Item') as $idx => $file) {
                    $slots[$idx] = $file->store('images', 'public');
                }

                $photo = Photo::create([
                    'item_id' => $item->id,
                    'image'   => $slots[0],
                    'img_xl'  => $slots[1],
                    'img_l'   => $slots[2],
                    'img_m'   => $slots[3],
                    'img_s'   => $slots[4],
                ]);
                $item->update(['photo_id' => $photo->id]);
            }


            DB::commit();
            return redirect()->back()->with('success', 'Item berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan item.');
        }
    }
    public function index()
    {
        $categories = Category::orderBy('categori_name')->get();
        $items = Item::with('category')->get();
        return view('layouts.kategori', compact('items','categories'));
    }
}