## ADDED Requirements

### Requirement: Fetch Olsera products
The system must be able to retrieve a list of all active products from the Olsera store via their REST API.

#### Scenario: Successful product retrieval
- **WHEN** the Olsera integration service is called to fetch products
- **THEN** it should return a standardized collection of product data including name, SKU, price, and description

### Requirement: Fetch Olsera stock levels
The system must be able to retrieve current stock levels for products to ensure inventory synchronization.

#### Scenario: Stock level retrieval
- **WHEN** requested for current inventory
- **THEN** it should return stock quantities mapped to product SKUs
