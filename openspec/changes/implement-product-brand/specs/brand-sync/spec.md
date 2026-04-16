## ADDED Requirements

### Requirement: WooCommerce Brand Ingestion
The system must be able to fetch all available brands from the WooCommerce store and store them in a local `brands` database table.

#### Scenario: Syncing brands from WooCommerce
- **WHEN** the `olsera:sync-brands` command is executed
- **THEN** the system should call the WooCommerce API for the `product_brand` taxonomy
- **AND** create or update local `Brand` records with the `name`, `slug`, and `woocommerce_id`.

### Requirement: Brand Mapping Resolution
The system must resolve a product's brand name (from Olsera) into a WooCommerce brand term ID during the synchronization process.

#### Scenario: Mapping Olsera brand to WooCommerce ID
- **WHEN** a product is being mapped for WooCommerce
- **AND** the product has a brand string (e.g., "Adidas")
- **THEN** the system should look up "Adidas" in the local `brands` table
- **AND** include the corresponding `woocommerce_id` in the API payload's `brands` field.

#### Scenario: Unmapped Brand
- **WHEN** a product brand string does not match any local `Brand` record
- **THEN** the system should skip brand assignment for that product and log a warning.
