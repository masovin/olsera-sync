## Why

The current synchronization process uploads images for each product variation to WooCommerce. This consumes significant storage on the WooCommerce server and increases sync time. The user has requested to remove this functionality to optimize resources and speed up the synchronization process.

## What Changes

This change will remove the image mapping logic for product variations in the `SyncProcessor`. Variation images will no longer be included in the payloads sent to the WooCommerce API during `POST` or `PUT` operations.

## Capabilities

### New Capabilities
- None

### Modified Capabilities
- `product-sync`: Removed variation image synchronization from the data mapping layer.

## Impact

- `SyncProcessor.php`: Modification of `mapVariantToWoo` to exclude the `image` key.
- Performance: Faster synchronization for variable products.
- Storage: Reduced media library growth on the WooCommerce site.
