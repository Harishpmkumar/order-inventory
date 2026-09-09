<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        $customer = Customer::query()
            ->where('email', $request->string('email'))
            ->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found.',
                'data' => [],
            ], 404);
        }

        $orders = $customer->orders()
            ->with('orderItems.product')
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }
}
