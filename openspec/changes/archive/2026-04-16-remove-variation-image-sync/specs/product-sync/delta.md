## REMOVED Requirements

### Requirement: Exclude Variant Images
The synchronization process must not include image data when mapping Olsera products to WooCommerce variations.

#### Scenario: Update existing variation
- **WHEN** a variation is being updated/created in WooCommerce
- **THEN** the request payload must NOT contain an `image` key.
