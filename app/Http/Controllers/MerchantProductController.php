<?php
namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\MerchantProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MerchantProductController extends BaseController
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'product_id' => 'required|exists:product,id', // Perbaiki ini
            'stock' => 'required|integer|min:0',
        ]);
    
        // Debug: Log data yang akan disimpan
        Log::info('Request data:', $request->all());
    
        try {
            $merchantProduct = MerchantProduct::create($request->all());
            return $this->sendResponse($merchantProduct, 'Merchant product created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating Merchant Product:', ['error' => $e->getMessage()]);
            return $this->sendError('Error creating Merchant Product', [$e->getMessage()], 500);
        }
    }

    // Get all MerchantProducts
    public function index(): JsonResponse
    {
        $merchantProducts = MerchantProduct::with(['merchant', 'product'])->get();
        return $this->sendResponse($merchantProducts, 'Merchant products retrieved successfully.');
    }

    // Get a single MerchantProduct
    public function show($id): JsonResponse
    {
        $merchantProduct = MerchantProduct::with(['merchant', 'product'])->find($id);
        if (!$merchantProduct) {
            return $this->sendError('Merchant Product not found', ['Merchant Product not found.'], 404);
        }
        return $this->sendResponse($merchantProduct, 'Merchant product retrieved successfully.');
    }

    // Update a MerchantProduct
    public function update(Request $request, $id): JsonResponse
    {
    $merchantProduct = MerchantProduct::with(['merchant', 'product'])->find($id);
    if (!$merchantProduct) {
        return $this->sendError('Merchant Product not found', ['Merchant Product not found.'], 404);
    }

    $request->validate([
        'stock' => 'sometimes|required|integer|min:0',
    ]);

    $merchantProduct->update($request->all());

    $merchantProduct->load(['merchant', 'product']);

    return $this->sendResponse($merchantProduct, 'Merchant product updated successfully.');
    }

    // Delete a MerchantProduct
    public function destroy($id): JsonResponse
    {
        $merchantProduct = MerchantProduct::find($id);
        if (!$merchantProduct) {
            return $this->sendError('Merchant Product not found', ['Merchant Product not found.'], 404);
        }

        $merchantProduct->delete();
        return $this->sendResponse(null, 'Merchant product deleted successfully.');
    }
}