<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\ItemStock;
use App\Models\ItemLog;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



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
                'stok_awal'   => 'required|numeric|min:0',
                'satuan'      => 'required|string',
                'deskripsi'   => 'required|string',
                'category_id' => 'required|exists:category,id',
                'photo_Item'   => 'required|array',
                'photo_Item.*' => 'image|mimes:jpeg,png,jpg|max:2048',

            ]);

            $item = Item::create([
                'nama_barang'   => $validated['nama_barang'],
                'kode_barang'   => $validated['kode_barang'],
                'stok_minimum'  => $validated['stok_awal'] ?? 0,
                'satuan'        => $validated['satuan'],
                'deskripsi'     => $validated['deskripsi'],
                'category_id'   => $validated['category_id'],
            ]);
            ItemStock::create([
                'item_id' => $item->id,
                'qty'     => $validated['stok_awal'],
            ]);
            $adjustment = StockAdjustment::create([
                'item_id'         => $item->id,
                'qty_sebelum'     => 0,
                'qty_fisik'       => $validated['stok_awal'],
                'qty_selisih'     => $validated['stok_awal'],
                'tipe_adjustment' => 'stok awal',
                'keterangan'      => 'Stok awal saat pembuatan item',
                'adjusted_by'     => auth()->id(),
                'adjusted_at'     => now(),
            ]);
            ItemLog::create([
                'item_id'   => $item->id,
                'tipe'      => 'in',
                'qty'       => $validated['stok_awal'],
                'sumber'    => 'adjustment',
                'sumber_id' => $adjustment->id,
                'deskripsi' => 'Stok awal saat pembuatan item',
            ]);

            $firstImageId = null;
            foreach ($request->file('photo_Item') as $index => $file) {
                $path = $file->store('images', 'public');
                $img = ItemImage::create([
                    'item_id' => $item->id,
                    'image'   => $path,
                ]);
                if ($index === 0) {
                    $item->update(['photo_id' => $img->id]);
                }
            }
            if ($firstImageId) {
                $item->update(['photo_id' => $firstImageId]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Item berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan item: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Item $item)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'kode_barang' => 'required|string|max:255|unique:items,kode_barang,' . $item->id,
                'satuan'      => 'required|string',
                'deskripsi'   => 'required|string',
                'category_id' => 'nullable|exists:category,id',
                'photo_Item'   => 'nullable|array|max:5',
                'photo_Item.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $item->update([
                'nama_barang'   => $validated['nama_barang'],
                'kode_barang'   => $validated['kode_barang'],
                'satuan'        => $validated['satuan'],
                'deskripsi'     => $validated['deskripsi'],
                'category_id'   => $validated['category_id'],
            ]);

            $stock = ItemStock::where('item_id', $item->id)->first();
            if ($stock) {
                $stock->qty = $validated['stok'];
                $stock->save();
            } else {
                ItemStock::create([
                    'item_id' => $item->id,
                    'qty' => $validated['stok'],
                ]);
            }

            if ($request->hasFile('photo_Item')) {
                $firstImageId = null;

                foreach ($request->file('photo_Item') as $index => $file) {
                    $path = $file->store('images', 'public');
                    $image = ItemImage::create([
                        'item_id' => $item->id,
                        'image'   => $path,
                    ]);

                    if ($index === 0) {
                        $firstImageId = $image->id;
                    }
                }
                if ($firstImageId) {
                    $item->update(['photo_id' => $firstImageId]);
                }
            }

            DB::commit();
            return redirect()->route('admin.items')->with('success', 'Item berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui item.');
        }
    }

    public function index()
    {
        $categories = Category::orderBy('categori_name')->get();
        $items = Item::with(['category', 'state'])
            ->whereHas('state', function ($query) {
                $query->where('is_archived', false); 
            })
            ->orderByRaw('stok_minimum <= 0')
            ->orderByDesc('stok_minimum')
            ->get();

        return view('layouts.kategori', compact('items', 'categories'));
    }


    public function itemList()
    {
        $items = Item::with(['images', 'category'])
                    ->withCount('images as variant_count') // <-- sesuaikan dengan relasi 'images'
                    ->withSum('stocks as total_stok', 'qty')
                    ->orderBy('nama_barang')
                    ->get();

        return view('admin.items', compact('items'));
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('admin.editItem', compact('item', 'categories'));
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('admin.items')->with('success', 'Item berhasil dihapus.');
    }


public function toggleState(Item $item)
{
    if (!$item->state) {
        $item->state()->create(['is_archived' => false]);
    }
    $current = $item->state->is_archived;
    $item->state->update([
        'is_archived' => !$current
    ]);
    return back()->with('status', 'Status item berhasil diperbarui.');
}

    public function bulkAction(Request $request)
    {
        $ids = $request->input('selected_items', []);
        $action = $request->input('action');
        if (empty($ids)) {
            return back()->with('warning', 'Tidak ada item yang dipilih.');
        }
        foreach ($ids as $id) {
            $item = Item::find($id);
            if (!$item->state) {
                $item->state()->create(['is_archived' => false]);
            }

            if ($action === 'arsipkan') {
                $item->state->update(['is_archived' => true]);
            } elseif ($action === 'tampilkan') {
                $item->state->update(['is_archived' => false]);
            }
        }

        return back()->with('status', 'Aksi berhasil dijalankan.');
    }


}