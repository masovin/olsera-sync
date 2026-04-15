## ADDED Requirements

### Requirement: Product can have multiple variants
The system SHALL support a one-to-many relationship between the `Product` model and a new `ProductVariant` model.

#### Scenario: Product with variants is retrieved
- **WHEN** a product is fetched from the database
- **THEN** it SHOULD be possible to access its associated variants through an Eloquent relationship

### Requirement: Variant data capture
The system SHALL store the following data for each product variant: `olsera_id` (unique), `sku`, `name`, `price`, `stock`, `barcode`, and `weight`.

#### Scenario: Storing a new variant
- **WHEN** a product variant is synced from Olsera
- **THEN** the system SHALL create or update a record in the `product_variants` table with all specified fields

### Requirement: Parent-child relationship integrity
The system SHALL ensure that each variant is correctly linked to its parent product via a foreign key `product_id`.

#### Scenario: Deleting a product
- **WHEN** a product is deleted from the database
- **THEN** all its associated variants SHALL also be deleted (cascade delete)
