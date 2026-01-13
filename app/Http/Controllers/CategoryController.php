<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Get all subcategory IDs for a given parent category ID
     *
     * @param int $parentId
     * @return array
     */
    public function getSubcategoryIds($categoryId, &$visited = [])
    {
        $subcategoryIds = [];

        if (in_array($categoryId, $visited)) {
            return [];
        }

        $visited[] = $categoryId;

        $category = Category::find($categoryId);

        if (!$category) {
            return [];
        }

        if ($category->parent_id) {
            return $this->getSubcategoryIds($category->parent_id, $visited);
        }

        $childCategories = Category::where('parent_id', $categoryId)->pluck('id')->toArray();

        $subcategoryIds = array_merge($subcategoryIds, $childCategories);

        foreach ($childCategories as $childId) {
            $subcategoryIds = array_merge($subcategoryIds, $this->getSubcategoryIds($childId, $visited));
        }

        return !empty($subcategoryIds) ? array_unique($subcategoryIds) : [$categoryId];
    }

    /**
     * Get the category tree for a given category ID
     *
     * @param int $categoryId
     * @return array
     */
    public function getCategoryTree($categoryId)
    {
        $allCategories = Category::all();
        return $this->buildTree($allCategories, $categoryId);
    }

    /**
     * Build the category tree for a given parent ID
     *
     * @param array $categories
     * @param int $parentId
     * @return array
     */
    private function buildTree($categories, $parentId)
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildTree($categories, $category->id);

                $branch[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'children' => $children
                ];
            }
        }

        return $branch;
    }


}