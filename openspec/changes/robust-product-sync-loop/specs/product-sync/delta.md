## MODIFIED Requirements

### Requirement: Full Inventory Ingestion
The system must be able to fetch all products from the Olsera Open API, regardless of the number of pages.

#### Scenario: Multi-page ingestion
- **WHEN** the Olsera API has results spanning multiple pages
- **THEN** the system must iterate through all pages (page 1, 2, ...) until no more items are found.

### Requirement: Time-Limited Dispatching
The system must be able to synchronize products to WooCommerce in batches and stop when a provided timeout is reached.

#### Scenario: Running out of time
- **WHEN** the elapsed time of the synchronization command exceeds the user-provided `--timeout`
- **THEN** the current batch must finish, the command must stop further processing, and report the partial results.
