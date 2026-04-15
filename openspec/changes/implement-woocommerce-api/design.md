## Context

The system currently interacts with Olsera to fetch products and variations. To complete the synchronization, we need a way to push this data to WooCommerce. While the `SyncProcessor` already exists, it lacks a robust implementation for WooCommerce communication, which has resulted in `cURL error 28` timeouts and inconsistent state mapping.

## Goals / Non-Goals

**Goals:**
- Implement a dedicated `WooCommerceClient` using Laravel's `Http` client.
- Support core REST operations for `products` and `products/variations`.
- Implement robust error handling (e.g., handling rate limits and timeouts).
- Support SKU-based lookup for existing products on WooCommerce.
- Use Guzzle configuration (like `timeout` and `connect_timeout`) to prevent the observed `cURL error 28`.

**Non-Goals:**
- Implementing other WooCommerce entities (Orders, Coupons, Customers).
- Complex mapping logic (this belongs in `SyncProcessor`).
- Automated image handling or resizing.

## Decisions

- **Client Selection**: Use the native Laravel `Http` client rather than a third-party WooCommerce wrapper to maintain full control over timeout settings and middleware (like logging).
- **Authentication**: Use standard WooCommerce REST API Consumer Key and Consumer Secret via `Basic Auth` over HTTPS.
- **Timeout Management**: Configure a significant `timeout` (e.g., 30s-60s) to account for slow WooCommerce API responses on some hosting environments.
- **Variable Product Strategy**: When syncing a product with variants, the parent must be created as type `variable`. Variations are indexed under that parent ID.

## Risks / Trade-offs

- **Performance**: Large batches of variations can result in many API calls. We will use batch endpoints (`/batch`) where possible to optimize performance.
- **Data Integrity**: If a product ID is lost locally, matching by SKU is the only fallback. This assumes SKUs are unique across all platforms.
- **Timeout Risk**: WooCommerce API performance is highly dependent on the target store's server. Even with longer timeouts, synchronization can fail if the server is under heavy load.
