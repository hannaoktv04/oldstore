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
use Yajra\DataTables\Facades\DataTables;


use function Psy\debug;

class ItemController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('categori_name')->get();
        return view('admin.item.index', compact('categories'));
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

    public function edit(Item $item)
    {
        $categories = Category::all();
        $satuans = Satuan::all();
        return view('admin.item.edit', compact('item', 'categories'));
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
                'existing_images.*' => 'exists:item_images,id,item_id,' . $item->id
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
        // $items = Item::with(['category', 'stocks', 'state'])->select('items.*');
         $items = Item::with(['category', 'stocks', 'state'])
                     ->leftJoin('item_stocks', 'items.id', '=', 'item_stocks.item_id')
                     ->select('items.*');

        // Terapkan filter berdasarkan input dari request
        $items->when($request->get('kategori'), function ($query, $categoryId) {
            return $query->where('items.category_id', $categoryId);
        });

        $items->when($request->get('status'), function ($query, $status) {
            if ($status === 'aktif') {
                return $query->where(function ($q) {
                    $q->whereDoesntHave('state')->orWhereHas('state', function ($sub) {
                        $sub->where('is_archived', false);
                    });
                });
            } elseif ($status === 'arsip') {
                return $query->whereHas('state', function ($sub) {
                    $sub->where('is_archived', true);
                });
            }
        });

        $items->when($request->get('stok'), function ($query, $stok) {
            if ($stok === 'habis') {
                return $query->where('item_stocks.qty', '=', 0);
            } elseif ($stok === 'menipis') {
                return $query->where('item_stocks.qty', '>', 0)
                             ->whereRaw('item_stocks.qty <= items.stok_minimum');
            } elseif ($stok === 'aman') {
                return $query->whereRaw('item_stocks.qty > items.stok_minimum');
            }
        });





        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('produk', function ($item) {
                $photoUrl = $item->photo_url ? asset('storage/' . $item->photo_url) : asset('assets/img/default.png');
                $initials = strtoupper(substr($item->category->categori_name ?? 'NA', 0, 2));
                $image = $item->photo_url
                    ? '<img src="' . $photoUrl . '" alt="' . $item->nama_barang . '" class="rounded-2">'
                    : '<span class="avatar-initial rounded-2 bg-label-secondary">' . $initials . '</span>';

                return '<div class="d-flex justify-content-start align-items-center product-name">' .
                    '<div class="avatar-wrapper me-3"><div class="avatar rounded-3 bg-label-secondary">' . $image . '</div></div>' .
                    '<div class="d-flex flex-column">' .
                    '<a href="' . route('produk.show', $item->id) . '" class="text-nowrap text-heading fw-medium view-details" data-id="' . $item->id . '">' . $item->nama_barang . '</a>' .
                    '<small class="text-truncate d-none d-sm-block">' . ($item->category->categori_name ?? 'N/A') . '</small>' .
                    '</div></div>';
            })
            ->addColumn('kategori', function ($item) {
                return $item->category->categori_name ?? 'N/A';
            })
            ->addColumn('stok', function ($item) {
                $badgeClass = ($item->total_stok <= $item->stok_minimum) ? 'bg-label-danger' : 'bg-label-success';
                return '<span class="badge ' . $badgeClass . '">' . number_format($item->total_stok) . '</span>';
            })
            ->addColumn('status', function ($item) {
                $isArchived = $item->state->is_archived ?? false;
                $badgeClass = $isArchived ? 'bg-label-danger' : 'bg-label-success';
                $text = $isArchived ? 'Diarsip' : 'Aktif';
                return '<span class="badge rounded-pill ' . $badgeClass . '">' . $text . '</span>';
            })
            ->addColumn('action', function ($item) {
                $editUrl = route('admin.items.edit', $item->id);
                $isArchived = $item->state->is_archived ?? false;
                $archiveText = $isArchived ? 'Aktifkan' : 'Arsipkan';

                return '<div class="d-inline-block text-nowrap">' .
                    '<a href="' . $editUrl . '" class="btn btn-sm btn-icon btn-text-secondary waves-effect waves-light rounded-pill me-50"><i class="ri-edit-box-line ri-20px"></i></a>' .
                    '<button class="btn btn-sm btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ri-more-2-line ri-20px"></i></button>' .
                    '<div class="dropdown-menu dropdown-menu-end m-0">' .
                    '<a href="javascript:void(0);" class="dropdown-item btn-detail" data-id="' . $item->id . '">Detail</a>' .
                    '<a href="javascript:void(0);" class="dropdown-item btn-archive" data-id="' . $item->id . '">' . $archiveText . '</a>' .
                    '<a href="javascript:void(0);" class="dropdown-item btn-delete" data-id="' . $item->id . '" data-nama="' . $item->nama_barang . '">Hapus</a>' .
                    '</div></div>';
            })
            ->addColumn('checkbox', function ($item) {
                return '<input type="checkbox" value="' . $item->id . '" class="form-check-input item-checkbox">';
            })
            ->rawColumns(['checkbox', 'produk', 'stok', 'status', 'action'])
            ->make(true);
    }
    // public function show(Item $item)
    // {
    //     $item->load(['images', 'category', 'stocks', 'satuan', 'state']);
    //     $item->full_photo_url = $item->photo_url ? asset('storage/' . $item->photo_url) : asset('assets/img/default.png');

    //     if (request()->ajax()) {
    //         return response()->json($item);
    //     }
    //     return view('admin.item.show');
    // }


public function show(Item $item)
{
    $item->load(['images', 'category', 'stocks', 'satuan', 'state']);
    $item->full_photo_url = $item->photo_url ? asset('storage/' . $item->photo_url) : asset('assets/img/default.png');
    $galleryUrls = [];
    foreach ($item->images as $image) {
        $galleryUrls[] = asset('storage/' . $image->image);
    }
    $item->gallery_urls = $galleryUrls;

    if (request()->ajax()) {
        return response()->json($item);
    }

    return view('admin.items.show', compact('item'));
}


    public function toggleArchive(Item $item)
    {
        if (!$item->state) {
            $item->state()->create(['is_archived' => true]);
        } else {
            $item->state->update(['is_archived' => !$item->state->is_archived]);
        }
        return response()->json(['success' => true, 'message' => 'Status item berhasil diperbarui.']);
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada item yang dipilih.'], 422);
        }
        $items = Item::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            if ($action === 'hapus') {
                $item->delete();
            } elseif ($action === 'arsipkan') {
                if (!$item->state) $item->state()->create(['is_archived' => true]);
                else $item->state->update(['is_archived' => true]);
            } elseif ($action === 'aktifkan') {
                if (!$item->state) $item->state()->create(['is_archived' => false]);
                else $item->state->update(['is_archived' => false]);
            }
        }
        return response()->json(['success' => true, 'message' => count($items) . ' item berhasil di'.$action]);
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
}