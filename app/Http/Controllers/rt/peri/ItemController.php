<?php

namespace App\Http\Controllers\rt\peri;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\ItemSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('categori_name')->get();
        return view('peri::admin.item.index', compact('categories'));
    }

    public function create()
    {
        return view('peri::admin.item.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'stok'        => 'required|integer|min:0',
                'harga'       => 'required|string',
                'deskripsi'   => 'required|string',
                'category_id' => 'required|exists:category,id',
                'sizes'       => 'nullable|array',
                'sizes.*'     => 'string|max:50',
                'photo_Item'  => 'required|array',
                'photo_Item.*'=> 'image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $harga = (float) str_replace(['.', ','], '', $validated['harga']);

            $item = Item::create([
                'nama_barang' => $validated['nama_barang'],
                'stok'        => $validated['stok'],
                'harga'       => $harga,
                'deskripsi'   => $validated['deskripsi'],
                'category_id' => $validated['category_id'],
            ]);

            // sizes
            if (!empty($validated['sizes'])) {
                foreach ($validated['sizes'] as $size) {
                    if ($size) {
                        ItemSize::create([
                            'item_id' => $item->id,
                            'size'    => $size,
                        ]);
                    }
                }
            }

            // images
            foreach ($request->file('photo_Item') as $index => $file) {
                $path = $file->store('images', 'public');
                $img = ItemImage::create([
                    'item_id' => $item->id,
                    'image'   => $path,
                ]);

                if ($index == 0) {
                    $item->update(['photo_id' => $img->id]);
                }
            }

            DB::commit();
            return redirect()->route('admin.items.index')->with('success', 'Item berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan item: ' . $e->getMessage());
        }
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $item->load('sizes', 'images');
        return view('peri::admin.item.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'stok'        => 'required|integer|min:0',
                'harga'       => 'required|string',
                'deskripsi'   => 'required|string',
                'category_id' => 'required|exists:category,id',
                'sizes'       => 'nullable|array',
                'sizes.*'     => 'string|max:50',
                'photo_Item'  => 'nullable|array',
                'photo_Item.*'=> 'image|mimes:jpeg,png,jpg|max:5120',
                'thumbnail_index' => 'required|integer|min:0',
                'existing_images' => 'nullable|array',
                'existing_images.*' => 'exists:item_images,id,item_id,' . $item->id
            ]);

            $harga = (float) str_replace(['.', ','], '', $validated['harga']);

            $item->update([
                'nama_barang' => $validated['nama_barang'],
                'stok'        => $validated['stok'],
                'harga'       => $harga,
                'deskripsi'   => $validated['deskripsi'],
                'category_id' => $validated['category_id'],
            ]);

            // update sizes
            ItemSize::where('item_id', $item->id)->delete();
            if (!empty($validated['sizes'])) {
                foreach ($validated['sizes'] as $size) {
                    if ($size) {
                        ItemSize::create([
                            'item_id' => $item->id,
                            'size'    => $size,
                        ]);
                    }
                }
            }

            // handle images
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
                        'image'   => $path,
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
            return redirect()->route('admin.items.index')->with('success', 'Item berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui item: ' . $e->getMessage());
        }
    }

    public function destroy(Item $item)
    {
        try {
            $item->delete();
            return response()->json(['success' => true, 'message' => 'Item berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus item: ' . $e->getMessage()], 500);
        }
    }

    public function data(Request $request)
    {
        $items = Item::with(['category', 'sizes']);

        $items->when($request->get('kategori'), function ($query, $categoryId) {
            return $query->where('items.category_id', $categoryId);
        });

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('produk', function ($item) {
                $photoUrl = $item->photo_url ? asset('storage/' . $item->photo_url) : asset('assets/img/default.png');
                return '<div class="d-flex align-items-center">
                            <img src="'.$photoUrl.'" class="rounded-2 me-2" style="width:50px;height:50px;object-fit:cover;">
                            <span>'.$item->nama_barang.'</span>
                        </div>';
            })
            ->addColumn('kategori', function ($item) {
                return $item->category->categori_name ?? 'N/A';
            })
            ->addColumn('stok', function ($item) {
                return number_format($item->stok);
            })
            ->addColumn('harga', function ($item) {
                return 'Rp ' . number_format($item->harga, 0, ',', '.');
            })
            ->addColumn('size', function ($item) {
                return $item->sizes->pluck('size')->join(', ');
            })
            ->addColumn('action', function ($item) {
                $editUrl = route('admin.items.edit', $item->id);
                return '
                    <a href="'.$editUrl.'" class="btn btn-sm btn-primary me-1">Edit</a>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="'.$item->id.'" data-nama="'.$item->nama_barang.'">Hapus</button>
                ';
            })
            ->rawColumns(['produk', 'action'])
            ->make(true);
    }

    public function show(Item $item)
    {
        $item->load(['images', 'category', 'sizes']);
        $item->full_photo_url = $item->photo_url ? asset('storage/' . $item->photo_url) : asset('assets/img/default.png');

        $galleryUrls = [];
        foreach ($item->images as $image) {
            $galleryUrls[] = asset('storage/' . $image->image);
        }
        $item->gallery_urls = $galleryUrls;

        if (request()->ajax()) {
            return response()->json($item);
        }

        return view('peri::admin.items.show', compact('item'));
    }
}
