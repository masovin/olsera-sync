<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooCommerceClient
{
    protected string $storeUrl;
    protected string $consumerKey;
    protected string $consumerSecret;

    public function __construct()
    {
        $this->storeUrl = rtrim(config('services.woocommerce.store_url'), '/');
        $this->consumerKey = config('services.woocommerce.consumer_key');
        $this->consumerSecret = config('services.woocommerce.consumer_secret');
    }

    /**
     * Create a new product in WooCommerce.
     */
    public function createProduct(array $data): array
    {
        return $this->request('POST', '/products', $data);
    }

    /**
     * Update an existing product in WooCommerce.
     */
    public function updateProduct(int $id, array $data): array
    {
        return $this->request('PUT', "/products/{$id}", $data);
    }

    /**
     * Find a product by SKU.
     */
    public function findProductBySku(string $sku): ?array
    {
        $response = $this->request('GET', '/products', ['sku' => $sku, 'consumer_key' => $this->consumerKey, 'consumer_secret' => $this->consumerSecret]);
        return (!empty($response) && is_array($response)) ? $response[0] : null;
    }

    /**
     * Update stock level for a product.
     */
    public function updateStock(int $id, int $quantity): array
    {
        return $this->updateProduct($id, [
            'manage_stock' => true,
            'stock_quantity' => $quantity,
        ]);
    }

    /**
     * List all variations for a specific product.
     */
    public function getProductVariations(int $productId): array
    {
        return $this->request('GET', "/products/{$productId}/variations");
    }

    /**
     * Create a new product variation.
     */
    public function createProductVariation(int $productId, array $data): array
    {
        return $this->request('POST', "/products/{$productId}/variations", $data);
    }

    /**
     * Update an existing product variation.
     */
    public function updateProductVariation(int $productId, int $variationId, array $data): array
    {
        return $this->request('PUT', "/products/{$productId}/variations/{$variationId}", $data);
    }

    /**
     * Batch update variations for a specific product.
     */
    public function batchUpdateVariations(int $productId, array $data): array
    {
        return $this->request('POST', "/products/{$productId}/variations/batch", $data);
    }

    /**
     * Generic request handler for WooCommerce API.
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->storeUrl . '/wp-json/wc/v3' . $endpoint;

        // Force consumer_key and consumer_secret into query params for higher compatibility
        $query = $method === 'GET' ? $data : [];
        $query['consumer_key'] = $this->consumerKey;
        $query['consumer_secret'] = $this->consumerSecret;

        $response = Http::timeout(60)
            ->connectTimeout(30)
            ->withUserAgent('WooCommerce API Client')
            ->retry(3, 100, function ($exception, $request) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException 
                    || ($exception instanceof \Illuminate\Http\Client\ResponseException && in_array($exception->getCode(), [429, 503]));
            })
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->send($method, $url, [
                'query' => $query,
                'json' => $method !== 'GET' ? $data : [],
            ]);

        if ($response->failed()) {
            $excerpt = substr($response->body(), 0, 500);
            Log::error("WooCommerce API Error ({$endpoint}) [Status: {$response->status()}]: {$excerpt}");
            return [];
        }

        return $response->json();
    }
}
