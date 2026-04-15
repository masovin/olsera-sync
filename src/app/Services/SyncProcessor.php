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
    public function ingest(): array
    {
        try {
            $response = $this->olsera->getProducts();
            
            // Olsera Open API response structure has a 'data' key with array of products
            $products = $response['data'] ?? [];
            
            if (empty($products)) {
                $this->logSync('olsera_ingest', 'success', 'No products found to ingest.');
                return ['count' => 0];
            }

            $count = 0;
            foreach ($products as $olseraProduct) {
                // 1. Update or Create Parent Product
                $localProduct = Product::updateOrCreate(
                    ['olsera_id' => $olseraProduct['id']],
                    [
                        'sku' => $olseraProduct['sku'] ?? null,
                        'brand' => $olseraProduct['klasifikasi'] ?? null,
                        'name' => $olseraProduct['name'],
                        'barcode' => $olseraProduct['barcode'] ?? null,
                        'price' => $olseraProduct['selling_price'] ?? $olseraProduct['price'] ?? 0,
                        'buy_price' => $olseraProduct['buy_price'] ?? 0,
                        'weight' => $olseraProduct['weight'] ?? 0,
                        'stock' => $olseraProduct['stock_quantity'] ?? $olseraProduct['stock'] ?? 0,
                        'description' => $olseraProduct['description'] ?? '',
                        'images' => $olseraProduct['photo_md'] ?? [],
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
                                'price' => $olseraVariant['sell_price'] ?? 0,
                                'sell_price' => $olseraVariant['sell_price'] ?? null,
                                'buy_price' => $olseraVariant['buy_price'] ?? 0,
                                'weight' => $olseraVariant['vweight'] ?? 0,
                                'stock' => $olseraVariant['stock_qty'] ?? 0,
                                'barcode' => $olseraVariant['variant_barcode'] ?? null,
                                'images' => !empty($olseraVariant['photo_md']) ? [$olseraVariant['photo_md']] : null,
                            ]
                        );
                    }
                }
                
                if ($localProduct->wasRecentlyCreated || $localProduct->wasChanged()) {
                    $count++;
                }
            }

            $this->logSync('olsera_ingest', 'success', "Successfully ingested {$count} products (and their variants) from Olsera Open API.");
            return ['count' => $count];

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
    public function dispatch(): array
    {
        try {
            $pendingProducts = Product::where('is_synced', false)->get();
            
            if ($pendingProducts->isEmpty()) {
                $this->logSync('woo_dispatch', 'success', 'No pending products to sync to WooCommerce.');
                return ['synced' => 0, 'failed' => 0];
            }

            $synced = 0;
            $failed = 0;

            foreach ($pendingProducts as $product) {
                // 1. Map data
                $wooData = $this->mapProductToWoo($product);

                // 2. Check for existing mapping
                $mapping = SyncMapping::where('olsera_id', $product->olsera_id)->first();
                
                if ($mapping) {
                    // Update existing
                    $response = $this->woo->updateProduct($mapping->woocommerce_id, $wooData);
                } else {
                    // Try to find by SKU first to avoid duplicates
                    $existingWoo = $product->sku ? $this->woo->findProductBySku($product->sku) : null;
                    
                    if ($existingWoo) {
                        $response = $this->woo->updateProduct($existingWoo['id'], $wooData);
                        SyncMapping::create([
                            'olsera_id' => $product->olsera_id,
                            'woocommerce_id' => $existingWoo['id']
                        ]);
                    } else {
                        // Create new
                        $response = $this->woo->createProduct($wooData);
                        if (!empty($response['id'])) {
                            SyncMapping::create([
                                'olsera_id' => $product->olsera_id,
                                'woocommerce_id' => $response['id']
                            ]);
                        }
                    }
                }

                if (!empty($response) && isset($response['id'])) {
                    $product->update(['is_synced' => true]);
                    $synced++;
                } else {
                    $failed++;
                    Log::warning("Failed to sync product SKU: {$product->sku}");
                }
            }

            $this->logSync('woo_dispatch', 'success', "Sync complete. Synced: {$synced}, Failed: {$failed}");
            return ['synced' => $synced, 'failed' => $failed];

        } catch (\Exception $e) {
            $this->logSync('woo_dispatch', 'error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Map internal product model to WooCommerce API format.
     */
    protected function mapProductToWoo(Product $product): array
    {
        $data = [
            'name' => $product->name,
            'type' => 'simple',
            'regular_price' => (string) $product->price,
            'description' => $product->description,
            'sku' => $product->sku,
            'manage_stock' => true,
            'stock_quantity' => $product->stock,
        ];

        if (!empty($product->images) && is_array($product->images)) {
            $data['images'] = array_map(function($img) {
                return ['src' => is_array($img) ? ($img['url'] ?? $img['src'] ?? '') : $img];
            }, $product->images);
        }

        return $data;
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
