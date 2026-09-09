<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmation;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created_successfully(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'price' => 100.00,
            'tax_percentage' => 18.00,
            'stock_on_hand' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Order created successfully.')
            ->assertJsonPath('data.subtotal', '200.00')
            ->assertJsonPath('data.tax', '36.00')
            ->assertJsonPath('data.grand_total', '236.00');

        $this->assertDatabaseHas('customers', [
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('orders', [
            'subtotal' => 200.00,
            'tax' => 36.00,
            'grand_total' => 236.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100.00,
            'tax_percentage' => 18.00,
            'subtotal' => 200.00,
            'tax_amount' => 36.00,
            'total' => 236.00,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 8,
        ]);

        Queue::assertPushed(
            SendOrderConfirmation::class,
            function ($job) {
                return $job->orderId === 1;
            }
        );
    }

    public function test_order_fails_when_stock_is_insufficient(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'price' => 100.00,
            'tax_percentage' => 18.00,
            'stock_on_hand' => 2,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => "Insufficient stock for product: {$product->name}",
            ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 2,
        ]);

        Queue::assertNothingPushed();
    }

    public function test_existing_customer_is_reused_by_email(): void
    {
        Queue::fake();

        $customer = Customer::factory()->create([
            'name' => 'Original Name',
            'email' => 'existing@example.com',
        ]);

        $product = Product::factory()->create([
            'price' => 50.00,
            'tax_percentage' => 5.00,
            'stock_on_hand' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Updated Name',
                'email' => 'existing@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertCreated();

        $this->assertDatabaseCount('customers', 1);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Original Name',
            'email' => 'existing@example.com',
        ]);
    }

    public function test_order_validation_rejects_invalid_data(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => '',
                'email' => 'invalid-email',
            ],
            'items' => [],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'customer.name',
            'customer.email',
            'items',
        ]);
    }
}
