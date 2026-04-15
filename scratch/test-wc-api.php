<?php

use App\Services\WooCommerceClient;
use Illuminate\Support\Facades\Log;

require __DIR__.'/../src/vendor/autoload.php';
$app = require_once __DIR__.'/../src/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$client = app(WooCommerceClient::class);

echo "Testing WooCommerce API Connection...\n";

try {
    $products = $client->findProductBySku('DUNK-LOW-PANDA'); // Example SKU
    
    if ($products !== null) {
        echo "Success! Found product: " . $products['name'] . " (ID: " . $products['id'] . ")\n";
    } else {
        echo "Notice: Connection successful, but product with SKU 'DUNK-LOW-PANDA' not found.\n";
    }
    
    // Test listing products (first page)
    echo "Listing recent products...\n";
    $list = $client->request('GET', '/products', ['per_page' => 5]);
    echo "Total products fetched in list: " . count($list) . "\n";
    foreach ($list as $p) {
        echo "- " . $p['name'] . " (SKU: " . $p['sku'] . ")\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Test script failed: " . $e->getMessage());
}
