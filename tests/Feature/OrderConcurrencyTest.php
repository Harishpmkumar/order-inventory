<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class OrderConcurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'order_inventory',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);

        $this->ensureMysqlSchema();
    }

    public function test_only_one_concurrent_order_can_purchase_the_last_unit(): void
    {
        $emails = [
            'concurrent-one@example.com',
            'concurrent-two@example.com',
        ];

        $this->cleanupConcurrentTestData();

        $product = Product::factory()->create([
            'price' => 100.00,
            'tax_percentage' => 18.00,
            'stock_on_hand' => 1,
        ]);

        try {
            $worker = base_path(
                'tests/Support/ConcurrentOrderWorker.php'
            );

            $environment = [
                'APP_ENV' => 'testing',
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => '127.0.0.1',
                'DB_PORT' => '3306',
                'DB_DATABASE' => 'order_inventory',
                'DB_USERNAME' => 'root',
                'DB_PASSWORD' => '',
                'QUEUE_CONNECTION' => 'sync',
            ];

            $processOne = new Process([
                PHP_BINARY,
                $worker,
                (string) $product->id,
                $emails[0],
            ]);

            $processTwo = new Process([
                PHP_BINARY,
                $worker,
                (string) $product->id,
                $emails[1],
            ]);

            $processOne->setEnv($environment);
            $processTwo->setEnv($environment);

            $processOne->start();
            $processTwo->start();

            $processOne->wait();
            $processTwo->wait();

            $outputOne = trim($processOne->getOutput());
            $outputTwo = trim($processTwo->getOutput());

            $errorOne = trim($processOne->getErrorOutput());
            $errorTwo = trim($processTwo->getErrorOutput());

            $resultOne = json_decode($outputOne, true);
            $resultTwo = json_decode($outputTwo, true);

            if (!is_array($resultOne) || !is_array($resultTwo)) {
                $this->fail(
                    "Concurrent worker did not return valid JSON.\n\n" .
                        "Worker 1 stdout:\n{$outputOne}\n\n" .
                        "Worker 1 stderr:\n{$errorOne}\n\n" .
                        "Worker 2 stdout:\n{$outputTwo}\n\n" .
                        "Worker 2 stderr:\n{$errorTwo}"
                );
            }

            $results = [$resultOne, $resultTwo];

            $successes = array_filter(
                $results,
                fn(array $result) => ($result['status'] ?? null) === 'success'
            );

            $failures = array_filter(
                $results,
                fn(array $result) => ($result['status'] ?? null) === 'failed'
            );

            $this->assertCount(
                1,
                $successes,
                'Expected exactly one concurrent order to succeed. ' .
                    json_encode($results, JSON_PRETTY_PRINT)
            );

            $this->assertCount(
                1,
                $failures,
                'Expected exactly one concurrent order to fail. ' .
                    json_encode($results, JSON_PRETTY_PRINT)
            );

            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'stock_on_hand' => 0,
            ]);

            $orders = Order::query()
                ->whereHas('customer', function ($query) use ($emails) {
                    $query->whereIn('email', $emails);
                })
                ->get();

            $this->assertCount(1, $orders);

            $order = $orders->first();

            $this->assertNotNull($order);

            $this->assertDatabaseHas('order_items', [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        } finally {
            $this->cleanupConcurrentTestData($product->id);
        }
    }

    private function ensureMysqlSchema(): void
    {
        Artisan::call('migrate', [
            '--database' => 'mysql',
            '--force' => true,
        ]);
    }

    private function cleanupConcurrentTestData(?int $productId = null): void
    {
        $emails = [
            'concurrent-one@example.com',
            'concurrent-two@example.com',
        ];

        $customerIds = Customer::query()
            ->whereIn('email', $emails)
            ->pluck('id');

        if ($customerIds->isNotEmpty()) {
            $orderIds = Order::query()
                ->whereIn('customer_id', $customerIds)
                ->pluck('id');

            if ($orderIds->isNotEmpty()) {
                OrderItem::query()
                    ->whereIn('order_id', $orderIds)
                    ->delete();

                Order::query()
                    ->whereIn('id', $orderIds)
                    ->delete();
            }

            Customer::query()
                ->whereIn('id', $customerIds)
                ->delete();
        }

        if ($productId !== null) {
            Product::query()
                ->whereKey($productId)
                ->delete();
        }
    }
}
