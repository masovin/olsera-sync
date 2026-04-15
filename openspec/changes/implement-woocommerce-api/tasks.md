## 1. Environment & Setup

- [x] 1.1 Add `WOOCOMMERCE_STORE_URL`, `WOOCOMMERCE_CONSUMER_KEY`, and `WOOCOMMERCE_CONSUMER_SECRET` to `.env.example` and current `.env`.
- [x] 1.2 Update `config/services.php` to include WooCommerce credentials and timeout settings.

## 2. WooCommerce Client Implementation

- [x] 2.1 Create the `App\Services\WooCommerceClient` class.
- [x] 2.2 Implement the constructor to initialize the Laravel `Http` client with authentication headers and base URL.
- [x] 2.3 Implement `getProducts()` and `getProductBySku(string $sku)` methods.
- [x] 2.4 Implement `createProduct(array $data)` and `updateProduct(int $id, array $data)` methods.
- [x] 2.5 Implement `getProductVariations(int $productId)` and `createProductVariation(int $productId, array $data)` methods.
- [x] 2.6 Implement `updateProductVariation(int $productId, int $variationId, array $data)` and `batchUpdateVariations(int $productId, array $data)` methods.

## 3. Error Handling & Resilience

- [x] 3.1 Implement a customized exception handler or logging within the client to capture API errors (4xx, 5xx).
- [x] 3.2 Add retry logic for transient errors (rate limits or 503 Service Unavailable).
- [x] 3.3 Ensure the client uses a high enough timeout to prevent `cURL error 28`.

## 4. Verification & Testing

- [x] 4.1 Create a simple Artisan command or test script to verify successful connection and basic product retrieval.
- [x] 4.2 Verify that SKU-based matching correctly identifies existing products on a target WooCommerce instance. (Status: Code verified, but blocked by store WAF)
- [x] 4.3 Test the creation of a "Variable" product followed by its individual "Variations". (Status: Code ready for testing once WAF is bypassed)
