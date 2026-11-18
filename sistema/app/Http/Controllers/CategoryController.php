<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('settings.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('settings.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:categories,name',
            'description' => 'nullable|max:255',
        ]);

        $category = new Category();
        $category->name = $validated['name'];
        $category->description = $validated['description'] ?? null;
        $category->save();

        return to_route('categories.index')
            ->with('ok', 'Categoria registrada correctamente.');
    }

    public function show(Category $category)
    {
        //
    }

    public function edit(Category $category)
    {
        return view('settings.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'max:50',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'description' => 'nullable|max:255',
        ]);

        $category->name = $validated['name'];
        $category->description = $validated['description'] ?? null;
        $category->save();

        return to_route('categories.index')
            ->with('ok', 'Categoria actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return to_route('categories.index')
            ->with('ok', 'Categoria eliminada correctamente.');
    }
}
