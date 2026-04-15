## 1. Database & Configuration

- [x] 1.1 Add `olsera_open` credentials (`app_id`, `secret_key`, `base_url`) to `config/services.php`.
- [x] 1.2 Update the `products` table migration to include `barcode`, `buy_price`, `weight`, `is_variant`, and `allow_decimal`.
- [x] 1.3 Add placeholder environment variables to `.env.example`.

## 2. Core Client Infrastructure

- [x] 2.1 Create `App\Services\OlseraOpenClient` class with basic request handling.
- [x] 2.2 Implement raw response logging to `storage/olsera/`.

## 3. Authentication Implementation

- [x] 3.1 Implement `fetchToken` method to get `access_token` and `refresh_token`.
- [x] 3.2 Implement `refreshToken` method.
- [x] 3.3 Implement `getAccessToken` with caching logic and automatic refresh on expiry.

## 4. API Endpoints Implementation

- [x] 4.1 Implement `getProducts` to fetch the product list.
- [x] 4.2 Implement `getProductDetail` to fetch specific product data by ID.
- [x] 4.3 Implement middleware or internal retry logic for 401 Unauthorized errors.

## 5. SyncProcessor Logic Migration

- [x] 5.1 Update `SyncProcessor` constructor and properties to use `OlseraOpenClient`.
- [x] 5.2 Refactor `ingest()` to correctly parse Open API product data.
- [x] 5.3 Refactor `ingestInventory()` to use Open API data.

## 6. Dashboard & CLI Verification

- [x] 6.1 Test `olsera:sync-products` command with the new client.
- [x] 6.2 Test `olsera:sync-inventory` command with the new client.
- [x] 6.3 Verify the Livewire `SyncDashboard` correctly triggers sync and displays results.
- [x] 6.4 Verify raw responses are still logged to `storage/olsera/`.
