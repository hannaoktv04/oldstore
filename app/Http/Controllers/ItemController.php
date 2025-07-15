<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\ItemStock;
use App\Models\ItemLog;
use App\Models\StockNotification;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



class ItemController extends Controller
{
  public function index()
{
    $items = Item::with(['images', 'category', 'stocks']) // pastikan 'stocks' di-load
        ->withCount('images as variant_count')
        ->orderBy('nama_barang')
        ->get();

    return view('admin.item.index', compact('items'));
}
    public function create()
    {
        return view('admin.item.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'nama_barang'    => 'required|string|max:255',
                'kode_barang'    => 'required|string|max:255|unique:items,kode_barang',
                'stok_awal'      => 'required|numeric|min:0',
                'stok_minimum'   => 'required|numeric|min:0',
                'satuan'         => 'required|string',
                'deskripsi'      => 'required|string',
                'category_id'    => 'required|exists:category,id',
                'photo_Item'     => 'required|array',
                'photo_Item.*'   => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $item = Item::create([
                'nama_barang'   => $validated['nama_barang'],
                'kode_barang'   => $validated['kode_barang'],
                'stok_minimum'  => $validated['stok_minimum'],
                'satuan'        => $validated['satuan'],
                'deskripsi'     => $validated['deskripsi'],
                'category_id'   => $validated['category_id'],
            ]);

            ItemStock::create([
                'item_id' => $item->id,
                'qty'     => $validated['stok_awal'],
            ]);

            if ($validated['stok_awal'] > 0) {
                StockNotification::create([
                    'item_id' => $item->id,
                    'seen' => false,
                ]);
            }

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

            $thumbnailIndex = $request->input('thumbnail_index', 0);
            if (!is_numeric($thumbnailIndex) || $thumbnailIndex < 0) {
                $thumbnailIndex = 0;
            }

            $uploadedImages = [];

            foreach ($request->file('photo_Item') as $index => $file) {
                $path = $file->store('images', 'public');
                $img = ItemImage::create([
                    'item_id' => $item->id,
                    'image'   => $path,
                ]);

                $uploadedImages[] = $img;

                if ($index == $thumbnailIndex) {
                    $item->update(['photo_id' => $img->id]);
                }
            }

            if (!$item->photo_id && count($uploadedImages)) {
                $item->update(['photo_id' => $uploadedImages[0]->id]);
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
                'nama_barang'   => 'required|string|max:255',
                'kode_barang'   => 'required|string|max:255|unique:items,kode_barang,' . $item->id,
                'satuan'        => 'required|string',
                'deskripsi'     => 'required|string',
                'category_id'   => 'nullable|exists:category,id',
                'photo_Item'    => 'nullable|array|max:5',
                'photo_Item.*'  => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $item->update([
                'nama_barang' => $validated['nama_barang'],
                'kode_barang' => $validated['kode_barang'],
                'satuan'      => $validated['satuan'],
                'deskripsi'   => $validated['deskripsi'],
                'category_id' => $validated['category_id'],
            ]);

            if ($request->hasFile('photo_Item')) {
                $thumbnailIndex = $request->input('thumbnail_index', 0);
                if (!is_numeric($thumbnailIndex) || $thumbnailIndex < 0) {
                    $thumbnailIndex = 0;
                }

                $uploadedImages = [];

                foreach ($request->file('photo_Item') as $index => $file) {
                    $path = $file->store('images', 'public');
                    $img = ItemImage::create([
                        'item_id' => $item->id,
                        'image'   => $path,
                    ]);
                    $uploadedImages[] = $img;

                    if ($index == $thumbnailIndex) {
                        $item->update(['photo_id' => $img->id]);
                    }
                }

                if (!$item->photo_id && count($uploadedImages)) {
                    $item->update(['photo_id' => $uploadedImages[0]->id]);
                }
            }

            DB::commit();
            return redirect()->route('admin.items')->with('success', 'Item berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui item: ' . $e->getMessage());
        }
    }

    public function deleteImage(ItemImage $image)
    {
        if ($image->id === optional($image->item)->photo_id) {
            return back()->with('error', 'Gambar utama tidak boleh dihapus langsung.');
        }

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', 'Gambar berhasil dihapus.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('admin.item.edit', compact('item', 'categories'));
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
            $item->load('state');
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
