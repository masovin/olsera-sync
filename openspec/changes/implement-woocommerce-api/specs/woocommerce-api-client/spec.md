## ADDED Requirements

### Requirement: CRUD for WooCommerce Products
The client must support creating, retrieving, listing, and updating parent products on the WooCommerce store.

#### Scenario: Existing Product Lookup
- **WHEN** searching for a product by its SKU
- **THEN** the client returns the specific product details or null if no match is found.

### Requirement: Variation Management
The client must allow creating and updating variations specifically under a parent variable product.

#### Scenario: Update Variation Stock
- **WHEN** providing a parent ID and variation ID with a new stock level
- **THEN** the variation's inventory is updated on WooCommerce.

### Requirement: Connection Resilience
The client must handle connection issues and long-running API requests without timing out prematurely.

#### Scenario: API Timeout handling
- **WHEN** the WooCommerce API takes longer than 30 seconds to respond
- **THEN** the client properly catches the error and reports it instead of crashing.
