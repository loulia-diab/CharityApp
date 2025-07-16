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
        $user = null;
        $admin = null;
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
        } elseif (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();
        } else {
            return response()->json(['message' => ' Unauthorized'], 401);
        }
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
        $user = null;
        $admin = null;
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
        } elseif (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();
        } else {
            return response()->json(['message' => ' Unauthorized'], 401);
        }
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
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $nameField = "name_category_{$locale}";

        $validated = $request->validate([
            $nameField => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $categoryData = [
                $nameField => $validated[$nameField],
                'image_category' => null,
            ];

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('category_images', 'public');
                $categoryData['image_category'] = $path;
            }

            $category = Category::create($categoryData);

            return response()->json([
                'message' => $locale === 'ar' ? 'تمت إضافة التصنيف بنجاح' : 'Category added successfully',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إضافة التصنيف' : 'Error adding category',
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

        $locale = app()->getLocale();
        $nameField = "name_category_{$locale}";

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->$nameField = $validated['name'];

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا موجودة
            if ($category->image_category && \Storage::disk('public')->exists($category->image_category)) {
                \Storage::disk('public')->delete($category->image_category);
            }

            // رفع الصورة الجديدة
            $path = $request->file('image')->store('category_images', 'public');
            $category->image_category = $path;
        }

        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
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



}
