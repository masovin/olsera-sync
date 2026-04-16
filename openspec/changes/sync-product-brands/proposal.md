## Why

Product brand synchronization is currently missing or incomplete. Brands fetched from Olsera need to be mapped to the corresponding brand taxonomy in WooCommerce to ensure consistent product classification across both platforms.

## What Changes

- **Brand Synchronization**: A new Artisan command to fetch and store WooCommerce brands locally.
- **Product Mapping**: Enhancement of the product synchronization logic to resolve brand names to WooCommerce IDs.
- **API Reliability**: Fixed a critical payload delivery bug in the WooCommerce API client that was causing product updates (including brand assignments) to be ignored.

## Capabilities

### New Capabilities
- `product-brand-sync`: Automated fetching and local storage of WooCommerce product brands for mapping.

### Modified Capabilities
- `product-sync-olsera-woocommerce`: Updated to include brand ID resolution and fixed payload delivery for all sync activities.

## Impact

- `WooCommerceClient`: Updated generic request handling for correct JSON payload delivery.
- `SyncProcessor`: Refined brand mapping logic and payload structure for compatibility with WC brand plugins.
- New database table `brands` to store WooCommerce brand data.
