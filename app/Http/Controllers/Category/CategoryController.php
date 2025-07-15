<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        $locale = app()->getLocale(); // 'ar' أو 'en'

        $categories = Category::select(
            'id',
            "name_category_{$locale} as name",
            'image_category as image',
            'created_at'
        )->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ]);
    }

    public function getCategoryById($id)
    {
        $locale = app()->getLocale(); // 'ar' أو 'en'

        $category = Category::select(
            'id',
            "name_category_{$locale} as name",
            'image_category as image',
            'created_at'
        )->find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => $category
        ]);
    }


    public function addCategory(Request $request)
    {
        $admin = null;
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $locale = app()->getLocale(); // 'ar' or 'en'
        $nameField = "name_category_{$locale}";

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        try {
            $category = Category::create([
                $nameField => $validated['name'],
                'image_category' => $validated['image'] ?? null,
            ]);

            return response()->json([
                'message' => 'Category added successfully',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function deleteCategory($id)
    {
        $admin = null;
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
            'id' => $id
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $admin = null;
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale(); // 'ar' or 'en'
        $nameField = "name_category_{$locale}";

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->$nameField = $validated['name'];
        if (isset($validated['image'])) {
            $category->image_category = $validated['image'];
        }
        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

}
