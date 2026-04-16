<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SyncMapping;
use App\Models\SyncLog;
use Illuminate\Support\Facades\Log;

class SyncProcessor
{
    protected OlseraOpenClient $olsera;
    protected WooCommerceClient $woo;

    public function __construct(OlseraOpenClient $olsera, WooCommerceClient $woo)
    {
        $this->olsera = $olsera;
        $this->woo = $woo;
    }

    /**
     * Stage 1: Fetch from Olsera and store in local database.
     */
    public function ingest(int $page = 1, int $limit = 15): array
    {
        try {
            $response = $this->olsera->getProducts([
                'page' => $page,
                'limit' => $limit
            ]);
            
            // Olsera Open API response structure has a 'data' key with array of products
            $products = $response['data'] ?? [];
            $resultCount = count($products);
            
            if (empty($products)) {
                $this->logSync('olsera_ingest', 'success', "No products found on page {$page}.");
                return ['count' => 0, 'processed' => 0, 'has_more' => false];
            }

            $count = 0;
            foreach ($products as $olseraProduct) {
                // ... (rest of the logic remains the same)
                // 1. Update or Create Parent Product
                $localProduct = Product::updateOrCreate(
                    ['olsera_id' => $olseraProduct['id']],
                    [
                        'sku' => $olseraProduct['sku'] ?? null,
                        'brand' => !empty($olseraProduct['klasifikasi']) ? ucwords(strtolower(trim($olseraProduct['klasifikasi']))) : null,
                        'name' => $olseraProduct['name'],
                        'barcode' => $olseraProduct['barcode'] ?? null,
                        'price' => $olseraProduct['market_price'] ?? $olseraProduct['sell_price'] ?? 0,
                        'buy_price' => $olseraProduct['buy_price'] ?? 0,
                        'weight' => $olseraProduct['weight'] ?? 0,
                        'stock' => $olseraProduct['stock_quantity'] ?? $olseraProduct['stock'] ?? 0,
                        'description' => $olseraProduct['description'] ?? '',
                        'images' => array_values(array_unique(array_filter([
                            $olseraProduct['photo_md'] ?? null,
                            $olseraProduct['photo'] ?? null,
                            $olseraProduct['photo_lg'] ?? null
                        ]))),
                        'has_variants' => (bool) ($olseraProduct['has_variant'] ?? false),
                        'allow_decimal' => (bool) ($olseraProduct['allow_decimal'] ?? false),
                        'is_synced' => false,
                    ]
                );

                // 2. Process Variants if available
                if (!empty($olseraProduct['variants'])) {
                    foreach ($olseraProduct['variants'] as $olseraVariant) {
                        $localProduct->variants()->updateOrCreate(
                            ['olsera_id' => $olseraVariant['id']],
                            [
                                'sku' => $olseraVariant['sku'] ?? null,
                                'name' => $olseraVariant['name'],
                                'price' => $olseraVariant['market_price'] ?? 0,
                                'sell_price' => $olseraVariant['sell_price'] ?? null,
                                'buy_price' => $olseraVariant['buy_price'] ?? 0,
                                'weight' => $olseraVariant['vweight'] ?? 0,
                                'stock' => $olseraVariant['stock_qty'] ?? 0,
                                'barcode' => $olseraVariant['variant_barcode'] ?? null,
                                'images' => array_values(array_unique(array_filter([
                                    $olseraVariant['photo_md'] ?? null,
                                    $olseraVariant['photo'] ?? null
                                ]))),
                            ]
                        );
                    }
                }
                
                if ($localProduct->wasRecentlyCreated || $localProduct->wasChanged()) {
                    $count++;
                }
            }

            $this->logSync('olsera_ingest', 'success', "Successfully ingested {$resultCount} items (page {$page}) from Olsera Open API.");
            
            return [
                'count' => $resultCount,
                'processed' => $count,
                'has_more' => $resultCount > 0
            ];

        } catch (\Exception $e) {
            $this->logSync('olsera_ingest', 'error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Stage 1b: Fetch only stock from Olsera.
     * Note: Open API v1 uses the same product endpoint for stock updates if no dedicated endpoint exists.
     */
    public function ingestInventory(): array
    {
        try {
            $response = $this->olsera->getProducts();
            $products = $response['data'] ?? [];

            if (empty($products)) {
                return ['count' => 0];
            }

            $count = 0;
            foreach ($products as $item) {
                // Update parent stock
                $product = Product::where('olsera_id', $item['id'])->first();

                if ($product) {
                    $newStock = $item['stock_quantity'] ?? $item['stock'] ?? 0;
                    if ($product->stock != $newStock) {
                        $product->update([
                            'stock' => $newStock,
                            'is_synced' => false
                        ]);
                        $count++;
                    }

                    // Update variant stock
                    if (!empty($item['variants'])) {
                        foreach ($item['variants'] as $variantItem) {
                            $variant = $product->variants()->where('olsera_id', $variantItem['id'])->first();
                            if ($variant) {
                                $newVarStock = $variantItem['stock_qty'] ?? 0;
                                if ($variant->stock != $newVarStock) {
                                    $variant->update(['stock' => $newVarStock]);
                                    // Optionally mark parent as unsynced if ANY variant changed
                                    $product->update(['is_synced' => false]);
                                }
                            }
                        }
                    }
                }
            }

            $this->logSync('olsera_inventory', 'success', "Updated stock for {$count} products and their variants from Olsera Open API.");
            return ['count' => $count];
        } catch (\Exception $e) {
            $this->logSync('olsera_inventory', 'error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Stage 2: Sync local products to WooCommerce.
     */
    public function dispatch(int $limit = 20): array
    {
        try {
            $pendingProducts = Product::where('is_synced', false)->limit($limit)->get();
            $totalUnsynced = Product::where('is_synced', false)->count();
            
            if ($pendingProducts->isEmpty()) {
                $this->logSync('woo_dispatch', 'success', 'No pending products to sync to WooCommerce.');
                return ['synced' => 0, 'failed' => 0, 'remaining' => 0];
            }

            $synced = 0;
            $failed = 0;

            foreach ($pendingProducts as $product) {
                try {
                    $isVariable = $product->has_variants;
                    $attributeSlug = $this->determineAttributeSlug($product);
                    
                    // 1. Map data
                    $wooData = $this->mapProductToWoo($product, $isVariable, $attributeSlug);

                    // 2. Check for existing mapping
                    $mapping = SyncMapping::where('olsera_id', $product->olsera_id)->first();
                    $wooId = null;

                    if ($mapping) {
                        // Update existing
                        $response = $this->woo->updateProduct($mapping->woocommerce_id, $wooData);
                        if (!empty($response['id'])) {
                            $wooId = $response['id'];
                        }
                    } else {
                        // Try to find by SKU first to avoid duplicates
                        $existingWoo = $product->sku ? $this->woo->findProductBySku($product->sku) : null;
                        
                        if ($existingWoo) {
                            $response = $this->woo->updateProduct($existingWoo['id'], $wooData);
                            if (!empty($response['id'])) {
                                $wooId = $response['id'];
                                SyncMapping::create([
                                    'olsera_id' => $product->olsera_id,
                                    'woocommerce_id' => $wooId
                                ]);
                            }
                        } else {
                            // Create new
                            $response = $this->woo->createProduct($wooData);
                            if (!empty($response['id'])) {
                                $wooId = $response['id'];
                                SyncMapping::create([
                                    'olsera_id' => $product->olsera_id,
                                    'woocommerce_id' => $wooId
                                ]);
                            }
                        }
                    }

                    if ($wooId) {
                        // 3. Process Variations if applicable
                        if ($isVariable) {
                            $this->syncVariations($product, $wooId, $attributeSlug);
                        }
                        
                        $product->update(['is_synced' => true]);
                        $synced++;
                    } else {
                        $failed++;
                        Log::warning("Failed to sync product SKU: {$product->sku}");
                    }
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("Failed to dispatch product ID {$product->id}: " . $e->getMessage());
                    // Continue to next product
                }
            }

            $remaining = $totalUnsynced - $synced;
            $this->logSync('woo_dispatch', 'success', "Batch sync complete. Synced: {$synced}, Remaining: {$remaining}");
            
            return [
                'synced' => $synced,
                'failed' => $failed,
                'remaining' => $remaining > 0 ? $remaining : 0
            ];

        } catch (\Exception $e) {
            $this->logSync('woo_dispatch', 'error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Map internal product model to WooCommerce API format.
     */
    protected function mapProductToWoo(Product $product, bool $isVariable = false, string $attributeSlug = 'pa_size'): array
    {
        $categoryId = ($attributeSlug === 'pa_size') ? 23 : 80;

        $data = [
            'name' => $product->name,
            'type' => $isVariable ? 'variable' : 'simple',
            'regular_price' => (string) $product->price,
            'description' => $product->description,
            'sku' => $product->sku,
            'manage_stock' => !$isVariable,
            'categories' => [
                ['id' => $categoryId]
            ]
        ];

        if ($isVariable) {
            // Define the attributes for the variable product
            $variants = $product->variants;
            $options = $variants->pluck('name')->unique()->toArray();
            
            $data['attributes'] = [
                [
                    'id' => $attributeSlug == 'pa_size' ? 2 : 4,
                    'visible' => true,
                    'variation' => true,
                    'options' => array_values($options),
                ]
            ];
            
            unset($data['regular_price']);
            $data['stock_quantity'] = 0;
            $data['manage_stock'] = false;
        } else {
            $data['stock_quantity'] = $product->stock;
        }

        if (!empty($product->images) && is_array($product->images)) {
            $data['images'] = array_map(function($img) {
                return ['src' => is_array($img) ? ($img['url'] ?? $img['src'] ?? '') : $img];
            }, $product->images);
        }

        return $data;
    }

    /**
     * Map internal variant model to WooCommerce variation API format.
     */
    protected function mapVariantToWoo($variant, string $attributeSlug = 'pa_size'): array
    {
        $data = [
            'regular_price' => (string) ($variant->price ?: $variant->sell_price ?: 0),
            'sku' => $variant->sku,
            'manage_stock' => true,
            'stock_quantity' => $variant->stock,
            'attributes' => [
                [
                    'id' => $attributeSlug == 'pa_size' ? 2 : 4,
                    'option' => $variant->name,
                ]
            ],
        ];

        if($variant->sell_price < $variant->price){
            $data['sale_price'] = (string) $variant->sell_price;
        }
       
        return $data;
    }

    /**
     * Determine whether to use Size or Apparel Size.
     */
    protected function determineAttributeSlug(Product $product): string
    {
        $variants = $product->variants;
        if ($variants->isEmpty()) {
            return 'pa_size';
        }

        foreach ($variants as $variant) {
            if (is_numeric($variant->name) || preg_match('/^\d+(\.\d+)?$/', $variant->name)) {
                return 'pa_size';
            }
        }

        return 'pa_size-apparel';
    }

    /**
     * Helper to synchronize variations for a variable product.
     */
    protected function syncVariations(Product $product, int $wooParentId, string $attributeSlug = 'pa_size'): void
    {
        foreach ($product->variants as $variant) {
            $variantData = $this->mapVariantToWoo($variant, $attributeSlug);
            
            // Check mapping for variation
            $mapping = SyncMapping::where('olsera_id', $variant->olsera_id)->first();
            
            if ($mapping) {
                $this->woo->updateProductVariation($wooParentId, $mapping->woocommerce_id, $variantData);
            } else {
                $response = $this->woo->createProductVariation($wooParentId, $variantData);
                if (!empty($response['id'])) {
                    SyncMapping::create([
                        'olsera_id' => $variant->olsera_id,
                        'woocommerce_id' => $response['id']
                    ]);
                }
            }
        }
    }

    /**
     * Helper to log sync status.
     */
    protected function logSync(string $type, string $status, string $message, array $details = []): void
    {
        SyncLog::create([
            'type' => $type,
            'status' => $status,
            'message' => $message,
            'details' => $details,
        ]);
        
        if ($status === 'error') {
            Log::error("[{$type}] Sync Error: {$message}");
        } else {
            Log::info("[{$type}] Sync: {$message}");
        }
    }
}
