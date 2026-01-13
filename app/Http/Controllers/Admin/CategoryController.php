<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('can:manage-categories');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        // Build category tree array with indentation
        $categoryTree = $this->buildCategoryTreeArray();

        return view('admin.products.category.index', compact('categories', 'categoryTree'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {

            $category = Category::create($request->all());

            if($category) {
                Helper::updateImage($category, 'category_thumbnail');
            }

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the category'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category->category_thumb = Helper::imageDataForFilePond($category, 'category_thumbnail');
        $categoryTree = $this->buildCategoryTreeArray();
        $viewpage = view('admin.products.category.edit', compact('category', 'categoryTree'))->render();
        return response()->json([
            'status' => true,
            'data' => ['data' => $viewpage]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {

            $category->update($request->all());

            if($category) {
                Helper::updateImage($category, 'category_thumbnail');
            }

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the category'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->products()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category has products.',
                ], 400);
            }

            if (Category::where('parent_id', $category->id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category has child categories.',
                ], 400);
            }

            $category->delete();

            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Category delete failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }


    public function status(Category $category)
    {
        try {
            if($category->status){
                $category->status = false;
            }else{
                $category->status = true;
            }
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Upddated successfully.'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Category publish failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    public function buildCategoryTreeArray()
    {
        $categoryTree = [];

        // Get all parent categories first
        $parentCategories = Category::whereNull('parent_id')->get();

        // Recursive function to build category tree
        function buildCategoryTree($category, $level = 1) {
            $tree = [];

            // Add current category with dashes based on level
            $tree[] = [
                'id' => $category->id,
                'name' => str_repeat('¦––', $level) . ' ' . $category->name
            ];

            // Get all children of current category
            $children = Category::where('parent_id', $category->id)->get();

            // Recursively process each child
            foreach ($children as $child) {
                $tree = array_merge($tree, buildCategoryTree($child, $level + 1));
            }

            return $tree;
        }

        // Build complete category tree
        $categoryTree = [];
        foreach ($parentCategories as $parent) {
            $categoryTree = array_merge($categoryTree, buildCategoryTree($parent));
        }

        return $categoryTree;
    }


    public function categoryStatus(Request $request,Category $category)
    {
        try {
            if($category->status){
                $category->status = false;
            }else{
                $category->status = true;
            }
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully.'
            ], 200);

        } catch (\Throwable $th) {
            Log::error('Category publish failed: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    public function categoryFeatured(Request $request,Category $category)
    {
        try {
            if($category->featured){
                $category->featured = false;
            }else{
                $category->featured = true;
            }
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully.'
            ], 200);

        } catch (\Throwable $th) {
            Log::error('Category featured failed: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }


     /**
     * Bulk delete categories
     */
    public function destroyBulk(Request $request)
    {
        $ids = $request->input('ids', []);

        // Fetch categories with product count
        $categories = Category::withCount('products')->whereIn('id', $ids)->get();

        // Get all child categories related to the selected ones
        $childCategoryCounts = Category::whereIn('parent_id', $ids)
            ->selectRaw('parent_id, COUNT(*) as children_count')
            ->groupBy('parent_id')
            ->pluck('children_count', 'parent_id');

        $undeletable = [];
        $deletableIds = [];

        foreach ($categories as $category) {
            $hasProducts = $category->products_count > 0;
            $hasChildren = $childCategoryCounts->get($category->id, 0) > 0;

            if ($hasProducts || $hasChildren) {
                $reasons = [];
                if ($hasProducts) $reasons[] = 'has products';
                if ($hasChildren) $reasons[] = 'has subcategories';

                $undeletable[] = "{$category->name} (" . implode(', ', $reasons) . ")";
            } else {
                $deletableIds[] = $category->id;
            }
        }

        try {
            if (!empty($deletableIds)) {
                Category::whereIn('id', $deletableIds)->forceDelete();
            }

            if (!empty($undeletable)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Some categories could not be deleted: ' . implode('; ', $undeletable),
                    'deleted_count' => count($deletableIds),
                    'skipped_count' => count($undeletable)
                ], 400);
            }

            return response()->json([
                'status' => true,
                'message' => count($deletableIds) . ' category(ies) deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Category delete failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

}
