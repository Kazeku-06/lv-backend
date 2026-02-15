<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/product-categories
     */
    public function index(): JsonResponse
    {
        $categories = ProductCategory::all();
        
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/product-categories
     */
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $category = ProductCategory::create($request->validated());
        
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     * GET /api/product-categories/{id}
     */
    public function show(string $id): JsonResponse
    {
        $category = ProductCategory::find($id);
        
        if (!$category) {
            return response()->json([
                'message' => 'Product category not found'
            ], 404);
        }
        
        return response()->json($category, 200);
    }

    /**
     * Update the specified resource in storage.
     * PATCH /api/product-categories/{id}
     */
    public function update(UpdateProductCategoryRequest $request, string $id): JsonResponse
    {
        $category = ProductCategory::find($id);
        
        if (!$category) {
            return response()->json([
                'message' => 'Product category not found'
            ], 404);
        }
        
        $category->update($request->validated());
        
        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/product-categories/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $category = ProductCategory::find($id);
        
        if (!$category) {
            return response()->json([
                'message' => 'Product category not found'
            ], 404);
        }
        
        $category->delete();
        
        return response()->json([
            'message' => 'Product category deleted successfully'
        ], 200);
    }
}
