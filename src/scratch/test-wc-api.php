<?php

use App\Services\WooCommerceClient;
use Illuminate\Support\Facades\Http;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$client = app(WooCommerceClient::class);

echo "Testing WooCommerce Variable Product Hub with CASED/SPACED Slugs...\n\n";

/**
 * Helper to Test Product with Dynamic Attribute Slug
 */
function testCasedSlugs($client, $name, $attributeSlug, $options, $variantName) {
    echo "--- Testing: $name ($attributeSlug) ---\n";
    $categoryId = ($attributeSlug === 'Size') ? 23 : 80;
    
    $data = [
        'name' => 'Antigravity Cased Test: ' . $name . ' ' . time(),
        'type' => 'variable',
        'categories' => [['id' => $categoryId]],
        'attributes' => [
            [
                'name' => $attributeSlug,
                'visible' => true,
                'variation' => true,
                'options' => $options
            ]
        ]
    ];

    try {
        $product = $client->createProduct($data);
        if (isset($product['id'])) {
            $parentId = $product['id'];
            echo "√ Created Parent Product (ID: $parentId)\n";
            
            $vData = [
                'regular_price' => '1000000',
                'sku' => 'CASE-TEST-' . str_replace(' ', '-', $attributeSlug) . '-' . time(),
                'attributes' => [
                    [
                        'name' => $attributeSlug,
                        'option' => $variantName
                    ]
                ],
                'manage_stock' => true,
                'stock_quantity' => 10
            ];
            $v = $client->createProductVariation($parentId, $vData);
            echo "√ Created Variation '$variantName' using slug '$attributeSlug' (ID: " . ($v['id'] ?? 'FAILED') . ")\n";
            return $parentId;
        } else {
            echo "X Failed to create product.\n";
            print_r($product);
        }
    } catch (\Exception $e) {
        echo "X Error: " . $e->getMessage() . "\n";
    }
    return null;
}

// 1. Test Cased Footwear -> Size (23)
testCasedSlugs($client, 'Cased Footwear', 'Size', ['42', '43'], '42');

echo "\n";

// 2. Test Cased Apparel -> Apparel Size (80)
testCasedSlugs($client, 'Cased Apparel', 'Apparel Size', ['M', 'L', 'XL'], 'XL');

echo "\nVerification Complete.\n";
