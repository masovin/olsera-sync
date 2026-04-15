## Context

The system currently interacts with the Olsera Open API v1 to synchronize product data. However, the data model only accounts for top-level products. Olsera supports multi-variant products (e.g., sizes for shoes), where each variant has its own unique SKU, price, and stock levels. This design addresses the gap by introducing a structural way to store and manage these variants.

## Goals / Non-Goals

**Goals:**
- Store Olsera product variants in a dedicated database table.
- Maintain a proper relationship between parent products and their variations.
- Update synchronization logic to ingest nested variant data.

**Non-Goals:**
- Handling WooCommerce variation synchronization (this will be a separate change).
- Managing product categories or attributes beyond what's needed for variants.

## Decisions

### 1. Database Schema: Separate Table for Variants
**Decision**: Create a `product_variants` table instead of using a JSON column in the `products` table.
**Rationale**: 
- Better query performance for inventory management.
- Standardizes the structure for future integration with WooCommerce variations.
- Enables the use of Eloquent relationships for cleaner code.
**Alternatives Considered**: JSON column in `products`. Rejected because it complicates direct database queries for specific SKUs or stock levels.

### 2. Parent-Child Relationship
**Decision**: Use `product_id` (foreign key) to link `product_variants` to the `products` table. 
**Rationale**: Established database pattern that ensures data integrity.
**Cascade**: If a parent product is deleted, its variants should also be deleted.

### 3. Sync Logic: Nested Iteration
**Decision**: Update `SyncProcessor::ingest` to detect a non-empty `variants` array.
**Rationale**: Olsera's response includes all variants within the product object, allowing for efficient one-pass synchronization.
**Mapping**:
- Parent Product: `id` → `olsera_id`
- Variant: `id` → `olsera_id`, `product_id` → parent's `id`

## Risks / Trade-offs

- **[Risk] Duplicate Variants** → **Mitigation**: Unique index on `product_variants.olsera_id`.
- **[Risk] Stock Inconsistency** → **Mitigation**: When `has_variant` is true, the `SyncProcessor` should treat the sum of variant stocks (or specific variant stock) as the source of truth rather than the parent's aggregate `stock_qty` if they differ.
