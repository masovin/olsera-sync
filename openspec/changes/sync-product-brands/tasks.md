## 1. Brand Ingestion Foundation

- [x] 1.1 Create `Brand` model and `brands` migration to store WooCommerce brand data (id, name, slug).
- [x] 1.2 Update `WooCommerceClient::getBrands()` to fetch terms from the `/products/brands` endpoint.
- [x] 1.3 Create `olsera:sync-brands` Artisan command to populate the local brands table.
- [x] 1.4 Implement pagination in `olsera:sync-brands` and `WooCommerceClient` to fetch all brands.

## 2. API Payload Reliability Fix

- [ ] 2.1 Refactor `WooCommerceClient::request()` to correctly send JSON payloads in the request body for `POST`, `PUT`, and `PATCH` requests.
- [ ] 2.2 Verify that `POST` requests now return a WooCommerce response that matches the request data (name, type, images).

## 3. Product-Brand Mapping Refinement

- [x] 3.1 Update `SyncProcessor::mapProductToWoo()` to resolve brand names to WooCommerce IDs from the local brands table.
- [x] 3.2 Ensure brand mapping is case-insensitive and robust using `LIKE` queries.
- [ ] 3.3 Verify that `brands` and `product_brand` IDs are correctly included in the product synchronization payload.

## 4. Verification

- [ ] 4.1 Reset a sample product's mapping and run `php artisan olsera:sync-products`.
- [ ] 4.2 Verify in the WooCommerce dashboard that the product is created with the correct brand, name, and images.
- [ ] 4.3 Run `php artisan olsera:sync-brands` and verify all available brands are imported.
