<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilterValue;
use App\Models\Filter;
use Illuminate\Http\Request;

class FilterValueController extends Controller
{
    // 1. List Page
    public function index()
    {
        // 'with' ka use karke parent Filter ka naam bhi layenge
        $filterValues = FilterValue::with('filter')->latest()->get();
        return view('admin.filter-values.index', compact('filterValues'));
    }

    // 2. Create Page
    public function create()
    {
        // Dropdown ke liye saare Filters layein
        $filters = Filter::latest()->get();
        return view('admin.filter-values.create', compact('filters'));
    }

    // 3. Store Logic
    public function store(Request $request)
    {
        $request->validate([
            'filter_id' => 'required|exists:filters,id', // Check karein ki filter exist karta hai
            'value'     => 'required|string|max:255',
        ]);

        FilterValue::create($request->all());

        return redirect()->route('admin.filter-values.index')
            ->with('success', 'Filter Value added successfully!');
    }

    // 4. Edit Page
    public function edit(string $id)
    {
        $filterValue = FilterValue::findOrFail($id);
        $filters = Filter::latest()->get();
        return view('admin.filter-values.edit', compact('filterValue', 'filters'));
    }

    // 5. Update Logic
    public function update(Request $request, string $id)
    {
        $filterValue = FilterValue::findOrFail($id);

        $request->validate([
            'filter_id' => 'required|exists:filters,id',
            'value'     => 'required|string|max:255',
        ]);

        $filterValue->update($request->all());

        return redirect()->route('admin.filter-values.index')
            ->with('success', 'Filter Value updated successfully!');
    }

    // 6. Delete Logic
    public function destroy(string $id)
    {
        $filterValue = FilterValue::findOrFail($id);
        $filterValue->delete();

        return redirect()->route('admin.filter-values.index')
            ->with('success', 'Filter Value deleted successfully!');
    }
}
