<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends BaseController
{
    // Menambahkan Customer baru
    public function store(Request $request): JsonResponse
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        // Buat Customer
        $customer = Customer::create($request->all());

        return $this->sendResponse($customer, 'Customer created successfully.');
    }

    // Mendapatkan semua Customers
    public function index(): JsonResponse
    {
        $customers = Customer::all();
        return $this->sendResponse($customers, 'Customers retrieved successfully.');
    }

    // Mendapatkan Customer berdasarkan ID
    public function show($id): JsonResponse
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->sendError('Customer not found', ['Customer not found.'], 404);
        }
        return $this->sendResponse($customer, 'Customer retrieved successfully.');
    }

    // Mengupdate Customer
    public function update(Request $request, $id): JsonResponse
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|exists:users,id',
            'points' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $customer = Customer::find($id);
        if (!$customer) {
            return $this->sendError('Customer not found', ['Customer not found.'], 404);
        }

        // Update Customer
        $customer->update($request->all());

        return $this->sendResponse($customer, 'Customer updated successfully.');
    }

    // Menghapus Customer
    public function destroy($id): JsonResponse
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->sendError('Customer not found', ['Customer not found.'], 404);
        }

        $customer->delete();
        return $this->sendResponse(null, 'Customer deleted successfully.');
    }
}
