## Context

The current integration supports data-only synchronization for products and variations. While Olsera provides image links (primarily `photo_md`), the synchronization process does not consistently ensure these images are uploaded to the WooCommerce media library. The local database storage for images is also prone to type inconsistencies (string vs array).

## Goals / Non-Goals

**Goals:**
- Automatically synchronize product images from Olsera to WooCommerce.
- Ensure all images are properly imported into the WordPress media library.
- Support multiple images for parent products and specific images for variations.
- Modernize the image handling logic in `SyncProcessor.php`.

**Non-Goals:**
- Implementing a separate media management UI.
- Handling image optimization or resizing (WooCommerce/WordPress will handle this on import).
- Deleting images from WooCommerce if they are deleted from Olsera (out of scope for now).

## Decisions

### 1. Ingestion Data Standardisation
In `SyncProcessor::ingest()`, we will modify the image assignment to always be an array. If only `photo_md` is present, it will be stored as `[$photo_md]`. If additional fields like `photo` are found, they will be appended to the array, ensuring unique entries.

### 2. WooCommerce Mapping Enhancement
Update `mapProductToWoo` to transform the array of strings into an array of objects:
```php
'images' => [
    ['src' => 'https://example.com/image1.jpg'],
    ['src' => 'https://example.com/image2.jpg']
]
```
This specific structure (using the `src` key) tells WooCommerce to sideload the image from the URL.

### 3. Variation Image Handling
Update `mapVariantToWoo` to extract the first available image from the variation's `images` field and set it as the `image` (singular) for the variation.

### 4. Robust URL Handling
Add a helper method or logic to ensure URLs are valid and potentially filter out known placeholder URLs or empty strings.

## Risks / Trade-offs

- **Memory/Timeout Issues**: Large image sets might cause the WooCommerce API to take longer to respond. We have already increased timeout in `WooCommerceClient`, but we may need to monitor this.
- **Duplicates**: If sync runs multiple times, WooCommerce *should* recognize existing images by URL, but it sometimes creates duplicates if the URL changes (e.g., has temporary tokens). We will rely on WooCommerce's default behavior for now.
