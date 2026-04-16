## Why

Currently, product brands are treated as simple text strings derived from the Olsera `klasifikasi` field. This approach is limited because WooCommerce typically handles brands through a dedicated taxonomy (e.g., via the "WooCommerce Brands" plugin or custom attributes). 

To ensure products are correctly categorized by brand in WooCommerce, we need a way to:
1.  Import existing brands from WooCommerce into our local database.
2.  Maintain a mapping between Olsera classifications and WooCommerce brand IDs.
3.  Automatically assign the correct brand ID when syncing products.

## What Changes

- **Brand Model & Migration**: Create a new `Brand` model and a `brands` table to store WooCommerce brand names and their corresponding internal IDs.
- **WooCommerce API Expansion**: Add methods to `WooCommerceClient` to fetch available brands from the WooCommerce store.
- **Brand Synchronization Command**: Implement a new Artisan command `olsera:sync-brands` to populate the local brands table.
- **Product Mapping Update**: Update the product synchronization logic to resolve the string-based brand name into a WooCommerce brand ID before dispatching to the API.

## Capabilities

### New Capabilities
- `brand-management`: Provides the ability to fetch, store, and manage WooCommerce brands within the local system for mapping purposes.
- `brand-assignment`: Automatically resolves and assigns the correct WooCommerce brand taxonomy ID during product synchronization.

### Modified Capabilities
- `product-sync`: Updated to support structured brand assignment instead of simple string-based tagging.

## Impact

- `Brand.php`: New model.
- `brands` table: New database migration.
- `WooCommerceClient.php`: New `getBrands()` method.
- `SyncProcessor.php`: Enhanced mapping logic in `mapProductToWoo()`.
- `olsera:sync-brands`: New Artisan command.
