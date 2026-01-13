<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:view_products')->only('index');
        // $this->middleware('permission:create_product')->only('create');
        // $this->middleware('permission:edit_product')->only('edit');
        // $this->middleware('permission:delete_product')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('categories')->get();
        return view('admin.products.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categoryController = new CategoryController();
        $categories = $categoryController->buildCategoryTreeArray();
        $products = Product::where('is_active', 1)->get();
        return view('admin.products.products.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sinhala_name' => 'nullable|string|max:255',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'short_description' => 'nullable|string|max:500',
            'variant_name' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'shipping_type' => 'required|string|in:weight,free',
            'related_products' => 'nullable|array',
            'variants' => 'nullable|array',

        ], [
            'name.required' => 'Product name is required.',
            'name.string' => 'Product name must be a valid string.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'category_ids.required' => 'Please select at least one category.',
            'category_ids.array' => 'Categories must be an array.',
            'category_ids.*.exists' => 'One or more selected categories are invalid.',
            'description.string' => 'Description must be a valid string.',
            'shipping_type.required' => 'Please select a shipping type (weight or free).',
            'stock.required' => 'Quantity is required.',
            'related_products.array' => 'Related products must be an array.',
            'variants.array' => 'Variants must be an array.',
        ]);

        if ($request->enable_variants) {
            $request->validate([
                'variants' => 'required|array|min:1',
                'variants.*.name' => 'required|string|max:255',
                'variants.*.stock' => 'required|integer|min:0',
                'variants.*.weight' => 'required|numeric|min:0',
                'variants.*.cost' => 'required|numeric|min:0',
                'variants.*.price' => 'required|numeric|min:0',
            ], [
                'variants.required' => 'At least one variant is required.',
                'variants.array' => 'Variants must be an array.',
                'variants.*.name.required' => 'Variant must have a name.',
                'variants.*.stock.required' => 'Variant must have a stock value.',
                'variants.*.weight.required' => 'Variant must have a weight.',
                'variants.*.cost.required' => 'Variant must have a cost.',
                'variants.*.price.required' => 'Variant must have a price.',
            ]);
        }


        try {
            $product = DB::transaction(function () use ($request) {
                $product = Product::create([
                    'name' => $request->name,
                    'sinhala_name' => $request->sinhala_name,
                    'short_description' => $request->short_description,
                    'how_to_use' => $request->how_to_use,
                    'description' => $request->descr,
                    'price' => $request->price ?? 0,
                    'special_price' => $request->special_price ?? 0,
                    'selling_price' => $request->selling_price ?? 0,
                    'special_price_type' => $request->special_price_type ?? null,
                    'special_price_start' => $request->sp_start_date ?? null,
                    'special_price_end' => $request->sp_end_date ?? null,
                    'meta_title' => $request->meta_title ?? null,
                    'meta_description' => $request->meta_description ?? null,
                    'is_free_shipping' => $request->shipping_type === 'free',
                    'weight' => $request->shipping_type === 'weight' ? ($request->weight ?? 0) : 0,
                    'is_active' => (bool) $request->published,
                    'allow_inquiries' => (bool) $request->allow_inquiries,
                    'is_featured' => (bool) $request->featured,
                    'track_stock' => true,
                ]);

                if ($request->has('related_products') && is_array($request->related_products)) {
                    foreach ($request->related_products as $related_product) {
                        $product->relatedProducts()->create([
                            'product_id' => $product->id,
                            'related_product_id' => $related_product,
                        ]);
                    }
                }

                $product->categories()->attach($request->category_ids);

                //Product Thumbnail
                if ($product) {
                    Helper::updateImage($product, 'product_thumbnail');
                }

                if ($request->enable_variants && $request->has('variants')) {
                    // Create standard variant first
                    if (!empty($request->stock) && !empty($request->price) && !empty($request->selling_price)) {
                        $standardStock = ProductStock::create([
                            'product_id' => $product->id,
                            'variant' => $request->variant_name,
                            'sku' => $request->sku,
                            'qty' => $request->stock,
                            'purchase_price' => $request->price,
                            'weight' => $request->weight,
                            'selling_price' => $request->selling_price,
                            'is_standard' => true,
                        ]);

                        // Handle standard variant image
                        if ($request->hasFile('variant_image')) {
                            $standardStock->addMediaFromRequest('variant_image')->toMediaCollection('product_stock_image');
                        }
                    }

                    // Create additional variants
                    foreach ($request->variants as $index => $variant) {
                        $variantStock = ProductStock::create([
                            'product_id' => $product->id,
                            'variant' => $variant['name'],
                            'sku' => $variant['sku'] ?? null,
                            'qty' => $variant['stock'] ?? 0,
                            'purchase_price' => $variant['cost'] ?? 0,
                            'weight' => $variant['weight'] ?? 0,
                            'selling_price' => $variant['price'] ?? 0,
                            'is_standard' => false,
                        ]);

                        // Handle variant image
                        $imageKey = "variants.{$index}.image";
                        if ($request->hasFile($imageKey)) {
                            $variantStock->addMediaFromRequest($imageKey)->toMediaCollection('product_stock_image');
                        }
                    }
                } else {
                    $product_stock = ProductStock::create([
                        'product_id' => $product->id,
                        'variant' => $request->variant_name,
                        'sku' => $request->sku,
                        'qty' => $request->stock,
                        'purchase_price' => $request->price,
                        'weight' => $request->weight,
                        'selling_price' => $request->selling_price,
                        'is_standard' => true,
                    ]);

                    if ($request->hasFile('variant_image')) {
                        $product_stock->addMediaFromRequest('variant_image')->toMediaCollection('product_stock_image');
                    }
                }

                return $product;
            });

            if ($product) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Product creation failed',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Product creation failed',
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Product $product) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //Categories
        $categoryController = new CategoryController();
        $categories = $categoryController->buildCategoryTreeArray();

        $product->product_thumb = Helper::imageDataForFilePond($product, 'product_thumbnail');

        //Related Products
        $relatedProducts = Product::where('id', '!=', $product->id)->get();

        return view('admin.products.products.edit', compact('categories', 'product', 'relatedProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sinhala_name' => 'nullable|string|max:255',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'short_description' => 'nullable|string|max:500',
            'variant_name' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'shipping_type' => 'required|string|in:weight,free',
            'related_products' => 'nullable|array',
            'variants' => 'nullable|array',

        ], [
            'name.required' => 'Product name is required.',
            'name.string' => 'Product name must be a valid string.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'category_ids.required' => 'Please select at least one category.',
            'category_ids.array' => 'Categories must be an array.',
            'category_ids.*.exists' => 'One or more selected categories are invalid.',
            'description.string' => 'Description must be a valid string.',
            'shipping_type.required' => 'Please select a shipping type (weight or free).',
            'stock.required' => 'Quantity is required.',
            'related_products.array' => 'Related products must be an array.',
            'variants.array' => 'Variants must be an array.',
        ]);

        if ($request->enable_variants) {
            $request->validate([
                'variants' => 'required|array|min:1',
                'variants.*.name' => 'required|string|max:255',
                'variants.*.stock' => 'required|integer|min:0',
                'variants.*.weight' => 'required|numeric|min:0',
                'variants.*.cost' => 'required|numeric|min:0',
                'variants.*.price' => 'required|numeric|min:0',
            ], [
                'variants.required' => 'At least one variant is required.',
                'variants.array' => 'Variants must be an array.',
                'variants.*.name.required' => 'Variant must have a name.',
                'variants.*.stock.required' => 'Variant must have a stock value.',
                'variants.*.weight.required' => 'Variant must have a weight.',
                'variants.*.cost.required' => 'Variant must have a cost.',
                'variants.*.price.required' => 'Variant must have a price.',
            ]);
        }
        try {


            DB::transaction(function () use ($request, $product) {

                // Update product fields
                $product->update([
                    'name' => $request->name,
                    'sinhala_name' => $request->sinhala_name,
                    'short_description' => $request->short_description,
                    'description' => $request->descr,
                    'how_to_use' => $request->how_to_use,
                    'price' => $request->price ?? 0,
                    'special_price' => $request->special_price ?? 0,
                    'selling_price' => $request->selling_price ?? 0,
                    'special_price_type' => $request->special_price_type ?? null,
                    'special_price_start' => $request->sp_start_date ?? null,
                    'special_price_end' => $request->sp_end_date ?? null,
                    'meta_title' => $request->meta_title ?? null,
                    'meta_description' => $request->meta_description ?? null,
                    'is_free_shipping' => $request->shipping_type === 'free',
                    'weight' => $request->shipping_type === 'weight' ? ($request->weight ?? 0) : 0,
                    'is_active' => (bool) $request->published,
                    'allow_inquiries' => (bool) $request->allow_inquiries,
                    'is_featured' => (bool) $request->featured,
                    'track_stock' => true,
                ]);

                // Update related products
                if ($request->has('related_products')) {
                    $product->relatedProducts()->delete();
                    foreach ($request->related_products as $related_product) {
                        $product->relatedProducts()->create([
                            'product_id' => $product->id,
                            'related_product_id' => $related_product,
                        ]);
                    }
                } else {
                    $product->relatedProducts()->delete();
                }

                // Sync categories
                $product->categories()->sync($request->category_ids);

                // Update thumbnail
                Helper::updateImage($product, 'product_thumbnail');

                $existingStocks = $product->stocks()->get()->keyBy('id');
                $processedIds = [];

                if ($request->enable_variants) {
                    // First, handle the standard variant (from the main pricing section)
                    $standardVariantData = [
                        'product_id' => $product->id,
                        'variant' => $request->variant_name,
                        'sku' => $request->sku ?? null,
                        'qty' => $request->stock,
                        'purchase_price' => $request->price ?? 0,
                        'weight' => $request->weight ?? 0,
                        'selling_price' => $request->selling_price ?? 0,
                        'is_standard' => true, // Add this flag to identify standard variant
                    ];

                    // Check if standard variant exists (get by is_standard flag or use the hidden standard ID)
                    $standardId = $request->standard ?? null;
                    if ($standardId && $existingStocks->has($standardId)) {
                        $standardStock = $existingStocks[$standardId];
                        $standardStock->update($standardVariantData);

                        // Handle standard variant image deletion
                        if ($request->delete_standard_image == '1') {
                            $standardStock->clearMediaCollection('product_stock_image');
                        }

                        // Handle standard variant image upload
                        if ($request->hasFile('variant_image')) {
                            $standardStock->clearMediaCollection('product_stock_image');
                            $standardStock->addMediaFromRequest('variant_image')->toMediaCollection('product_stock_image');
                        }

                        $processedIds[] = $standardId;
                    } else {
                        // Create new standard variant
                        $newStandardStock = ProductStock::create($standardVariantData);

                        // Handle standard variant image
                        if ($request->hasFile('variant_image')) {
                            $newStandardStock->addMediaFromRequest('variant_image')->toMediaCollection('product_stock_image');
                        }

                        $processedIds[] = $newStandardStock->id;
                    }

                    // Then handle additional variants
                    if ($request->variants) {
                        Log::info('Processing variants:', ['variants' => $request->variants]);
                        Log::info('All files:', ['files' => array_keys($request->allFiles())]);

                        foreach ($request->variants as $index => $variantData) {
                            $stockData = [
                                'product_id' => $product->id,
                                'variant' => $variantData['name'],
                                'sku' => $variantData['sku'] ?? null,
                                'qty' => $variantData['stock'] ?? 0,
                                'purchase_price' => $variantData['cost'] ?? 0,
                                'weight' => $variantData['weight'] ?? 0,
                                'selling_price' => $variantData['price'] ?? 0,
                            ];

                            // Check if this is an existing variant
                            if (!empty($variantData['id']) && $existingStocks->has($variantData['id'])) {
                                // Update existing variant
                                $variantStock = $existingStocks[$variantData['id']];
                                $variantStock->update($stockData);

                                // Handle variant image deletion
                                if (isset($variantData['delete_image']) && $variantData['delete_image'] == '1') {
                                    $variantStock->clearMediaCollection('product_stock_image');
                                }

                                // Handle variant image update
                                // Check if image file exists in the variants array
                                if (isset($request->file('variants')[$index]['image'])) {
                                    $imageFile = $request->file('variants')[$index]['image'];
                                    if ($imageFile && $imageFile->isValid()) {
                                        $variantStock->clearMediaCollection('product_stock_image');
                                        $variantStock->addMedia($imageFile)->toMediaCollection('product_stock_image');
                                    }
                                }

                                $processedIds[] = $variantData['id'];
                            } else {
                                // Create new variant
                                $newStock = ProductStock::create($stockData);

                                // Handle variant image for new variant
                                if (isset($request->file('variants')[$index]['image'])) {
                                    $imageFile = $request->file('variants')[$index]['image'];
                                    if ($imageFile && $imageFile->isValid()) {
                                        $newStock->addMedia($imageFile)->toMediaCollection('product_stock_image');
                                    }
                                }

                                $processedIds[] = $newStock->id;
                            }
                        }
                    }

                    // Delete any existing stocks not in processed IDs
                    if (!empty($processedIds)) {
                        $product->stocks()->whereNotIn('id', $processedIds)->delete();
                    }
                } else {
                    // If variants are disabled, only keep the standard variant
                    $standardVariantData = [
                        'product_id' => $product->id,
                        'variant' => $request->variant_name,
                        'sku' => $request->sku ?? null,
                        'qty' => $request->stock,
                        'purchase_price' => $request->price ?? 0,
                        'weight' => $request->weight ?? 0,
                        'selling_price' => $request->selling_price ?? 0,
                        'is_standard' => true,
                    ];

                    // Get standard variant by ID or is_standard flag
                    $standardId = $request->standard ?? null;
                    $standardStock = null;

                    if ($standardId && $existingStocks->has($standardId)) {
                        $standardStock = $existingStocks[$standardId];
                    } else {
                        $standardStock = $product->stocks()->where('is_standard', true)->first();
                    }

                    if ($standardStock) {
                        $standardStock->update($standardVariantData);

                        // Handle standard variant image deletion
                        if ($request->delete_standard_image == '1') {
                            $standardStock->clearMediaCollection('product_stock_image');
                        }

                        // Handle standard variant image upload
                        if ($request->hasFile('variant_image')) {
                            $standardStock->clearMediaCollection('product_stock_image');
                            $standardStock->addMediaFromRequest('variant_image')->toMediaCollection('product_stock_image');
                        }

                        $product->stocks()->where('id', '!=', $standardStock->id)->delete();
                    } else {
                        $newStandardStock = ProductStock::create($standardVariantData);

                        // Handle standard variant image
                        if ($request->hasFile('variant_image')) {
                            $newStandardStock->addMediaFromRequest('variant_image')->toMediaCollection('product_stock_image');
                        }

                        $product->stocks()->delete();
                    }
                }
            });

            // Prepare response with updated stock information
            $updatedStocks = $product->fresh()->stocks()->with('media')->get();
            $stockImages = [];

            foreach ($updatedStocks as $stock) {
                $stockImages[] = [
                    'id' => $stock->id,
                    'variant' => $stock->variant,
                    'is_standard' => $stock->is_standard,
                    'thumbnail' => $stock->thumbnail,
                    'has_image' => $stock->getMedia('product_stock_image')->count() > 0
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'stock_images' => $stockImages,
                'product_id' => $product->id
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "An error occurred while updating the product "
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            if ($product->orderInfos()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product has orders.',
                ], 400);
            }

            $product->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Product delete failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                // 'message' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function productStatus(Product $product)
    {
        try {
            if ($product->is_active) {
                $product->is_active = false;
            } else {
                $product->is_active = true;
            }
            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Upddated successfully.'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Product publish failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Update the featured status of the product.
     */
    public function productFeatured(Product $product)
    {
        try {
            if ($product->is_featured) {
                $product->is_featured = false;
            } else {
                $product->is_featured = true;
            }
            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Upddated successfully.'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Product featured failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Search products for Select2
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        $page = $request->get('page', 1);
        $perPage = 10;

        $products = Product::where('name', 'like', "%{$search}%")
            ->where('is_active', true)
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items' => $products->items(),
            'more' => $products->hasMorePages()
        ]);
    }


    /**
     * Bulk delete products
     */
    public function destroyBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        $products = Product::withCount('orderInfos')->whereIn('id', $ids)->get();

        $undeletable = [];
        $deletableIds = [];

        foreach ($products as $product) {
            if ($product->order_infos_count > 0) {
                $undeletable[] = $product->name ?? "ID {$product->id}";
            } else {
                $deletableIds[] = $product->id;
            }
        }

        try {
            if (!empty($deletableIds)) {
                Product::whereIn('id', $deletableIds)->forceDelete();
            }

            if (!empty($undeletable)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Some products could not be deleted because they have orders: ' . implode(', ', $undeletable),
                    'deleted_count' => count($deletableIds),
                    'skipped_count' => count($undeletable)
                ], 400);
            }

            return response()->json([
                'status' => true,
                'message' => count($deletableIds) . ' product(s) deleted successfully.',
            ]);
        } catch (Exception $e) {
            Log::error('Product delete failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }


    public function bulkUpload()
    {
        return view('admin.products.bulk-upload');
    }

    public function downloadSample()
    {
        $headers = [
            'product_id',
            'name',
            'sinhala_name',
            'variant',
            'sku',
            'qty',
            'selling_price',
            'purchase_price',
            'weight',
            'track_stock',
            'category_ids',
            'description',
            'short_description'
        ];

        $sampleData = [
            [
                '',
                'Apple',
                'ඇපල්',
                'Red',
                'APP-RED',
                '50',
                '150.00',
                '120.00',
                '180',
                '0',
                '1',
                '1,2',
                'Fresh red apples from local farms',
                'Crispy and sweet red apples'
            ],
            [
                '',
                'Digital Photography Course',
                'ඩිජිටල් ඡායාරූප පාඨමාලාව',
                'Standard',
                'DPC-001',
                '999',
                '49.99',
                '0.00',
                '0',
                '1',
                '0',
                '3',
                'Complete digital photography course with video tutorials',
                'Learn photography from basics to advanced'
            ],
            [
                '',
                'Banana',
                'කෙසෙල්',
                'Large',
                'BAN-L',
                '100',
                '70.00',
                '50.00',
                '200',
                '0',
                '1',
                '1',
                'Fresh organic bananas',
                'Large sized yellow bananas'
            ]
        ];

        $filename = 'product_bulk_upload_sample.csv';

        return response()->streamDownload(function () use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for proper Unicode support
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $headers);
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('bulk_file');
            $path = $file->getRealPath();

            // Read file with proper UTF-8 encoding
            $content = file_get_contents($path);

            // Remove BOM if present
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

            // Convert to UTF-8 if needed
            if (!mb_check_encoding($content, 'UTF-8')) {
                $content = mb_convert_encoding($content, 'UTF-8', 'auto');
            }

            // Split into lines and parse CSV
            $lines = explode("\n", $content);
            $data = array_map('str_getcsv', $lines);

            // Remove empty lines
            $data = array_filter($data, function ($row) {
                return !empty(array_filter($row));
            });

            $headers = array_shift($data);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $processedProducts = [];

            foreach ($data as $index => $row) {
                try {
                    if (count($row) < count($headers)) {
                        continue;
                    }

                    $rowData = array_combine($headers, $row);

                    // Validate required fields
                    if (empty($rowData['name']) || empty($rowData['selling_price'])) {
                        $errors[] = "Row " . ($index + 2) . ": Name and selling price are required";
                        $errorCount++;
                        continue;
                    }

                    $productName = trim($rowData['name']);
                    $productSinhalaName = trim($rowData['sinhala_name'] ?? '');

                    // Check if we need to create a new product or use existing
                    $product = null;

                    // If product_id is provided and exists, use it
                    if (!empty($rowData['product_id']) && is_numeric($rowData['product_id'])) {
                        $product = Product::find($rowData['product_id']);
                    }

                    // If no product found by ID, check by name
                    if (!$product) {
                        $productKey = $productName . '|' . $productSinhalaName;

                        if (isset($processedProducts[$productKey])) {
                            // Use already processed product
                            $product = $processedProducts[$productKey];
                        } else {
                            // Check if product exists in database
                            $product = Product::where('name', $productName)
                                ->where('sinhala_name', $productSinhalaName)
                                ->first();

                            // Create new product if doesn't exist
                            if (!$product) {
                                $trackStock = isset($rowData['track_stock']) ? in_array(strtolower($rowData['track_stock']), ['1', 'true', 'yes']) : true;

                                $product = Product::create([
                                    'name' => $productName,
                                    'sinhala_name' => $productSinhalaName ?: null,
                                    'description' => $rowData['description'] ?? null,
                                    'short_description' => $rowData['short_description'] ?? null,
                                    'price' => floatval($rowData['selling_price']),
                                    'selling_price' => floatval($rowData['selling_price']),
                                    'is_active' => false,
                                    'allow_inquiries' => true,
                                    'is_featured' => false,
                                    'is_free_shipping' => false,
                                    'weight' => floatval($rowData['weight'] ?? 0),
                                    'track_stock' => $trackStock,
                                ]);

                                // Handle category assignment
                                if (!empty($rowData['category_ids'])) {
                                    $categoryIds = array_map('trim', explode(',', $rowData['category_ids']));
                                    $categoryIds = array_filter($categoryIds, 'is_numeric');
                                    if (!empty($categoryIds)) {
                                        $product->categories()->attach($categoryIds);
                                    }
                                }
                            }

                            $processedProducts[$productKey] = $product;
                        }
                    }

                    // Check if this is the first variant for this product
                    $isFirstVariant = ProductStock::where('product_id', $product->id)->count() === 0;

                    // Create product stock/variant
                    $stockData = [
                        'product_id' => $product->id,
                        'variant' => trim($rowData['variant'] ?? 'Standard'),
                        'sku' => trim($rowData['sku'] ?? ''),
                        'qty' => intval($rowData['qty'] ?? 0),
                        'selling_price' => floatval($rowData['selling_price']),
                        'purchase_price' => floatval($rowData['purchase_price'] ?? 0),
                        'weight' => floatval($rowData['weight'] ?? 0),
                        'is_standard' => $isFirstVariant,
                    ];

                    // Check if this variant already exists for this product
                    $existingStock = ProductStock::where('product_id', $product->id)
                        ->where('variant', $stockData['variant'])
                        ->where('sku', $stockData['sku'])
                        ->first();

                    if ($existingStock) {
                        // Update existing stock
                        $existingStock->update($stockData);
                    } else {
                        // Create new stock
                        ProductStock::create($stockData);
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                    $errorCount++;
                }
            }

            $message = "Bulk upload completed. Success: $successCount variants processed, Errors: $errorCount";

            if ($errorCount > 0) {
                $message .= "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "\n... and " . (count($errors) - 10) . " more errors";
                }
                toast()->warning($message);
            } else {
                toast()->success($message);
            }
        } catch (\Exception $e) {
            toast()->error('Error processing file: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Update product order
     */
    public function updateOrder(Request $request)
    {
        try {
            $orderData = $request->input('order_data');

            if (!$orderData || !is_array($orderData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid order data'
                ], 400);
            }

            foreach ($orderData as $item) {
                if (isset($item['id']) && isset($item['order'])) {
                    Product::where('id', $item['id'])
                        ->update(['sort_order' => $item['order']]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Product order updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating product order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product order'
            ], 500);
        }
    }

    /**
     * Reset all product sort orders (alphabetical by name)
     */
    public function resetSortOrder(Request $request)
    {
        try {
            $sortBy = $request->input('sort_by', 'name'); // name, created_at, price
            $direction = $request->input('direction', 'asc'); // asc, desc

            $validSortFields = ['name', 'created_at', 'price', 'id'];
            $validDirections = ['asc', 'desc'];

            if (!in_array($sortBy, $validSortFields) || !in_array($direction, $validDirections)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid sort parameters'
                ], 400);
            }

            DB::transaction(function () use ($sortBy, $direction) {
                $products = Product::withoutGlobalScope('ordered')
                    ->orderBy($sortBy, $direction)
                    ->get();

                foreach ($products as $index => $product) {
                    $product->update(['sort_order' => $index + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Products sorted by {$sortBy} ({$direction}) successfully"
            ]);

        } catch (\Exception $e) {
            Log::error('Product sort reset failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset product sort order'
            ], 500);
        }
    }
}
