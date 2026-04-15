## Why

Currently, the system only stores top-level product data from the Olsera Open API. Many products in Olsera have variants (e.g., sizes, colors), which include their own pricing, stock levels, and SKUs. Without storing these variants, the synchronization is incomplete and cannot accurately mirror the merchant's inventory.

## What Changes

- **Database Update**: Update the `products` table with a `has_variants` flag and create a new `product_variants` table to store variation-specific data.
- **Model Introduction**: Create a `ProductVariant` Eloquent model to represent individual product variations.
- **Sync Logic Refinement**: Update `SyncProcessor` to detect nested `variants` in the API response and map them to the new `product_variants` table.
- **Data Integrity**: Ensure variants are correctly linked to their parent products and that stock updates reflect variant-level changes.

## Capabilities

### New Capabilities
- `product-variant-management`: Defines the schema and model relationships for handling product variants.
- `sync-variant-data`: Extends the synchronization logic to ingest nested variant information from the Olsera response.

### Modified Capabilities
- None

## Impact

- **Affected Code**: `App\Models\Product`, `App\Models\ProductVariant`, `App\Services\SyncProcessor`, `database/migrations/*_create_products_table.php` (update), new migration for variants.
- **APIs**: Olsera Open API v1 (utilizing the `variants` array in the product response).
- **Dependencies**: None.
