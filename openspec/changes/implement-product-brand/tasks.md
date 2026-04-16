## 1. Database & Model Foundation

- [x] 1.1 Create `Brand` model in `src/app/Models/Brand.php` with fields: `woocommerce_id`, `name`, `slug`.
- [x] 1.2 Create and run migration for `brands` table.

## 2. WooCommerce Client Integration

- [x] 2.1 Add `getBrands()` method to `WooCommerceClient.php` to fetch taxonomy terms from the `/products/brands` endpoint.

## 3. Brand Synchronization

- [x] 3.1 Create `app/Console/Commands/OlseraSyncBrands.php` to fetch brands from WooCommerce and populate the local `brands` table.
- [x] 3.2 Verify the command performs an `updateOrCreate` to avoid duplicate brand entries.

## 4. Product Sync Enhancement

- [x] 4.1 Update `SyncProcessor::mapProductToWoo()` to look up the WooCommerce Brand ID by matching the product's `brand` string with the `name` in the local `brands` table.
- [x] 4.2 Update the WooCommerce product payload to include the resolved brand ID in the `brands` array.

## 5. Verification

- [x] 5.1 Execute `php artisan olsera:sync-brands` and verify the `brands` table is populated correctly.
- [x] 5.2 Execute `php artisan olsera:sync-products` and confirm in WooCommerce that products are correctly assigned to their respective brand terms.
