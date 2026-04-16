## Context

The synchronization of product brands currently fails because:
1.  The brand fetch command lacked pagination, missing many WooCommerce brands.
2.  The API client was sending payloads in an incorrect format (`POST/PUT` data misaligned with the body structure), causing WooCommerce to ignore brand assignments and other product metadata.

## Goals / Non-Goals

**Goals:**
- Ensure all brands are fetched from WooCommerce and stored locally.
- Correctly map Olsera's classification strings to WooCommerce brand terminology IDs.
- Fix the API client payload delivery to ensure all product metadata (including brands and images) is persisted in WooCommerce.

**Non-Goals:**
- Creating new brands in WooCommerce automatically (must be managed in WooCommerce dashboard).
- Syncing brand metadata beyond ID and name.

## Decisions

- **Pagination**: Implement a while-loop in the brand sync command to fetch all pages of brands.
- **Payload Structure**: Update `WooCommerceClient::request` to use the `json` key for `POST/PUT` payloads to ensure proper body construction.
- **Brand Mapping**: Use case-insensitive `LIKE` matching in `SyncProcessor` to handle slight naming discrepancies between Olsera and WooCommerce.

## Risks / Trade-offs

- **Performance**: Fetching hundreds of brands might take time but is mitigated by using a 100-item per-page limit.
- **DB Compatibility**: Using `LIKE` ensures cross-compatible case-insensitivity between SQLite (local development) and MySQL (production).
