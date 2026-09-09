<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class OrderController extends Controller
{
    public function store(
        StoreOrderRequest $request,
        OrderService $orderService
    ): JsonResponse {
        try {
            $order = $orderService->createOrder(
                $request->validated()
            );

            return response()->json([
                'message' => 'Order created successfully.',
                'data' => $order,
            ], 201);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
