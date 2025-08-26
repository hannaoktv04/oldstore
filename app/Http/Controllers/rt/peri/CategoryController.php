<?php

namespace App\Http\Controllers\rt\peri;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::withCount('items')->get();

        $sumExpr = 'COALESCE((SELECT SUM(item_stocks.qty) FROM item_stocks WHERE items.id = item_stocks.item_id), 0)';

        $items = Item::with(['category','photo'])
            ->whereHas('state', fn($q) => $q->where('is_archived', false))
            ->orderByRaw("($sumExpr = 0) DESC")
            ->orderByRaw("$sumExpr DESC")
            ->paginate(20);

        return view('peri::layouts.kategori', compact('categories', 'items'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categori_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('category', 'categori_name'),
            ],
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $category = Category::create([
            'categori_name' => $request->categori_name,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Kategori berhasil ditambahkan!',
                'category' => [
                    'id'            => $category->id,
                    'categori_name' => $category->categori_name,
                    'items_count'   => method_exists($category, 'items') ? $category->items()->count() : 0,
                ],
                'routes' => [
                    'update'  => route('admin.categories.update', $category->id),
                    'destroy' => route('admin.categories.destroy', $category->id),
                ],
            ]);
        }

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'categori_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('category', 'categori_name')->ignore($category->id, 'id'),
            ],
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $category->update([
            'categori_name' => $request->categori_name,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diubah!',
                'category' => [
                    'id'            => $category->id,
                    'categori_name' => $category->categori_name,
                ],
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diubah!');
    }

    public function destroy(Request $request, Category $category)
    {
        $id   = $category->id;
        $name = $category->categori_name;
        $category->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Kategori \"{$name}\" dihapus.",
                'deleted' => $id,
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected_categories', []);

        if (empty($ids)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kategori yang dipilih untuk dihapus.',
                ], 400);
            }
            return back()->with('warning', 'Tidak ada kategori yang dipilih untuk dihapus.');
        }

        Category::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori terpilih berhasil dihapus!',
                'deleted' => $ids,
            ]);
        }

        return back()->with('success', 'Kategori terpilih berhasil dihapus!');
    }

    public function bulkAction(Request $request)
    {
        if ($request->action === 'hapus') {
            $ids = $request->selected_categories ?? [];
            Category::whereIn('id', $ids)->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori terpilih berhasil dihapus!',
                    'deleted' => $ids,
                ]);
            }
            return back()->with('success', 'Kategori terpilih berhasil dihapus!');
        }
        return $request->ajax()
            ? response()->json(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400)
            : back();
    }
    public function show($id)
    {
        $categories = Category::withCount('items')->get();
        $selectedCategory = Category::findOrFail($id);

        $items = Item::with(['category', 'photo'])
            ->where('category_id', $id)
            ->withSum('stocks', 'qty')
            ->orderByRaw('(select sum(qty) from item_stocks where item_id = items.id) = 0')
            ->orderByDesc('stocks_sum_qty')
            ->paginate(20);

        return view('peri::layouts.kategori', compact('categories', 'selectedCategory', 'items'));
    }

    public function publicView()
    {
        $categories = Category::withCount('items')->get();

        $items = Item::with(['category', 'photo'])
            ->orderByRaw('stok_minimum = 0')
            ->orderBy('stok_minimum', 'desc')
            ->get();

        return view('peri::admin.category.index', compact('categories', 'items'));
    }
}
