<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filter;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    // 1. List Page
    public function index()
    {
        $filters = Filter::latest()->get();
        return view('admin.filters.index', compact('filters'));
    }

    // 2. Create Page
    public function create()
    {
        return view('admin.filters.create');
    }

    // 3. Store Logic
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:filters,slug',
        ]);

        Filter::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('admin.filters.index')
            ->with('success', 'Filter created successfully!');
    }

    // 4. Edit Page
    public function edit(string $id)
    {
        $filter = Filter::findOrFail($id);
        return view('admin.filters.edit', compact('filter'));
    }

    // 5. Update Logic
    public function update(Request $request, string $id)
    {
        $filter = Filter::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:filters,slug,' . $filter->id,
        ]);

        $filter->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('admin.filters.index')
            ->with('success', 'Filter updated successfully!');
    }

    // 6. Delete Logic
    public function destroy(string $id)
    {
        $filter = Filter::findOrFail($id);
        $filter->delete();

        return redirect()->route('admin.filters.index')
            ->with('success', 'Filter deleted successfully!');
    }
}
