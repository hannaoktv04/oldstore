<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::withCount('items')->get();
        return view('category.index', compact('categories'));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'categori_name' => 'required|unique:category'
            ]);

            Category::create([
                'categori_name' => $request->categori_name
            ]);

            return redirect()->back()->with('success', 'Kategori item berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Kategory item sudah ada.');
        }
    }


    public function create()
    {
        return view('category.create');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'categori_name' => 'required|unique:category,categori_name,' . $id
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'categori_name' => $request->categori_name
        ]);

        return redirect()->route('category.index')->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('category.index')->with('success', 'Category deleted successfully!');
    }
}