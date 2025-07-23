<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\ItemStock;
use App\Models\ItemLog;
use App\Models\StockNotification;
use App\Models\StockAdjustment;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use function Psy\debug;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['images', 'category', 'stocks'])
            ->withCount('images as variant_count')
            ->orderBy('nama_barang')
            ->get();

        return view('admin.item.index', compact('items'));
    }
    public function create()
    {
        $satuans = Satuan::all();
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
                'satuan'      => 'required|exists:satuan,id',
                'deskripsi'      => 'required|string',
                'category_id'    => 'required|exists:category,id',
                'photo_Item'     => 'required|array',
                'photo_Item.*'   => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $item = Item::create([
                'nama_barang'   => $validated['nama_barang'],
                'kode_barang'   => $validated['kode_barang'],
                'stok_minimum'  => $validated['stok_minimum'],
                'satuan_id'        => $validated['satuan'],
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
                'nama_barang' => 'required|string|max:255',
                'kode_barang' => 'required|string|max:255|unique:items,kode_barang,' . $item->id,
                'satuan' => 'required|exists:satuan,id',
                'deskripsi' => 'required|string',
                'stok_minimum' => 'required|integer|min:0',
                'category_id' => 'required|exists:category,id',
                'photo_Item' => 'nullable|array|max:5',
                'photo_Item.*' => 'image|mimes:jpeg,png,jpg|max:2048',
                'thumbnail_index' => 'required|integer|min:0',
                'existing_images' => 'nullable|array',
                'existing_images.*' => 'exists:item_images,id,item_id,'.$item->id
            ]);
            $item->update([
                'nama_barang' => $validated['nama_barang'],
                'kode_barang' => $validated['kode_barang'],
                'satuan_id' => $validated['satuan'],
                'deskripsi' => $validated['deskripsi'],
                'stok_minimum' => $validated['stok_minimum'],
                'category_id' => $validated['category_id'],
            ]);
            $existingImages = $request->input('existing_images', []);
            $currentImages = $item->images()->pluck('id')->toArray();

            $imagesToDelete = array_diff($currentImages, $existingImages);
            foreach ($imagesToDelete as $imageId) {
                $image = ItemImage::find($imageId);
                if ($image) {
                    if ($image->id !== $item->photo_id) {
                        Storage::disk('public')->delete($image->image);
                        $image->delete();
                    }
                }
            }

            if ($request->hasFile('photo_Item')) {
                foreach ($request->file('photo_Item') as $file) {
                    $path = $file->store('images', 'public');
                    ItemImage::create([
                        'item_id' => $item->id,
                        'image' => $path,
                    ]);
                }
            }

            $thumbnailIndex = (int)$request->thumbnail_index;
            $allImages = $item->images()->orderBy('created_at')->get();

            if ($allImages->count() > 0) {
                $selectedIndex = min($thumbnailIndex, $allImages->count() - 1);
                $item->update(['photo_id' => $allImages[$selectedIndex]->id]);
            } else {
                $item->update(['photo_id' => null]);
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
        DB::beginTransaction();
        try {
            if ($image->item && $image->item->photo_id == $image->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gambar utama tidak boleh dihapus langsung. Silakan pilih thumbnail lain terlebih dahulu.'
                ], 400);
            }
            Storage::disk('public')->delete($image->image);
            $image->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar: ' . $e->getMessage()
            ], 500);
        }
    }
    public function edit(Item $item)
    {
        $categories = Category::all();
        $satuans = Satuan::all();
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
