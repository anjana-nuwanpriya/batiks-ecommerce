<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{

    public function search(Request $request, $categorySlug = null)
    {
        try {
            $sort = $request->input('sort');
            $queryTerm = $request->input('q');
            $stockStatus = $request->input('stock');
            $featuredStatus = $request->input('filter');
            $categoryTree = [];
            $selectedCategory = null;
            $parentCategory = null;
            $categories = [];
            $productsQuery = Product::where('is_active', true);

            // CATEGORY FILTERING
            if ($categorySlug) {
                $selectedCategory = Category::where('slug', $categorySlug)->first();

                // If category not found by slug, try by ID (for backward compatibility)
                if (!$selectedCategory && is_numeric($categorySlug)) {
                    $selectedCategory = Category::find($categorySlug);
                }

                // If still not found, redirect with error
                if (!$selectedCategory) {
                    Log::warning("Category not found for slug/ID: {$categorySlug}");
                    return redirect()->route('sf.products.list')->with('error', 'Category not found.');
                }

                $categoryController = new CategoryController();
                $subcategoryIds = $categoryController->getSubcategoryIds($selectedCategory->id);
                $categoryTree = $categoryController->getCategoryTree($selectedCategory->id);

                // Get parent category and sibling categories
                if ($selectedCategory->parent_id) {
                    $parentCategory = Category::findOrFail($selectedCategory->parent_id);
                    $categories = Category::where('parent_id', $selectedCategory->parent_id)
                        ->where('status', true)
                        ->get();
                } else {
                    $categories = collect([$selectedCategory]); // Wrap in collection for consistency
                }

                $productsQuery->whereHas('categories', fn($q) => $q->whereIn('categories.id', $subcategoryIds));
            } else {
                $categories = Category::whereNull('parent_id')
                    ->where('status', true)
                    ->get();
            }

            // SEARCH FILTERING
            if ($queryTerm) {
                $productsQuery->where(fn($q) => $q->where('name', 'like', "%{$queryTerm}%")
                    ->orWhere('description', 'like', "%{$queryTerm}%"));
            }

            if ($stockStatus) {
                $productsQuery->whereHas('stocks', fn($q) => $q->where('qty', $stockStatus === 'in_stock' ? '>=' : '=', $stockStatus === 'in_stock' ? 1 : 0));
            }

            // FEATURED FILTERING
            if ($featuredStatus === 'featured') {
                $productsQuery->where('is_featured', true);
            }

            // SORTING
            $productsQuery->orderBy('sort_order'); // Default sorting
            if (in_array($sort, ['price_asc', 'price_desc'])) {
                $products = $productsQuery->paginate(12)->appends($request->all());

                $products->getCollection()->transform(function ($product) {
                    $variant = $product->default_variant_id ?? $product->stocks()->first()->id ?? null;
                    $product->calculated_price = $variant ? $product->cartPrice($variant) : 0;
                    return $product;
                });

                $products->setCollection($products->getCollection()->sortBy('calculated_price', SORT_REGULAR, $sort === 'price_desc')->values());
            } else {
                $products = $productsQuery->paginate(12)->appends($request->all());
            }

            // SEO AND METADATA
            $title = $selectedCategory ? $selectedCategory->meta_title : config('app.name') . ' - ' . ($queryTerm ?? 'Products');
            $description = $selectedCategory ? $selectedCategory->meta_description : 'Browse our wide selection of quality products.';
            $heading = $selectedCategory ? ($queryTerm ? "Search Results for $queryTerm in {$selectedCategory->name}" : $selectedCategory->name) : ($queryTerm ? "Search Results for $queryTerm" : 'All Products');

            // Breadcrumbs
            $breadcrumbs = [['url' => route('home'), 'label' => 'Home']];
            if ($selectedCategory) {
                if ($parentCategory) {
                    $breadcrumbs[] = ['url' => route('sf.products.list', ['category' => $parentCategory->slug]), 'label' => $parentCategory->name];
                }
                $breadcrumbs[] = ['url' => route('sf.products.list', ['category' => $selectedCategory->slug]), 'label' => $selectedCategory->name];
            } else {
                $breadcrumbs[] = ['url' => route('sf.products.list'), 'label' => 'All Products'];
            }

            // SEO Configuration
            SEOTools::setTitle("$heading | Buy Online")
                ->setDescription("$description Best prices and quality guaranteed. Shop now for " . ($queryTerm ?? 'natural products') . ' online.')
                ->setCanonical(url()->current())
                ->opengraph()
                ->setUrl(url()->current())
                ->addProperty('type', 'website')
                ->setTitle("$heading | Buy Online - " . config('app.name'))
                ->setDescription("$description Best prices and quality guaranteed.")
                ->setTitle("$heading | Buy Online - " . config('app.name'))
                ->setDescription("$description Best prices and quality guaranteed.");

            return view('frontend.products-list', compact(
                'products',
                'categories',
                'sort',
                'categoryTree',
                'title',
                'description',
                'breadcrumbs',
                'heading',
                'queryTerm',
                'selectedCategory',
                'parentCategory'
            ));
        } catch (ModelNotFoundException $e) {
            // Handle case where category or parent category is not found
            Log::error("Category not found for slug: {$categorySlug}", ['exception' => $e]);
            return redirect()->route('sf.products.list')->with('error', 'Category not found.');
        } catch (\Exception $e) {
            // Handle other unexpected errors
            Log::error('Error in search method: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('sf.products.list')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }


    public function suggestions(Request $request)
    {
        // dd($request->all());
        $query = $request->input('query');

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->where('is_active', true)
            ->select('id', 'name', 'slug')
            ->limit(5)
            ->get();

        $view = view('frontend.partials.search-product', compact('products'))->render();

        return response()->json([
            'products' => $view
        ]);
    }
}
