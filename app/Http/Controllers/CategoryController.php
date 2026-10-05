<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('category_name')->get();

        return view('categories.categories-index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('categories.add_category');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:categories,category_name',
        ]);

        $category = new Category;
        $category->category_name = $request->input('category_name');
        $category->save();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category added successfully.');
    }

    public function show($id)
    {
        return redirect()->route('categories.index');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('categories.edit_category', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'category_name')->ignore($id),
            ],
        ]);

        $category = Category::findOrFail($id);
        $category->category_name = $request->input('category_name');
        $category->save();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
