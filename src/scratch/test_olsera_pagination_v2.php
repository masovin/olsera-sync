<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OlseraOpenClient;

$client = app(OlseraOpenClient::class);

echo "Testing page 2 WITHOUT limit\n";
try {
    $response = $client->getProducts(['page' => 2]);
    $products = $response['data'] ?? [];
    echo "  Count: " . count($products) . "\n";
    if (!empty($products)) {
        echo "  First ID: " . $products[0]['id'] . "\n";
    }
} catch (\Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "-------------------\n";

echo "Testing page 1 for comparison\n";
try {
    $response = $client->getProducts(['page' => 1]);
    $products = $response['data'] ?? [];
    echo "  Count: " . count($products) . "\n";
    if (!empty($products)) {
        echo "  First ID: " . $products[0]['id'] . "\n";
    }
} catch (\Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "-------------------\n";
