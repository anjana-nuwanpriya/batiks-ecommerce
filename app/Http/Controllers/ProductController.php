<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Wishlist;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Product List
     */
    // public function index()
    // {
    //     try {
    //         $title = config('app.name') . ' - Products';
    //         $description = 'Browse our wide selection of quality products. Find everything you need with our easy search and filter options.';

    //         // Breadcrumbs
    //         $breadcrumbs = [
    //             ['url' => route('home'), 'label' => 'Home'],
    //             ['url' => route('sf.products.list'), 'label' => 'All Products'],
    //         ];

    //         $heading = 'All Products';

    //         $queryTerm = null;

    //         $sort = "newest";
    //         $products = Product::where('is_active', true)->paginate(12);
    //         $categories = Category::where('parent_id', null)->where('status', true)->get();
    //         return view('frontend.products-list', compact('products', 'categories', 'sort', 'title', 'description', 'breadcrumbs', 'heading', 'queryTerm'));
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Error fetching products');
    //     }
    // }

    /**
     * Show the product.
     */
    public function show(Request $request, $slug)
    {
        // try {
            $product = Product::where('slug', $slug)->where('is_active', true)->first();
            if (!$product) {
                abort(404, 'Product not found');
            }

            $metaTitle = $product->meta_title ? $product->meta_title : $product->name;
            $title = $metaTitle .  ' | '.config('app.name');
            $description = $product->meta_description;

            SEOTools::setTitle($title);
            SEOTools::setDescription($description);
            SEOTools::setCanonical(url()->current());
            SEOTools::opengraph()->setUrl(url()->current());

            return view('frontend.product', compact('product'));
        // } catch (\Exception $e) {
        //     Log::error('Error fetching product: ' . $e->getMessage());
        //     abort(500, 'Error fetching product');
        // }
    }




    /**
     * Add to Wishlist
     */
    public function addToWishlist(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add to wishlist'
            ], 401);
        }

        try {
            $product = Product::where('slug', $request->slug)->first();
            $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $product->id)->first();

            if ($wishlist) {
                $wishlist->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from wishlist',
                    'count' => Auth::user()->wishlist->count()
                ], 200);
            }

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $wishlist = Wishlist::create([
                'user_id' => Auth::user()->id,
                'product_id' => $product->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist',
                'count' => Auth::user()->wishlist->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding to wishlist'
            ], 500);
        }
    }

    public function removeFromWishlist(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|max:255',
        ]);

        try {
            $product = Product::where('slug', $request->slug)->first();
            $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $product->id)->first();
            $wishlist->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'count' => Auth::user()->wishlist->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing from wishlist'
            ], 500);
        }
    }

    /**
     *
     */

    public function storeReview(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:255',
        ]);

        $productId = $request->input('product');

        try {
            $review = Review::create([
                'product_id' => $productId,
                'user_id' => Auth::user()->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error submitting review: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error submitting review'
            ], 500);
        }
    }

    /**
     * Get product variants for quote modal
     */
    public function getVariants(Product $product)
    {
        try {
            $variants = $product->stocks()->select('id', 'variant', 'selling_price', 'qty', 'sku')->get();

            return response()->json([
                'success' => true,
                'variants' => $variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->variant,
                        'price' => formatCurrency($variant->selling_price),
                        'price_raw' => $variant->selling_price,
                        'stock' => $variant->qty,
                        'sku' => $variant->sku,
                        'in_stock' => $variant->qty > 0
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching product variants: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching variants'
            ], 500);
        }
    }
}
