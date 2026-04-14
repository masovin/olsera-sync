<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OlseraClient
{
    protected string $apiUrl;
    protected string $accessToken;
    protected string $storeId;

    public function __construct()
    {
        $this->apiUrl = config('services.olsera.api_url');
        $this->accessToken = config('services.olsera.access_token');
        $this->storeId = config('services.olsera.store_id');
    }

    /**
     * Fetch all products from Olsera.
     */
    public function getProducts(array $params = []): array
    {
        return $this->request('GET', '/products', $params);
    }

    /**
     * Fetch stock levels from Olsera.
     */
    public function getStock(array $params = []): array
    {
        return $this->request('GET', '/inventory', $params);
    }

    /**
     * Generic request handler with JSON logging.
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = rtrim($this->apiUrl, '/') . $endpoint;

        $response = Http::withHeaders([
            'X-Olsera-Access-Token' => $this->accessToken,
            'X-Olsera-Store-Id' => $this->storeId,
            'Accept' => 'application/json',
        ])->send($method, $url, [
            'query' => $method === 'GET' ? $data : [],
            'json' => $method !== 'GET' ? $data : [],
        ]);

        if ($response->failed()) {
            Log::error("Olsera API Error: " . $response->body());
            return [];
        }

        $jsonResponse = $response->json();

        // Save response to storage/olsera as per requirement
        $this->logRawResponse($endpoint, $jsonResponse);

        return $jsonResponse;
    }

    /**
     * Save raw JSON response to storage/olsera.
     */
    protected function logRawResponse(string $endpoint, array $data): void
    {
        $filename = 'olsera/' . str_replace('/', '_', trim($endpoint, '/')) . '_' . now()->format('Y-m-d_H-i-s') . '.json';
        Storage::disk('local')->put($filename, json_encode($data, JSON_PRETTY_PRINT));
    }
}
