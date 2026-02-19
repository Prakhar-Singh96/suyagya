<?php

namespace App\Http\Controllers\Admin;

use App\Models\Filter;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    // 1. List Products
    public function index()
    {
        $products = Product::with(['category', 'subCategory'])->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    // 2. Show Create Form
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $subCategories = SubCategory::where('status', 1)->get();
        $filters = Filter::with('filterValues')->get();

        return view('admin.products.create', compact('categories', 'subCategories', 'filters'));
    }

    // 3. Store Product Logic
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'mrp_price' => 'required|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'required|integer',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_gemstone' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // B. Save Product Basic Info
            $data = $request->except(['main_image', 'gallery_images', 'filter_values', 'variants']);

            // --- Price Calculation for Main Product ---
            $mrp = $request->mrp_price;
            $inputPrice = $request->price;
            $inputDiscount = $request->discount;

            if ($request->filled('price') && $mrp > 0) {
                $sellingPrice = $inputPrice;
                $calculatedDiscount = (($mrp - $sellingPrice) / $mrp) * 100;
                $discount = $calculatedDiscount;
            } elseif ($request->filled('discount')) {
                $discount = $inputDiscount;
                $sellingPrice = $mrp - ($mrp * $discount / 100);
            } else {
                $sellingPrice = $mrp;
                $discount = 0;
            }

            $data['price'] = round($sellingPrice, 2);
            $data['discount'] = round($discount, 2);

            // Image Upload
            $data['main_image'] = uploadImage($request, 'main_image', 'uploads/products/main');
            $data['og_image'] = uploadImage($request, 'og_image', 'uploads/products/og');

            // Checkboxes
            $data['is_siddh_enabled'] = $request->has('is_siddh_enabled') ? 1 : 0;
            $data['siddh_price'] = $request->siddh_price ?? 0;
            $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
            $data['is_best_seller'] = $request->has('is_best_seller') ? 1 : 0;
            $data['emi_available'] = $request->has('emi_available') ? 1 : 0;
            $data['is_gemstone'] = $request->has('is_gemstone') ? 1 : 0;

            $product = Product::create($data);

            // --- ⚖️ WEIGHT VARIANT LOGIC START (Updated) ---
            $hasVariants = false;
            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    if (!empty($variant['weight']) && !empty($variant['price'])) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'weight' => $variant['weight'],
                            'mrp_price' => $variant['mrp'] ?? $variant['price'],
                            'selling_price' => $variant['price'],
                            'quantity' => $variant['qty'] ?? 0,     // 🟢 Added Qty
                            'discount' => $variant['discount'] ?? 0 // 🟢 Added Discount
                        ]);
                        $hasVariants = true;
                    }
                }
            }

            // 🔥 AUTO-SYNC: Update Main Product Price & Stock based on Variants
            if ($hasVariants) {
                $this->syncMainProductWithVariants($product);
            }
            // -----------------------------------------------

            // Gallery Images
            if ($request->hasFile('gallery_images')) {
                $alts = $request->gallery_alts ?? [];
                foreach ($request->file('gallery_images') as $index => $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/products/gallery'), $filename);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => 'uploads/products/gallery/' . $filename,
                        'alt' => $alts[$index] ?? null,
                    ]);
                }
            }

            // Sync Filters
            if ($request->has('filter_values')) {
                $product->filterValues()->sync($request->filter_values);
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    // 4. Show Edit Form
    public function edit($id)
    {
        $product = Product::with(['images', 'filterValues', 'variants'])->findOrFail($id);
        $categories = Category::where('status', 1)->get();
        $subCategories = SubCategory::where('category_id', $product->category_id)->get();
        $filters = Filter::with('filterValues')->get();
        $selectedFilters = $product->filterValues->pluck('id')->toArray();

        return view('admin.products.edit', compact('product', 'categories', 'subCategories', 'filters', 'selectedFilters'));
    }

    // 5. Update Product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'category_id' => 'required|exists:categories,id',
            'mrp_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['main_image', 'gallery_images', 'filter_values', 'variants']);

            // --- Price Logic ---
            $mrp = $request->mrp_price;
            $inputPrice = $request->price;
            $inputDiscount = $request->discount;

            if ($request->filled('price') && $mrp > 0) {
                $sellingPrice = $inputPrice;
                $calculatedDiscount = (($mrp - $sellingPrice) / $mrp) * 100;
                $discount = $calculatedDiscount;
            } elseif ($request->filled('discount')) {
                $discount = $inputDiscount;
                $sellingPrice = $mrp - ($mrp * $discount / 100);
            } else {
                $sellingPrice = $mrp;
                $discount = 0;
            }
            $data['price'] = round($sellingPrice, 2);
            $data['discount'] = round($discount, 2);

            // Checkboxes
            $data['is_siddh_enabled'] = $request->has('is_siddh_enabled') ? 1 : 0;
            $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
            $data['is_best_seller'] = $request->has('is_best_seller') ? 1 : 0;
            $data['emi_available'] = $request->has('emi_available') ? 1 : 0;

            // Images Logic
            if ($request->hasFile('main_image')) {
                deleteImage($product->main_image);
                $data['main_image'] = uploadImage($request, 'main_image', 'uploads/products/main');
            }
            if ($request->hasFile('og_image')) {
                deleteImage($product->og_image);
                $data['og_image'] = uploadImage($request, 'og_image', 'uploads/products/og');
            }

            $product->update($data);

            // --- ⚖️ UPDATE VARIANTS LOGIC (Updated) ---
            $product->variants()->delete(); // Purane variants delete

            $hasVariants = false;
            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    if (!empty($variant['weight']) && !empty($variant['price'])) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'weight' => $variant['weight'],
                            'mrp_price' => $variant['mrp'] ?? $variant['price'],
                            'selling_price' => $variant['price'],
                            'quantity' => $variant['qty'] ?? 0,     // 🟢 Added Qty
                            'discount' => $variant['discount'] ?? 0 // 🟢 Added Discount
                        ]);
                        $hasVariants = true;
                    }
                }
            }

            // 🔥 AUTO-SYNC: Update Main Product Price & Stock based on Variants
            if ($hasVariants) {
                $this->syncMainProductWithVariants($product);
            }
            // --------------------------------

            // Gallery Updates
            if ($request->has('existing_alts')) {
                foreach ($request->existing_alts as $imageId => $altText) {
                    ProductImage::where('id', $imageId)->update(['alt' => $altText]);
                }
            }
            if ($request->hasFile('gallery_images')) {
                $newAlts = $request->gallery_alts ?? [];
                foreach ($request->file('gallery_images') as $index => $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/products/gallery'), $filename);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => 'uploads/products/gallery/' . $filename,
                        'alt' => $newAlts[$index] ?? null,
                    ]);
                }
            }

            // Filters
            if ($request->has('filter_values')) {
                $product->filterValues()->sync($request->filter_values);
            } else {
                $product->filterValues()->detach();
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    // 6. Delete Product
    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);
        deleteImage($product->main_image);
        deleteImage($product->og_image);
        foreach ($product->images as $img) {
            deleteImage($img->image);
            $img->delete();
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    function getSubCategories($categoryId)
    {
        $subs = SubCategory::where('category_id', $categoryId)->where('status', 1)->get();
        return response()->json($subs);
    }

    function deleteGalleryImage($id)
    {
        $img = ProductImage::findOrFail($id);
        deleteImage($img->image);
        $img->delete();
        return response()->json(['success' => true]);
    }

    // 🔥 Helper Function: Sync Main Product Data with Variants
    private function syncMainProductWithVariants($product)
    {
        // Sare variants load karo
        $product->refresh();
        $variants = $product->variants;

        if ($variants->count() > 0) {
            // Logic:
            // 1. Total Quantity = Sum of all variant quantities
            // 2. Main Price = Lowest Selling Price among variants
            // 3. Main MRP = MRP of that lowest priced variant

            $totalQty = $variants->sum('quantity');

            // Sabse sasta variant dhundo
            $minVariant = $variants->sortBy('selling_price')->first();

            if ($minVariant) {
                $product->update([
                    'price' => $minVariant->selling_price,
                    'mrp_price' => $minVariant->mrp_price,
                    'quantity' => $totalQty,
                    'discount' => $minVariant->discount
                ]);
            }
        }
    }
}
