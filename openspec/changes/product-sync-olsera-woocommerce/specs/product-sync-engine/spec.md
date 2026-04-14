## ADDED Requirements

### Requirement: Local Product Staging
The system must store fetched Olsera product data in a local database table before attempting to synchronize it with WooCommerce.

#### Scenario: Olsera data persistence
- **WHEN** products are fetched from Olsera
- **THEN** they should be saved/updated in the local `products` table with their latest attributes

### Requirement: Raw Data Logging
The system must save the raw JSON response from every Olsera API call to a local storage directory for audit and debugging purposes.

#### Scenario: Save Olsera response
- **WHEN** a response is received from Olsera
- **THEN** it should be saved as a `.json` file in `storage/olsera` with a timestamped filename

### Requirement: Sync Status Tracking
Each local product record should maintain an `is_synced` flag to indicate whether its latest state has been successfully pushed to WooCommerce.

#### Scenario: Mark as synced
- **WHEN** a product is successfully updated in WooCommerce
- **THEN** the local record's `is_synced` flag should be set to `true`
