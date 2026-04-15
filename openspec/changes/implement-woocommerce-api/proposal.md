## Why

The application needs to synchronize product data from Olsera to WooCommerce. While the local database and UI for browsing these products have been implemented, the actual integration with the WooCommerce REST API is missing. implementing a dedicated WooCommerce API client will enable the system to push local product and variant data to WooCommerce, ensuring inventory and product details are consistent across both platforms.

## What Changes

- Implementation of a `WooCommerceClient` service to interact with the WooCommerce REST API v3.
- Support for managing "Variable" products and their individual "Variations".
- Capabilities for creating, updating, retrieving, and listing products and variations.

## Capabilities

### New Capabilities
- `woocommerce-api-integration`: A robust client for interacting with WooCommerce REST API v3, focused on product and variant management.

### Modified Capabilities
- `product-sync-olsera-woocommerce`: Updating the sync requirements to include the ability to push data to WooCommerce, not just fetching from Olsera.

## Impact

- **New Service**: `app/Services/WooCommerceClient.php`
- **Environment Variables**: New requirements for `WOOCOMMERCE_STORE_URL`, `WOOCOMMERCE_CONSUMER_KEY`, and `WOOCOMMERCE_CONSUMER_SECRET`.
- **Existing Logic**: `App\Services\SyncProcessor` will be updated to utilize this new client for the synchronization logic.
