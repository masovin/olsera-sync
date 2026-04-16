<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OlseraOpenClient;

$client = app(OlseraOpenClient::class);

$testParams = [
    'page' => ['page' => 2, 'limit' => 15],
    'p' => ['p' => 2, 'limit' => 15],
    'page_no' => ['page_no' => 2, 'limit' => 15],
    'offset' => ['offset' => 15, 'limit' => 15],
];

foreach ($testParams as $type => $params) {
    echo "Testing type: $type\n";
    try {
        $response = $client->getProducts($params);
        $products = $response['data'] ?? [];
        echo "  Count: " . count($products) . "\n";
        if (!empty($products)) {
            echo "  First ID: " . $products[0]['id'] . "\n";
        }
    } catch (\Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
    echo "-------------------\n";
}
