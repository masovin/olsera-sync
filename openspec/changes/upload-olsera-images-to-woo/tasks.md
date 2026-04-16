## 1. Ingestion Refactoring

- [x] 1.1 Update `SyncProcessor::ingest()` to standardise `images` as an array of URLs for parent products by wrapping `photo_md` and including `photo`.
- [x] 1.2 Update `SyncProcessor::ingest()` variant loop to store variation photos as an array in the local database.

## 2. WooCommerce Mapping Refactoring

- [x] 2.1 Update `SyncProcessor::mapProductToWoo()` to transform the `images` array into the WooCommerce API format `[{"src": "url"}]`.
- [x] 2.2 Update `SyncProcessor::mapVariantToWoo()` to transform the variation's first image into the WooCommerce API format `{"src": "url"}` for the `image` field.
- [x] 2.3 Ensure null or empty URLs are filtered out before sending payloads to WooCommerce.

## 3. Verification

- [x] 3.1 Execute `php artisan olsera:sync-products --ingest` and verify the `images` column in the `products` table contains an array of URLs.
- [x] 3.2 Execute `php artisan olsera:sync-products --dispatch` and monitor the sync logs for any image-related API errors.
- [x] 3.3 Verify in the WooCommerce admin dashboard that products now have correctly assigned featured images and gallery images.
