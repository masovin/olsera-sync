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
        $response = $this->request('GET', '/products', ['sku' => $sku]);
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
     * Generic request handler for WooCommerce API.
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->storeUrl . '/wp-json/wc/v3' . $endpoint;

        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->withHeaders(['Accept' => 'application/json'])
            ->send($method, $url, [
                'query' => $method === 'GET' ? $data : [],
                'json' => $method !== 'GET' ? $data : [],
            ]);

        if ($response->failed()) {
            Log::error("WooCommerce API Error ({$endpoint}): " . $response->body());
            return [];
        }

        return $response->json();
    }
}
