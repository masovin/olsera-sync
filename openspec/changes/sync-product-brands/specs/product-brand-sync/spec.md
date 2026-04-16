## ADDED Requirements

### Requirement: Automated Brand Fetching
The system must be able to fetch all available brands from WooCommerce and store them in a local cache/database for mapping.

#### Scenario: Successful Brand Synchronization
- **WHEN** the `olsera:sync-brands` command is executed.
- **THEN** it should retrieve multiple pages of brand data from WooCommerce and update the local `brands` table.

### Requirement: Brand Mapping during Product Sync
Products ingested from Olsera must have their classification resolved to a WooCommerce brand ID based on locally cached brand data.

#### Scenario: Brand Matching
- **WHEN** a product from Olsera has 'Nike' as its classification.
- **THEN** the system should find the corresponding WooCommerce brand ID for 'Nike' and include it in the product synchronization payload.
