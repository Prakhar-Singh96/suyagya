<?php

namespace App\Http\Controllers\Admin;

use App\Models\Filter;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\GemstoneVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image; // 👈 Make sure this is imported

class ProductController extends Controller
{
    // 1. List Products
    public function index(Request $request)
    {
        // 🔍 Search Logic
        $search = $request->input('search');
        $products = Product::with(['category', 'subCategory'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->latest() // Newest first
            ->paginate(20); // 15 per page
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
            'product_main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validation
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['product_main_image', 'main_image', 'og_image', 'gallery_images', 'filter_values', 'variants', 'gem_variants']);

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
                $data['faq_content'] = array_values($request->faqs);
            } else {
                $data['faq_content'] = null;
            }

            $data['price'] = round($sellingPrice, 2);
            $data['discount'] = round($discount, 2);

            // =========================================================
            // 🖼️ IMAGE UPLOAD & RESIZE LOGIC (Updated)
            // =========================================================

            // 1. Product Detail Image (600x600)
            if ($request->hasFile('product_main_image')) {
                $file = $request->file('product_main_image');
                $data['product_main_image'] = $this->uploadAndResize($file, 'uploads/products/main/product', 1080, 1080);

                // 🚀 AUTO OG GENERATION: Agar OG image upload nahi ki, to isiko use karein
                if (!$request->hasFile('og_image')) {
                    // Same file, but resized to 1200x630 specifically for Facebook
                    $data['og_image'] = $this->uploadAndResize($file, 'uploads/products/og', 1200, 630);
                }
            }

            // 2. Listing/Home Image (310x310)
            if ($request->hasFile('main_image')) {
                $data['main_image'] = $this->uploadAndResize($request->file('main_image'), 'uploads/products/main', 600, 600);
            }

            // 3. Manual OG Image (1200x630) - Agar user ne alag se upload ki
            if ($request->hasFile('og_image')) {
                $data['og_image'] = $this->uploadAndResize($request->file('og_image'), 'uploads/products/og', 1200, 630);
            }

            // =========================================================

            $data['is_siddh_enabled'] = $request->has('is_siddh_enabled') ? 1 : 0;
            $data['siddh_price'] = $request->siddh_price ?? 0;
            $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
            $data['is_best_seller'] = $request->has('is_best_seller') ? 1 : 0;
            $data['emi_available'] = $request->has('emi_available') ? 1 : 0;
            $data['is_gemstone'] = $request->has('is_gemstone') ? 1 : 0;

            $data['astro_planet'] = $request->astro_planet;
            $data['astro_rashi'] = $request->astro_rashi;
            $data['astro_benefits'] = $request->astro_benefits;
            $data['sort_order'] = $request->sort_order ?? 0;

            $product = Product::create($data);

            // Save Additional Categories
            if ($request->has('additional_cats')) {
                foreach ($request->additional_cats as $item) {
                    if (!empty($item['category_id'])) {
                        $product->additionalCategories()->attach($item['category_id'], [
                            'sub_category_id' => $item['sub_category_id'] ?? null
                        ]);
                    }
                }
            }

            // Variants Logic (Gemstone or Normal)
            if ($data['is_gemstone'] == 1) {
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
            } else {
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

            // Gallery Images (Normal Upload - No Resize needed or can be added)
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
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['product_main_image', 'main_image', 'og_image', 'gallery_images', 'filter_values', 'variants', 'gem_variants']);

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
                $data['faq_content'] = array_values($request->faqs);
            } else {
                $data['faq_content'] = null;
            }

            $data['price'] = round($sellingPrice, 2);
            $data['discount'] = round($discount, 2);

            $data['is_siddh_enabled'] = $request->has('is_siddh_enabled') ? 1 : 0;
            $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
            $data['is_best_seller'] = $request->has('is_best_seller') ? 1 : 0;
            $data['emi_available'] = $request->has('emi_available') ? 1 : 0;
            $data['is_gemstone'] = $request->has('is_gemstone') ? 1 : 0;

            $data['astro_planet'] = $request->astro_planet;
            $data['astro_rashi'] = $request->astro_rashi;
            $data['astro_benefits'] = $request->astro_benefits;
            $data['sort_order'] = $request->sort_order ?? 0;

            // =========================================================
            // 🖼️ IMAGE UPDATE LOGIC (With Auto OG Generation)
            // =========================================================

