<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItems;
use App\Models\MerchantProduct;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PurchaseController extends BaseController
{
    public function store(Request $request): JsonResponse
    {
        Log::info('Store method called', ['request_data' => $request->all()]);
    
        // Validasi input
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'merchant_id' => 'required|exists:merchants,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'used_coins' => 'sometimes|integer|min:0',
        ]);
    
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
    
        // Inisialisasi total amount
        $totalAmount = 0;
    
        // Buat Purchase
        $purchase = Purchase::create([
            'customer_id' => $request->customer_id,
            'merchant_id' => $request->merchant_id,
            'total_amount' => 0, // Sementara
            'used_coins' => $request->used_coins ?? 0,
            'qr_code' => null,
        ]);
    
        // Buat Purchase Items
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $productPrice = $product->price;
            $totalAmount += $productPrice * $item['quantity'];
    
            // Ambil stok dari tabel merchant_product
            $merchantProduct = MerchantProduct::where('merchant_id', $request->merchant_id)
                ->where('product_id', $item['product_id'])
                ->first();
    
            // Periksa dan kurangi stok
            if (!$merchantProduct || $merchantProduct->stock < $item['quantity']) {
                return $this->sendError('Insufficient stock for product ID: ' . $item['product_id'], [], 400);
            }
    
            // Kurangi stok produk
            $merchantProduct->stock -= $item['quantity'];
            $merchantProduct->save();
    
            $purchaseItems = PurchaseItems::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $productPrice,
            ]);
        }
    
        // Hitung total amount dengan diskon dari koin
        $usedCoins = $request->used_coins ?? 0;
        $finalAmount = max(0, $totalAmount - $usedCoins);
    
        // Generate QR Code dengan data yang lebih padat
        $qrData = json_encode([
            'customer_id' => $request->customer_id,
            'merchant_id' => $request->merchant_id,
            'purchase_id' => $purchase->id,
            'purchase_items_id' => $purchaseItems->id,
        ]);
        
        $qrCode = new QrCode($qrData);
        $qrCode->setSize(500);
    
        $writer = new PngWriter();
        
        // Simpan QR Code ke file
        $qrCodePath = 'qr_codes/purchase_' . $purchase->id . '.png';
        $writer->write($qrCode)->saveToFile(public_path($qrCodePath));
    
        // Update total amount dan QR Code di database
        $purchase->update([
            'total_amount' => $finalAmount,
            'qr_code' => $qrCodePath,
        ]);
    
        return $this->sendResponse($purchase, 'Purchase created successfully with QR Code.');
    }
    // Get all Purchases
    public function index(): JsonResponse
    {
        $purchases = Purchase::with(['customer', 'items.product'])->get();
        return $this->sendResponse($purchases, 'Purchases retrieved successfully.');
    }

    // Get a single Purchase
    public function show($id): JsonResponse
    {
        $purchase = Purchase::with(['customer', 'items.product'])->find($id);
        if (!$purchase) {
            return $this->sendError('Purchase not found', ['Purchase not found.'], 404);
        }
        return $this->sendResponse($purchase, 'Purchase retrieved successfully.');
    }

    // Delete a Purchase
    public function destroy($id): JsonResponse
    {
        $purchase = Purchase::find($id);
        if (!$purchase) {
            return $this->sendError('Purchase not found', ['Purchase not found.'], 404);
        }

        $purchase->delete();
        return $this->sendResponse(null, 'Purchase deleted successfully.');
    }
}
