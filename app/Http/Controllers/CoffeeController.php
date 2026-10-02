<?php

namespace App\Http\Controllers;

use App\Models\Coffee;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoffeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Coffee::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('category', function($catQ) use ($search) {
                      $catQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $coffees = $query->latest()->paginate(12);
        $categories = Category::where('status', 'active')->get();

        return view('coffees.index', compact('coffees', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'gte:0'],
            'status' => ['required', 'in:available,out_of_stock,inactive'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('coffees', 'public');
            $validated['image'] = Storage::url($path);
        }

        Coffee::create($validated);

        return redirect()->route('coffees.index')->with('success', 'Coffee menu item added successfully.');
    }

    public function update(Request $request, Coffee $coffee)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'gte:0'],
            'status' => ['required', 'in:available,out_of_stock,inactive'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('coffees', 'public');
            $validated['image'] = Storage::url($path);
        }

        $coffee->update($validated);

        return redirect()->route('coffees.index')->with('success', 'Coffee menu item updated successfully.');
    }

    public function destroy(Coffee $coffee)
    {
        $coffee->delete();
        return redirect()->route('coffees.index')->with('success', 'Coffee menu item deleted successfully.');
    }
}
