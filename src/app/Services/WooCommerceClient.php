<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * Fetch all product brands from WooCommerce.
     */
    public function getBrands(array $params = []): array
    {
        return $this->request('GET', '/products/brands', $params);
    }

    /**
     * Generic request handler for WooCommerce API.
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->storeUrl . '/wp-json/wc/v3' . $endpoint;

        $response = Http::timeout(60)
            ->connectTimeout(30)
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36')
            ->withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->retry(3, 100, function ($exception, $request) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException 
                    || ($exception instanceof \Illuminate\Http\Client\ResponseException && in_array($exception->getCode(), [429, 503]));
            })
            ->send($method, $url, $method === 'GET' ? $data : $data);

        $jsonResponse = $response->json();

        // Log raw response for debugging with request context
        $this->logRawResponse($method, $endpoint, $data, $jsonResponse);

        if ($response->failed()) {
            $excerpt = substr($response->body(), 0, 500);
            Log::error("WooCommerce API Error ({$endpoint}) [Status: {$response->status()}]: {$excerpt}");
            return $jsonResponse ?: [];
        }

        return $jsonResponse;
    }

    /**
     * Save raw JSON response to storage/woocommerce.
     */
    protected function logRawResponse(string $method, string $endpoint, array $requestData, array $responseData): void
    {
        $endpointName = str_replace(['/', '\\'], '_', trim($endpoint, '/'));
        $timestamp = now()->format('Y-m-d_H-i-s') . '_' . substr(explode(' ', microtime())[0], 2, 6);
        $filename = "woocommerce/{$endpointName}_{$method}_{$timestamp}.json";
        
        $logData = [
            'method' => $method,
            'endpoint' => $endpoint,
            'request' => $requestData,
            'response' => $responseData,
        ];

        Storage::disk('local')->put($filename, json_encode($logData, JSON_PRETTY_PRINT));
    }
}
