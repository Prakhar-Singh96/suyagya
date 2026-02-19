<?php

namespace App\Http\Controllers\Admin;

use App\Models\Filter;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\GemstoneVariant; // 👈 Import Gemstone Model
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
            'product_main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images.*' => 'nullable|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm|max:51200',

            // Gemstone Fields
            'is_gemstone' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // B. Save Product Basic Info
            $data = $request->except(['product_main_image','main_image', 'gallery_images', 'filter_values', 'variants', 'gem_variants']);

            // --- Price Calculation Logic ---
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

            if ($request->has('faqs')) {
                // Array values reset karke JSON encode karo
                $data['faq_content'] = array_values($request->faqs);
            } else {
                $data['faq_content'] = null;
            }

            $data['price'] = round($sellingPrice, 2);
            $data['discount'] = round($discount, 2);

            // Image Upload
            $data['main_image'] = uploadImage($request, 'main_image', 'uploads/products/main');
            $data['product_main_image'] = uploadImage($request, 'product_main_image', 'uploads/products/main/product');
            $data['og_image'] = uploadImage($request, 'og_image', 'uploads/products/og');


            // Checkboxes
            $data['is_siddh_enabled'] = $request->has('is_siddh_enabled') ? 1 : 0;
            $data['siddh_price'] = $request->siddh_price ?? 0;
            $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
            $data['is_best_seller'] = $request->has('is_best_seller') ? 1 : 0;
            $data['emi_available'] = $request->has('emi_available') ? 1 : 0;

            // 💎 Gemstone specific fields
            $data['is_gemstone'] = $request->has('is_gemstone') ? 1 : 0;

            $product = Product::create($data);

            // =========================================================
            // 🔗 SAVE ADDITIONAL CATEGORIES (Pivot Table)
            // =========================================================
            if ($request->has('additional_cats')) {
                $syncData = [];

                foreach ($request->additional_cats as $item) {
                    // Sirf tab add karein jab Category select ho
                    if (!empty($item['category_id'])) {
                        // Hum structure bana rahe hain: [category_id => ['sub_category_id' => id]]
                        // Isse pivot table me extra column 'sub_category_id' bhi bhar jayega

                        // Note: Agar ek hi category multiple baar select ho gayi to override ho jayegi,
                        // isliye hum index ko ignore karke unique ID use karte hain, par attach best hai yahan.

                        $product->additionalCategories()->attach($item['category_id'], [
                            'sub_category_id' => $item['sub_category_id'] ?? null
                        ]);
                    }
                }
            }

            // =========================================================
            // 💎 IF GEMSTONE: SAVE GEMSTONE VARIANTS
            // =========================================================
            if ($data['is_gemstone'] == 1) {
                if ($request->has('gem_variants')) {
                    foreach ($request->gem_variants as $gem) {
                        // Type & Price are required
                        if (!empty($gem['type']) && !empty($gem['price'])) {
                            GemstoneVariant::create([
                                'product_id' => $product->id,
                                'type' => $gem['type'], // ring, pendant, loose
                                'ratti_size' => $gem['ratti'] ?? null,
                                'material' => $gem['material'] ?? null, // silver, panchdhatu
                                'ring_size' => $gem['ring_size'] ?? null,
                                'price' => $gem['price'],
                                'mrp' => $gem['mrp'] ?? $gem['price'],
                                'quantity' => $gem['qty'] ?? 0
                            ]);
                        }
                    }
                    // Auto-sync Main Price (Lowest Gemstone Price)
                    $this->syncMainProductWithGemstones($product);
                }
            }
            // =========================================================
            // ⚖️ ELSE: SAVE NORMAL WEIGHT VARIANTS
            // =========================================================
            else {
                if ($request->has('variants')) {
                    $hasVariants = false;
                    foreach ($request->variants as $variant) {
                        if (!empty($variant['weight']) && !empty($variant['price'])) {
                            ProductVariant::create([
                                'product_id' => $product->id,
                                'weight' => $variant['weight'],
                                'mrp_price' => $variant['mrp'] ?? $variant['price'],
                                'selling_price' => $variant['price'],
                                'quantity' => $variant['qty'] ?? 0,
                                'discount' => $variant['discount'] ?? 0
                            ]);
                            $hasVariants = true;
                        }
                    }
                    if ($hasVariants) {
                        $this->syncMainProductWithVariants($product);
                    }
                }
            }

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
        // Load both relations: variants AND gemstoneVariants
        // 👇 yahan 'additionalCategories' add karein
        $product = Product::with(['images', 'filterValues', 'variants', 'gemstoneVariants', 'additionalCategories'])->findOrFail($id);

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

            $data = $request->except(['product_main_image','main_image', 'gallery_images', 'filter_values', 'variants', 'gem_variants']);

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


            if ($request->has('faqs')) {
                // Array values reset karke JSON encode karo
                $data['faq_content'] = array_values($request->faqs);
            } else {
                $data['faq_content'] = null;
            }

            $data['price'] = round($sellingPrice, 2);
            $data['discount'] = round($discount, 2);

            // Checkboxes
            $data['is_siddh_enabled'] = $request->has('is_siddh_enabled') ? 1 : 0;
            $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
            $data['is_best_seller'] = $request->has('is_best_seller') ? 1 : 0;
            $data['emi_available'] = $request->has('emi_available') ? 1 : 0;

            // Gemstone fields
            $data['is_gemstone'] = $request->has('is_gemstone') ? 1 : 0;
            // Note: If checkbox unchecked, these will act as 0 or old value if not present in request (handled by update)

            // Images Logic
            if ($request->hasFile('main_image')) {
                deleteImage($product->main_image);
                $data['main_image'] = uploadImage($request, 'main_image', 'uploads/products/main');
            }

            if ($request->hasFile('product_main_image')) {
                deleteImage($product->main_image);
                $data['product_main_image'] = uploadImage($request, 'product_main_image', 'uploads/products/main/product');
            }
            if ($request->hasFile('og_image')) {
                deleteImage($product->og_image);
                $data['og_image'] = uploadImage($request, 'og_image', 'uploads/products/og');
            }

            $product->update($data);

            // =========================================================
            // 🔗 UPDATE ADDITIONAL CATEGORIES (Sync)
            // =========================================================
            if ($request->has('additional_cats')) {
                $syncData = [];
                foreach ($request->additional_cats as $item) {
                    if (!empty($item['category_id'])) {
                        // Array Key me Category ID dalne se duplicate hat jayenge
                        // Value me Pivot table ka data (sub_category_id)
                        $syncData[$item['category_id']] = [
                            'sub_category_id' => $item['sub_category_id'] ?? null
                        ];
                    }
                }
                // Sync purane hata kar naye dal deta hai
                $product->additionalCategories()->sync($syncData);
            } else {
                // Agar user ne sab rows delete kar di, to DB se bhi hata do
                $product->additionalCategories()->detach();
            }

            // =========================================================
            // 💎 IF GEMSTONE: UPDATE GEMSTONE VARIANTS
            // =========================================================
            if ($product->is_gemstone) {
                // Clear old normal variants if any
                $product->variants()->delete();

                // Clear old gem variants and re-create (simplest way)
                $product->gemstoneVariants()->delete();

                if ($request->has('gem_variants')) {
                    foreach ($request->gem_variants as $gem) {
                        if (!empty($gem['type']) && !empty($gem['price'])) {
                            GemstoneVariant::create([
                                'product_id' => $product->id,
                                'type' => $gem['type'],
                                'ratti_size' => $gem['ratti'] ?? null,
                                'material' => $gem['material'] ?? null,
                                'ring_size' => $gem['ring_size'] ?? null,
                                'price' => $gem['price'],
                                'mrp' => $gem['mrp'] ?? $gem['price'],
                                'quantity' => $gem['qty'] ?? 0
                            ]);
                        }
                    }
                    $this->syncMainProductWithGemstones($product);
                }
            }
            // =========================================================
            // ⚖️ ELSE: UPDATE NORMAL VARIANTS
            // =========================================================
            else {
                // Clear old gem variants if any
                $product->gemstoneVariants()->delete();

                // Clear and recreate normal variants
                $product->variants()->delete();

                if ($request->has('variants')) {
                    $hasVariants = false;
                    foreach ($request->variants as $variant) {
                        if (!empty($variant['weight']) && !empty($variant['price'])) {
                            ProductVariant::create([
                                'product_id' => $product->id,
                                'weight' => $variant['weight'],
                                'mrp_price' => $variant['mrp'] ?? $variant['price'],
                                'selling_price' => $variant['price'],
                                'quantity' => $variant['qty'] ?? 0,
                                'discount' => $variant['discount'] ?? 0
                            ]);
                            $hasVariants = true;
                        }
                    }
                    if ($hasVariants) {
                        $this->syncMainProductWithVariants($product);
                    }
                }
            }

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

    // 🔥 Helper 1: Sync Normal Variants
    private function syncMainProductWithVariants($product)
    {
        $product->refresh();
        $variants = $product->variants;

        if ($variants->count() > 0) {
            $totalQty = $variants->sum('quantity');
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

    // 🔥 Helper 2: Sync Gemstone Variants
    private function syncMainProductWithGemstones($product)
    {
        $product->refresh();
        $gems = $product->gemstoneVariants;

        if ($gems->count() > 0) {
            $totalQty = $gems->sum('quantity');

            // Find lowest price among all gem variants
            $minGem = $gems->sortBy('price')->first();

            if ($minGem) {
                // Calculate discount if MRP > Price
                $discount = 0;
                if ($minGem->mrp > 0 && $minGem->mrp > $minGem->price) {
                    $discount = (($minGem->mrp - $minGem->price) / $minGem->mrp) * 100;
                }

                $product->update([
                    'price' => $minGem->price,
                    'mrp_price' => $minGem->mrp,
                    'quantity' => $totalQty,
                    'discount' => round($discount, 2)
                ]);
            }
        }
    }

    // ✅ CKEditor Image Upload Handler (Updated)
    public function uploadCkImage(Request $request)
    {
        try {
            // 1. Check if file is present
            if ($request->hasFile('upload')) {

                $file = $request->file('upload');

                // 2. Generate Unique Filename
                $originName = $file->getClientOriginalName();
                $fileName = pathinfo($originName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $newFileName = $fileName . '_' . time() . '.' . $extension;

                // 3. Define Path
                $destinationPath = public_path('uploads/description');

                // 4. Check & Create Directory
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                // 5. Move File
                $file->move($destinationPath, $newFileName);

                // 6. Generate URL
                $url = asset('uploads/description/' . $newFileName);

                // ✅ SUCCESS RESPONSE (CKEditor format)
                return response()->json([
                    'uploaded' => 1,
                    'fileName' => $newFileName,
                    'url' => $url
                ]);
            }

            return response()->json(['error' => ['message' => 'No file found in request']], 400);
        } catch (\Exception $e) {
            // ❌ ERROR RESPONSE (Taaki wo popup me HTML code na dikhaye)
            return response()->json(['error' => ['message' => $e->getMessage()]], 500);
        }
    }
}
