<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::firstOrCreate(
                ['email' => $data['customer']['email']],
                ['name' => $data['customer']['name']]
            );

            /*
             * Sort product IDs before acquiring row locks.
             * This ensures concurrent multi-product orders acquire
             * locks in the same deterministic order.
             */
            $items = collect($data['items'])
                ->sortBy('product_id')
                ->values()
                ->all();

            $products = [];

            foreach ($items as $item) {
                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $products[$product->id] = $product;
            }

            $subtotal = 0;
            $tax = 0;
            $orderItems = [];

            /*
             * All required product rows are now locked.
             * Validate stock before making any deductions.
             */
            foreach ($items as $item) {
                $product = $products[$item['product_id']];
                $quantity = $item['quantity'];

                if ($product->stock_on_hand < $quantity) {
                    throw new RuntimeException(
                        "Insufficient stock for product: {$product->name}"
                    );
                }
            }

            /*
             * Stock has been validated for every product.
             * Calculate totals and deduct stock.
             */
            foreach ($items as $item) {
                $product = $products[$item['product_id']];
                $quantity = $item['quantity'];

                $unitPrice = (float) $product->price;
                $taxPercentage = (float) $product->tax_percentage;

                $itemSubtotal = round(
                    $unitPrice * $quantity,
                    2
                );

                $itemTax = round(
                    $itemSubtotal * $taxPercentage / 100,
                    2
                );

                $itemTotal = round(
                    $itemSubtotal + $itemTax,
                    2
                );

                $subtotal += $itemSubtotal;
                $tax += $itemTax;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'tax_percentage' => $product->tax_percentage,
                    'subtotal' => $itemSubtotal,
                    'tax_amount' => $itemTax,
                    'total' => $itemTotal,
                ];

                $product->decrement(
                    'stock_on_hand',
                    $quantity
                );
            }

            $subtotal = round($subtotal, 2);
            $tax = round($tax, 2);
            $grandTotal = round($subtotal + $tax, 2);

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $grandTotal,
            ]);

            $order->orderItems()->createMany($orderItems);

            SendOrderConfirmation::dispatch($order->id);

            return $order->load([
                'customer',
                'orderItems.product',
            ]);
        });
    }
}
