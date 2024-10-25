<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MerchantController extends Controller
{
    /**
     * Menampilkan daftar semua merchant.
     */
    public function index()
    {
        $merchants = Merchant::all();
        return response()->json($merchants);
    }

    /**
     * Menampilkan detail merchant tertentu.
     */
    public function show($id)
    {
        $merchant = Merchant::find($id);

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        return response()->json($merchant);
    }

    /**
     * Menyimpan data merchant baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Mendapatkan ID pengguna yang sedang terautentikasi
        $userId = Auth::id();
    
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        // Menyimpan data merchant
        $merchant = Merchant::create([
            'user_id' => $userId, // Pastikan user_id dimasukkan di sini
            'merchant_name' => $request->merchant_name, // Pastikan ini sesuai
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    
        return response()->json(['message' => 'Merchant created successfully', 'merchant' => $merchant], 201);
    }

    /**
     * Memperbarui data merchant tertentu.
     */
    public function update(Request $request, $id)
    {
        $merchant = Merchant::find($id);

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'merchant_name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Perbarui data merchant
        $merchant->update($request->only(['merchant_name', 'address', 'latitude', 'longitude']));

        return response()->json(['message' => 'Merchant updated successfully', 'merchant' => $merchant]);
    }

    /**
     * Menghapus merchant tertentu.
     */
    public function destroy($id)
    {
        $merchant = Merchant::find($id);

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        $merchant->delete();
        return response()->json(['message' => 'Merchant deleted successfully']);
    }

    public function getNearbyMerchants(Request $request)
    {
        // Validasi input latitude dan longitude
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'sometimes|numeric|min:0', // radius dalam kilometer
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 3; // Default radius 10 km

        // Menghitung jarak menggunakan Haversine
        $merchants = Merchant::selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get();

        return response()->json($merchants);
    }
}