<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$woo = app(\App\Services\WooCommerceClient::class);

echo "Fetching products...\n";
$prods = $woo->request('GET', '/products', ['per_page' => 20]);

foreach ($prods as $p) {
    foreach ($p as $k => $v) {
        if (stripos($k, 'brand') !== false) {
            echo "ID: {$p['id']} | Key: {$k} | Value: " . json_encode($v) . "\n";
        }
    }
}

echo "Done.\n";
