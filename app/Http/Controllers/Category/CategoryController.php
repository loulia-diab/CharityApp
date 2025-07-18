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
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $categories = Category::select(
            'id',
            "name_category_{$locale}",
            'main_category'
        )->get();
        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ]);
    }

    public function getCategoryById($id)
    {
        $locale = app()->getLocale(); // 'ar' أو 'en'
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $category = Category::select(
            'id',
            "name_category_{$locale}",
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
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name_category_ar' => 'required|string|max:255',
            'name_category_en' => 'required|string|max:255',
            'main_category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $categoryData = [
                'name_category_ar' => $validated['name_category_ar'],
                'name_category_en' => $validated['name_category_en'],
                'main_category' => $validated['main_category'],
                'image_category' => null,
            ];

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('category_images', 'public');
                $categoryData['image_category'] = $path;
            }

            $category = Category::create($categoryData);

            return response()->json([
                'message' => 'تمت إضافة التصنيف بنجاح',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إضافة التصنيف',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateCategory(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name_category_ar' => 'nullable|string|max:255',
            'name_category_en' => 'nullable|string|max:255',
            'main_category' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        if (isset($validated['name_category_ar'])) {
            $category->name_category_ar = $validated['name_category_ar'];
        }
        if (isset($validated['name_category_en'])) {
            $category->name_category_en = $validated['name_category_en'];
        }
        if (isset($validated['main_category'])) {
            $category->main_category = $validated['main_category'];
        }


        if ($request->hasFile('image')) {
            $newImage = $request->file('image');
            $newImageHash = md5_file($newImage->getRealPath());

            $currentImagePath = $category->image_category
                ? storage_path('app/public/' . $category->image_category)
                : null;

            $currentImageHash = ($currentImagePath && file_exists($currentImagePath))
                ? md5_file($currentImagePath)
                : null;

            if ($newImageHash !== $currentImageHash) {
                if ($category->image_category && \Storage::disk('public')->exists($category->image_category)) {
                    \Storage::disk('public')->delete($category->image_category);
                }

                $extension = $newImage->getClientOriginalExtension();
                $filename = "category_{$category->id}." . $extension;
                $path = $newImage->storeAs('category_images', $filename, 'public');
                $category->image_category = $path;
            }
        }

        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => [
                'name_category_ar' => $category->name_category_ar,
                'name_category_en' => $category->name_category_en,
                'main_category' => $category->main_category,
                'image' => $category->image_category,
            ]
        ]);
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

    public function getAllCategoriesByMainCategory($main_category)
    {
        $allowedMainCategories = ['Campaign', 'HumanCase', 'Sponsorship'];

        if (!in_array($main_category, $allowedMainCategories)) {
            return response()->json([
                'message' => 'Invalid main category',
            ], 422);
        }

        $locale = app()->getLocale();

        $categories = Category::select(
            'id',
            "name_category_{$locale} as name",
            'main_category'
        )
            ->where('main_category', $main_category)
            ->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ]);
    }


    public function getAllCategoriesForUser()
    {
        $locale = app()->getLocale(); // 'ar' أو 'en'
        $categories = Category::select(
            'id',
            "name_category_{$locale} as name",
            'main_category'

        )->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ]);
    }

    public function getCategoryByIdForUser($id)
    {
        $locale = app()->getLocale(); // 'ar' أو 'en'
        $category = Category::select(
            'id',
            "name_category_{$locale} as name",
            'main_category'
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
}
