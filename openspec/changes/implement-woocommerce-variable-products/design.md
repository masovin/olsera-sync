## Context

The current `SyncProcessor` maps every Olsera product directly to a "simple" WooCommerce product. For products with variants, this is incorrect as it loses the relationship between variants. We need to transition to storing "variable" products in WooCommerce.

## Goals / Non-Goals

**Goals:**
- Dynamically identify products with variants during the sync process.
- Map Olsera variable products to WooCommerce `variable` type.
- Create/Update WooCommerce Attributes (specifically a generic "Option" attribute) based on Olsera variant names.
- Sync individual Olsera variants as WooCommerce Variations.
- Ensure stock levels and prices are correctly synchronized at the variation level.

**Non-Goals:**
- Creating multiple distinct attributes (e.g., Color AND Size) if the data from Olsera is combined into a single name string.
- Handling complex attribute taxonomies in WooCommerce.
- Refactoring the entire `SyncProcessor` architecture (only targeted changes).

## Decisions

- **Attribute Mapping**: We will dynamically select between two WooCommerce attribute slugs:
    - `Size`: Used for footwear (detected by numeric variant names like "42", "42.5").
    - `Apparel Size`: Used for apparel (detected by letter-based variant names like "S", "M", "L", "XL").
- **Category Mapping**:
    - Footwear (`Size`) -> WooCommerce Category ID `23`.
    - Apparel (`Apparel Size`) -> WooCommerce Category ID `80`.
- **Selection Priority**: The system will analyze the variant names first to determine the category. If a product contains any numeric variant name, it will be classified as footwear.
- **Two-Step Sync**:
    1. Update the parent product: Set `type: 'variable'` and define the `attributes` where `name` is `Size` or `Apparel Size` and `variation` is true.
    2. Loop through variants: For each, check if a variation mapping already exists. If not, create a new variation under the parent product.
- **Mapping Storage**: We will continue using `SyncMapping` but will now store mappings for variants as well.

## Risks / Trade-offs

- **API Request Volume**: Syncing a variable product with many variants requires multiple API calls (Parent + N Variants). While the client supports `batchUpdateVariations`, the initial creation might still be sequential or require careful batching.
- **SKU Collisions**: If a variant SKU matches a parent SKU elsewhere, it might cause issues. We assume SKUs are unique across all entities.
- **Existing Simple Products**: Products already synced as `simple` will need to be deleted or updated to `variable` on WooCommerce. Transitioning types via API is sometimes restricted; we will attempt to update the type, but may log a warning if it fails.
