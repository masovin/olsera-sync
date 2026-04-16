## Why

Synchronizing product data from Olsera to WooCommerce is incomplete without product images. Users need a reliable way to have their Olsera product images (accessible via public links) automatically uploaded and assigned to the corresponding products in WooCommerce during the sync process.

This change ensures that visual consistency is maintained between the two platforms and eliminates the manual step of uploading images to WordPress.

## What Changes

- **Image Extraction**: Update the ingestion logic to capture all available image URLs from Olsera (not just the medium thumbnail).
- **WooCommerce Sideloading**: Configure the WooCommerce product mapping to provide image URLs in a format that triggers WooCommerce's internal image downloading and media library registration.
- **Variant Support**: Ensure that product variations also have their specific images uploaded and assigned correctly.

## Capabilities

### New Capabilities
- `image-sync`: Automatically uploads product images from Olsera source URLs to WooCommerce. It handles both parent products and individual variations, ensuring the WordPress media library is populated with the correct assets during synchronisation.

### Modified Capabilities
- `product-sync`: Updated requirements to include image asset synchronization as part of the standard product dispatch process.

## Impact

- `SyncProcessor.php`: Modification to the mapping logic for products and variations.
- `Product.php`: Ensure the `images` field is correctly utilized as an array of URLs or image objects.
- WooCommerce API: The sync will now include image payloads, which may increase sync time slightly due to asset downloading on the WooCommerce side.
