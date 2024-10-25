<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseController
{
    // Create a new product
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($request->all());

        return $this->sendResponse($product, 'Product created successfully.');
    }

    // Get all products
    public function index(): JsonResponse
    {
        $products = Product::all();
        return $this->sendResponse($products, 'Products retrieved successfully.');
    }

    // Get a single product
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->sendError('Product not found', ['Product not found.'], 404);
        }
        return $this->sendResponse($product, 'Product retrieved successfully.');
    }

    // Update a product
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->sendError('Product not found', ['Product not found.'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($request->all());
        return $this->sendResponse($product, 'Product updated successfully.');
    }

    // Delete a product
    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->sendError('Product not found', ['Product not found.'], 404);
        }

        $product->delete();
        return $this->sendResponse(null, 'Product deleted successfully.');
    }
}