<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        return view('category.index', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categori_name' => 'required|unique:category'
        ]);

        $category = Category::create([
            'categori_name' => $request->categori_name
        ]);

        // AJAX response
        if ($request->ajax()) {
            return response()->json($category, 200);
        }

        return redirect()->back()->with('success', 'Category added successfully!');
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
