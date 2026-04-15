## 1. Product Mapping Logic

- [x] 1.1 Implement `SyncProcessor::determineAttributeSlug` helper to detect if variants belong to `Size` or `Apparel Size` based on their names.
- [x] 1.2 Update `SyncProcessor::mapProductToWoo` to rename attribute slugs to `Size` (footwear) and `Apparel Size` (apparel).
- [x] 1.3 Update `SyncProcessor::mapVariantToWoo` to use the renamed slugs.

## 2. Synchronization Loop Expansion

- [x] 2.1 Modify `SyncProcessor::dispatch` to detect the attribute slug before starting the hierarchical sync.
- [x] 2.2 Re-verify hierarchical sync with the new cased slug names.

## 3. Client Verification

- [x] 3.1 Update the test script `src/scratch/test-wc-api.php` to use the new cased slug names (`Size` and `Apparel Size`).

## 4. Verification

- [x] 4.1 Run a full sync for a single variable product of each category from Olsera and verify they use the cased WooCommerce attributes.
