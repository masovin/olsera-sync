## ADDED Requirements

### Requirement: Detecting variants during sync
The `SyncProcessor` SHALL check for the presence of a `variants` array in the Olsera product API response.

#### Scenario: Product with variants received
- **WHEN** the Olsera API returns a product with `has_variant` set to `1` and a populated `variants` array
- **THEN** the `SyncProcessor` SHALL iterate through the variants and process them individually

### Requirement: Upsert variant data
The system SHALL use an "update-or-create" approach for variants based on their Olsera variant ID.

#### Scenario: Syncing existing variant with changes
- **WHEN** a variant's price or stock has changed in Olsera
- **THEN** matching the variation by `olsera_id` SHALL update the local record with the new values

### Requirement: Variant stock updates in inventory sync
The `ingestInventory` method SHALL update stock levels for variants by parsing the nested `variants` data in the product list response.

#### Scenario: Inventory-only sync
- **WHEN** running the inventory synchronization command
- **THEN** the system SHALL update the `stock` field for both standalone products and product variants
