<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/products
     * Response WAJIB menyertakan data category (eager loading)
     */
    public function index(): JsonResponse
    {
        $products = Product::with('category')->get();
        
        return response()->json($products, 200);
    }

    /**
     * Display the specified resource.
     * GET /api/products/{id}
     * Response WAJIB menyertakan data category (eager loading)
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with('category')->find($id);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        
        return response()->json($product, 200);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/products
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:product_categories,id',
        ]);
        
        $product = Product::create($validated);
        $product->load('category');
        
        return response()->json($product, 201);
    }

    /**
     * Update the specified resource in storage.
     * PATCH /api/products/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:product_categories,id',
        ]);
        
        $product->update($validated);
        $product->load('category');
        
        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/products/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
