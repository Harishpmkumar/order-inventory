<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SupportingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_order_history_by_email(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $product = Product::factory()->create();

        $order = Order::create([
            'customer_id' => $customer->id,
            'subtotal' => 100.00,
            'tax' => 18.00,
            'grand_total' => 118.00,
        ]);

        $order->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 100.00,
            'tax_percentage' => 18.00,
            'subtotal' => 100.00,
            'tax_amount' => 18.00,
            'total' => 118.00,
        ]);

        $response = $this->getJson(
            '/api/customers/orders?email=john@example.com'
        );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $order->id)
            ->assertJsonPath(
                'data.0.customer.email',
                'john@example.com'
            )
            ->assertJsonPath(
                'data.0.order_items.0.product_id',
                $product->id
            );
    }

    public function test_customer_order_history_returns_not_found_for_unknown_email(): void
    {
        $response = $this->getJson(
            '/api/customers/orders?email=unknown@example.com'
        );

        $response
            ->assertNotFound()
            ->assertJson([
                'message' => 'Customer not found.',
                'data' => [],
            ]);
    }

    public function test_customer_order_history_validates_email(): void
    {
        $response = $this->getJson(
            '/api/customers/orders?email=invalid-email'
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_low_stock_products_use_configured_default_threshold(): void
    {
        config([
            'inventory.low_stock_threshold' => 5,
        ]);

        Product::factory()->create([
            'stock_on_hand' => 2,
        ]);

        Product::factory()->create([
            'stock_on_hand' => 5,
        ]);

        Product::factory()->create([
            'stock_on_hand' => 10,
        ]);

        $response = $this->getJson('/api/products/low-stock');

        $response
            ->assertOk()
            ->assertJsonPath('threshold', 5)
            ->assertJsonCount(2, 'data');
    }

    public function test_low_stock_products_accept_custom_threshold(): void
    {
        Product::factory()->create([
            'stock_on_hand' => 3,
        ]);

        Product::factory()->create([
            'stock_on_hand' => 7,
        ]);

        Product::factory()->create([
            'stock_on_hand' => 10,
        ]);

        $response = $this->getJson(
            '/api/products/low-stock?threshold=7'
        );

        $response
            ->assertOk()
            ->assertJsonPath('threshold', 7)
            ->assertJsonCount(2, 'data');
    }

    public function test_low_stock_products_reject_negative_threshold(): void
    {
        $response = $this->getJson(
            '/api/products/low-stock?threshold=-1'
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Threshold must be zero or greater.',
            ]);
    }

    public function test_order_can_contain_multiple_products(): void
    {
        Queue::fake();

        $productOne = Product::factory()->create([
            'price' => 100.00,
            'tax_percentage' => 10.00,
            'stock_on_hand' => 10,
        ]);

        $productTwo = Product::factory()->create([
            'price' => 200.00,
            'tax_percentage' => 20.00,
            'stock_on_hand' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Multiple Product Customer',
                'email' => 'multiple@example.com',
            ],
            'items' => [
                [
                    'product_id' => $productTwo->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $productOne->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.subtotal', '700.00')
            ->assertJsonPath('data.tax', '110.00')
            ->assertJsonPath('data.grand_total', '810.00');

        $this->assertDatabaseHas('products', [
            'id' => $productOne->id,
            'stock_on_hand' => 7,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $productTwo->id,
            'stock_on_hand' => 8,
        ]);
    }

    public function test_failed_multi_product_order_does_not_deduct_any_stock(): void
    {
        Queue::fake();

        $productOne = Product::factory()->create([
            'price' => 100.00,
            'tax_percentage' => 10.00,
            'stock_on_hand' => 10,
        ]);

        $productTwo = Product::factory()->create([
            'price' => 200.00,
            'tax_percentage' => 20.00,
            'stock_on_hand' => 1,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Rollback Customer',
                'email' => 'rollback@example.com',
            ],
            'items' => [
                [
                    'product_id' => $productOne->id,
                    'quantity' => 3,
                ],
                [
                    'product_id' => $productTwo->id,
                    'quantity' => 5,
                ],
            ],
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('products', [
            'id' => $productOne->id,
            'stock_on_hand' => 10,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $productTwo->id,
            'stock_on_hand' => 1,
        ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        Queue::assertNothingPushed();
    }
}
