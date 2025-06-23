<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::withCount('items')->get();
        return view('admin.categoryItem', compact('categories'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'categori_name' => 'required|string|max:100|unique:category,categori_name',
        ]);

        Category::create($validated);
        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'categori_name' => 'required|string|max:100|unique:category,categori_name,' . $category->id,
        ]);

        $category->update($validated);
        return redirect()->route('admin.category.index')
            ->with('success', 'Kategori berhasil diubah!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.category.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }

    public function bulkAction(Request $request)
    {
        if ($request->action === 'hapus') {
            Category::whereIn('id', $request->selected_categories ?? [])->delete();
            return back()->with('success', 'Kategori terpilih berhasil dihapus!');
        }
        return back();
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected_categories', []);

        if (empty($ids)) {
            return back()->with('warning', 'Tidak ada kategori yang dipilih untuk dihapus.');
        }

        Category::whereIn('id', $ids)->delete();

        return back()->with('success', 'Kategori terpilih berhasil dihapus!');
    }

    public function show($id)
    {
        $categories = Category::withCount('items')->get();
        $selectedCategory = Category::with('items.photo')->findOrFail($id);
        $items = $selectedCategory->items;

        return view('layouts.kategori', compact('categories', 'items', 'selectedCategory'));
    }

}