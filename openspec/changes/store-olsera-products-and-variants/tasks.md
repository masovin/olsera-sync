## 1. Database & Models Implementation

- [x] 1.1 Create a new migration file for the `product_variants` table with fields: `product_id`, `olsera_id`, `sku`, `name`, `price`, `stock`, `barcode`, and `weight`.
- [x] 1.2 Generate the `App\Models\ProductVariant` model with appropriate `$fillable` and timestamps.
- [x] 1.3 Add the `variants()` relationship to the `App\Models\Product` model and update its `$fillable` to include variant-related metadata if necessary.

## 2. Sync Logic Enhancement

- [x] 2.1 Refactor `SyncProcessor::ingest()` to detect the `variants` array in the Olsera response.
- [x] 2.2 Implement nested loop in `ingest()` to create or update `ProductVariant` records, ensuring they are correctly linked to the parent product.
- [x] 2.3 Refactor `SyncProcessor::ingestInventory()` to update stock for variants similarly to standalone products.

## 3. Verification & Testing

- [x] 3.1 Execute the `olsera:sync-products` command and verify that a multi-variant product is correctly stored as one parent and multiple variants.
- [x] 3.2 Execute the `olsera:sync-inventory` command and verify that variant stock levels are updated.
- [x] 3.3 Inspect the `product_variants` table in the database to confirm accurate data mapping from the Olsera response.
