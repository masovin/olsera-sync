## Context

Currently, the `SyncProcessor::mapVariantToWoo` method includes an `image` key in the variation data payload if the local product variant has images. This triggers an image upload/assignment in WooCommerce for every variant.

## Goals / Non-Goals

**Goals:**
- Remove the `image` key from the WooCommerce variation payload.
- Ensure only basic metadata (name, price, stock, attributes) is sent for variations.

**Non-Goals:**
- Removing images from parent products.
- Removing images from the local database.
- Modifying the Olsera ingestion logic.

## Decisions

- **Exclusion of 'image' Index**: We will remove the conditional block that adds the `image` key in `mapVariantToWoo`.
- **Minimal Perturbation**: The local data storage for variant images will remain intact in case the user decides to re-enable this feature later or use the images for another purpose.

## Risks / Trade-offs

- **Visual Consistency**: Variations in WooCommerce will no longer have distinct images unless manually assigned or if they inherit from the parent. Since this is an explicit request, this is an acceptable trade-off.
