<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralFaq;
use Illuminate\Http\Request;

class GeneralFaqController extends Controller
{
    // 1. List All FAQs
    public function index()
    {
        // Sort Order ke hisab se layenge taaki important sawal upar dikhen
        $faqs = GeneralFaq::orderBy('sort_order', 'asc')->get();
        return view('admin.general_faqs.index', compact('faqs'));
    }

    // 2. Show Create Form
    public function create()
    {
        return view('admin.general_faqs.create');
    }

    // 3. Store New FAQ
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        GeneralFaq::create($request->all());

        return redirect()->route('admin.general-faqs.index')->with('success', 'FAQ added successfully!');
    }

    // 4. Show Edit Form
    public function edit($id)
    {
        $faq = GeneralFaq::findOrFail($id);
        return view('admin.general_faqs.edit', compact('faq'));
    }

    // 5. Update FAQ
    public function update(Request $request, $id)
    {
        $faq = GeneralFaq::findOrFail($id);

        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        $faq->update($request->all());

        return redirect()->route('admin.general-faqs.index')->with('success', 'FAQ updated successfully!');
    }

    // 6. Delete FAQ
    public function destroy($id)
    {
        $faq = GeneralFaq::findOrFail($id);
        $faq->delete();
        return redirect()->route('admin.general-faqs.index')->with('success', 'FAQ deleted successfully!');
    }
}
