<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->orderBy('name')
            ->get();

        $lowStockProducts = Product::query()
            ->where(
                'stock_on_hand',
                '<=',
                config('inventory.low_stock_threshold', 5)
            )
            ->orderBy('stock_on_hand')
            ->get();

        return view('billing.index', [
            'products' => $products,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    public function lookupCustomer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $customer = Customer::query()
            ->where('email', $validated['email'])
            ->first();

        return response()->json([
            'found' => $customer !== null,
            'data' => $customer,
        ]);
    }
}
