<?php

use App\Services\OrderService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$productId = (int) ($argv[1] ?? 0);
$email = $argv[2] ?? '';

try {
    $orderService = $app->make(OrderService::class);

    $order = $orderService->createOrder([
        'customer' => [
            'name' => 'Concurrent Customer',
            'email' => $email,
        ],
        'items' => [
            [
                'product_id' => $productId,
                'quantity' => 1,
            ],
        ],
    ]);

    echo json_encode([
        'status' => 'success',
        'order_id' => $order->id,
    ]);

    exit(0);
} catch (Throwable $e) {
    Log::warning('Concurrent order worker failed.', [
        'email' => $email,
        'message' => $e->getMessage(),
    ]);

    echo json_encode([
        'status' => 'failed',
        'message' => $e->getMessage(),
    ]);

    exit(1);
}
