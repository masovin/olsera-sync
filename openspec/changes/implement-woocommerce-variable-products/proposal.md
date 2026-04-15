## Why

Currently, all products synced from Olsera to WooCommerce are created as "simple" products, even if they have multiple variants in Olsera (e.g., different sizes or colors). This results in a flattened and disorganized WooCommerce catalog that doesn't reflect the true structure of the product data. Implementing the "variable" product type in WooCommerce will allow the system to group variants under a single parent product, providing a much cleaner shopping experience for customers and accurately maintaining stock levels per variant.

## What Changes

- Update `SyncProcessor::mapProductToWoo` to set the product type to `variable` when the Olsera product has variants.
- Implement the creation of "Attributes" in the parent WooCommerce product based on the variant values from Olsera.
- Update the synchronization loop in `SyncProcessor` to:
    1. Create/Update the parent variable product.
    2. Retrieve and sync individual variations using the parent's WooCommerce ID.
- Link Olsera variants to WooCommerce variations using the `SyncMapping` system.

## Capabilities

### New Capabilities
- `woocommerce-variable-product-sync`: Ability to synchronize nested product variations from Olsera to WooCommerce using the `variable` product type.

### Modified Capabilities
- `product-sync-olsera-woocommerce`: Requirement change to handle hierarchical product structures instead of flat ones.

## Impact

- **`SyncProcessor::mapProductToWoo`**: Logic change to handle two mapping modes (simple vs variable).
- **`SyncProcessor::dispatch`**: Updated loop to handle the two-step variable product creation process (Parent -> Variations).
- **`SyncMapping`**: Expanded use to track variation-level mappings.
