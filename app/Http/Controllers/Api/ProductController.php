<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function lowStock(Request $request): JsonResponse
    {
        $threshold = $request->integer(
            'threshold',
            config('inventory.low_stock_threshold', 5)
        );

        if ($threshold < 0) {
            return response()->json([
                'message' => 'Threshold must be zero or greater.',
            ], 422);
        }

        $products = Product::query()
            ->where('stock_on_hand', '<=', $threshold)
            ->orderBy('stock_on_hand')
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'data' => $products,
        ]);
    }
}
