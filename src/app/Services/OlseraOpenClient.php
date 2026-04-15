<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OlseraOpenClient
{
    protected string $baseUrl;
    protected string $appId;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.olsera_open.base_url'), '/');
        $this->appId = config('services.olsera_open.app_id');
        $this->secretKey = config('services.olsera_open.secret_key');
    }

    /**
     * Generic request handler for Olsera Open API.
     */
    protected function request(string $method, string $endpoint, array $data = [], bool $authenticated = true): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        $pendingRequest = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        if ($authenticated) {
            $token = $this->getAccessToken();
            $pendingRequest = $pendingRequest->withToken($token);
        }

        $response = $pendingRequest->send($method, $url, [
            'query' => $method === 'GET' ? $data : [],
            'json' => $method !== 'GET' ? $data : [],
        ]);

        if ($response->status() === 401 && $authenticated) {
            // Clear cache and retry once
            Cache::forget('olsera_open_access_token');
            $token = $this->getAccessToken();
            if ($token) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->withToken($token)->send($method, $url, [
                    'query' => $method === 'GET' ? $data : [],
                    'json' => $method !== 'GET' ? $data : [],
                ]);
            }
        }

        $jsonResponse = $response->json();
        
        // Log raw response
        $this->logRawResponse($endpoint, $jsonResponse);

        if ($response->failed()) {
            Log::error("Olsera Open API Error [{$endpoint}]: " . $response->body());
            return $jsonResponse ?: [];
        }

        return $jsonResponse;
    }

    /**
     * Get access token from cache or fetch new one.
     */
    public function getAccessToken(): string
    {
        return Cache::remember('olsera_open_access_token', now()->addHours(2), function () {
            $response = $this->fetchToken();
            return $response['access_token'] ?? '';
        });
    }

    /**
     * Get full token response using secret key.
     */
    public function fetchToken(): array
    {
        $response = $this->request('POST', 'id/token', [
            'app_id' => $this->appId,
            'secret_key' => $this->secretKey,
            'grant_type' => 'secret_key',
        ], false);

        if (!empty($response['refresh_token'])) {
            Cache::put('olsera_open_refresh_token', $response['refresh_token']);
        }

        return $response;
    }

    /**
     * Refresh access token using refresh token.
     */
    public function refreshToken(): array
    {
        $refreshToken = Cache::get('olsera_open_refresh_token');

        if (!$refreshToken) {
            return $this->fetchToken();
        }

        $response = $this->request('POST', 'id/token', [
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ], false);

        if (!empty($response['access_token'])) {
            Cache::put('olsera_open_access_token', $response['access_token'], now()->addHours(2));
            if (!empty($response['refresh_token'])) {
                Cache::put('olsera_open_refresh_token', $response['refresh_token']);
            }
        }

        return $response;
    }

    /**
     * Fetch product list from Olsera Open API.
     */
    public function getProducts(array $params = []): array
    {
        return $this->request('GET', 'en/product', $params);
    }

    /**
     * Fetch detailed information for a specific product.
     */
    public function getProductDetail(int $id): array
    {
        return $this->request('GET', 'en/product/detail', [
            'id' => $id
        ]);
    }

    /**
     * Save raw JSON response to storage/olsera.
     */
    protected function logRawResponse(string $endpoint, array $data): void
    {
        $endpointName = str_replace(['/', '\\'], '_', trim($endpoint, '/'));
        $filename = 'olsera/' . $endpointName . '_' . now()->format('Y-m-d_H-i-s') . '.json';
        Storage::disk('local')->put($filename, json_encode($data, JSON_PRETTY_PRINT));
    }
}
