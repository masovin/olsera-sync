## ADDED Requirements

### Requirement: Capture Multiple Image Sources
The system must be able to capture multiple image sources from Olsera, including `photo_md` and `photo` if available, and store them as an array in the local database.

#### Scenario: Product with photo_md only
- **WHEN** an Olsera product has a `photo_md` link but no `photo`
- **THEN** it should be stored in the local `products` table as `["full_url_from_photo_md"]`

#### Scenario: Product with multiple photos
- **WHEN** an Olsera product has both `photo_md` and `photo`
- **THEN** both should be stored in the `images` array (avoiding duplicates)

### Requirement: WooCommerce Image Sideloading
The system must send image URLs to WooCommerce in the correct schema (`{"images": [{"src": "..."}]}`) to trigger automatic sideloading into the WordPress media library.

#### Scenario: Syncing Parent Product
- **WHEN** a product is dispatched to WooCommerce
- **THEN** the `images` array from the local database should be mapped to the WooCommerce `images` payload.

#### Scenario: Syncing Variation Image
- **WHEN** a variation is dispatched to WooCommerce
- **THEN** its primary image should be mapped to the WooCommerce `image` (singular) payload.