            // 1. Product Main Image (600x600)
            if ($request->hasFile('product_main_image')) {
                // Delete Old
                if ($product->product_main_image && File::exists(public_path($product->product_main_image))) {
                    File::delete(public_path($product->product_main_image));
                }

                $file = $request->file('product_main_image');
                $data['product_main_image'] = $this->uploadAndResize($file, 'uploads/products/main/product', 1080, 1080);

                // 🚀 AUTO OG UPDATE: Agar naya main image dala hai, aur OG explicitly nahi dala
                if (!$request->hasFile('og_image')) {
                    // Purana OG delete karo
                    if ($product->og_image && File::exists(public_path($product->og_image))) {
                        File::delete(public_path($product->og_image));
                    }
                    // Naya banao 1200x630
                    $data['og_image'] = $this->uploadAndResize($file, 'uploads/products/og', 1200, 630);
                }
            }

            // 2. Listing Image (310x310)
            if ($request->hasFile('main_image')) {
                if ($product->main_image && File::exists(public_path($product->main_image))) {
                    File::delete(public_path($product->main_image));
                }
                $data['main_image'] = $this->uploadAndResize($request->file('main_image'), 'uploads/products/main', 600, 600);
            }

            // 3. OG Image Manual Update (1200x630)
            if ($request->hasFile('og_image')) {
                if ($product->og_image && File::exists(public_path($product->og_image))) {
                    File::delete(public_path($product->og_image));
                }
                $data['og_image'] = $this->uploadAndResize($request->file('og_image'), 'uploads/products/og', 1200, 630);
            }

            $product->update($data);

            // Update Categories
            if ($request->has('additional_cats')) {
                $syncData = [];
                foreach ($request->additional_cats as $item) {
                    if (!empty($item['category_id'])) {
                        $syncData[$item['category_id']] = [
                            'sub_category_id' => $item['sub_category_id'] ?? null
                        ];
                    }
                }
                $product->additionalCategories()->sync($syncData);
            } else {
                $product->additionalCategories()->detach();
            }

            // Variant Updates
            if ($product->is_gemstone) {
                $product->variants()->delete();
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
            } else {
                $product->gemstoneVariants()->delete();
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

        if ($product->main_image && File::exists(public_path($product->main_image))) {
            File::delete(public_path($product->main_image));
        }
        if ($product->product_main_image && File::exists(public_path($product->product_main_image))) {
            File::delete(public_path($product->product_main_image));
        }
        if ($product->og_image && File::exists(public_path($product->og_image))) {
            File::delete(public_path($product->og_image));
        }

        foreach ($product->images as $img) {
            if (File::exists(public_path($img->image))) {
                File::delete(public_path($img->image));
            }
            $img->delete();
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    // ... (getSubCategories, deleteGalleryImage, etc. same as before) ...
    function getSubCategories($categoryId)
    {
        $subs = SubCategory::where('category_id', $categoryId)->where('status', 1)->get();
        return response()->json($subs);
    }

    function deleteGalleryImage($id)
    {
        $img = ProductImage::findOrFail($id);
        if (File::exists(public_path($img->image))) {
            File::delete(public_path($img->image));
        }
        $img->delete();
        return response()->json(['success' => true]);
    }

    // ... (syncMainProductWithVariants, syncMainProductWithGemstones, uploadCkImage - SAME AS BEFORE) ...
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
            $minGem = $gems->sortBy('price')->first();

            if ($minGem) {
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

    // ✅ CKEditor Image Upload Handler
    public function uploadCkImage(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $originName = $file->getClientOriginalName();
                $fileName = pathinfo($originName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $newFileName = $fileName . '_' . time() . '.' . $extension;
                $destinationPath = public_path('uploads/description');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $newFileName);
                $url = asset('uploads/description/' . $newFileName);

                return response()->json([
                    'uploaded' => 1,
                    'fileName' => $newFileName,
                    'url' => $url
                ]);
            }
            return response()->json(['error' => ['message' => 'No file found in request']], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 500);
        }
    }

    // =======================================================
    // 🔥 NEW HELPER: Upload & Resize (Intervention Image)
    // =======================================================
    private function uploadAndResize($file, $path, $width, $height)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path($path);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        // 1. Create Image Instance
        $img = Image::make($file->getRealPath());

        // 2. Resize with Aspect Ratio (Image Chapti nahi hogi)
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        // 3. Resize Canvas (Agar size match nahi hua to White Background add karega)
        $img->resizeCanvas($width, $height, 'center', false, '#ffffff');

        // 4. Save
        $img->save($destinationPath . '/' . $filename, 80); // 80% Quality

        return $path . '/' . $filename;
    }
}
