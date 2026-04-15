## ADDED Requirements

### Requirement: Expandable variant rows
The product table SHALL support expanding rows to show detailed variant information for products that have `has_variants` set to true.

#### Scenario: Inspecting shoe sizes
- **WHEN** the user clicks an "Expand" or "View Variants" button on a product row
- **THEN** a nested list or sub-table SHALL be displayed below that row showing all variants

### Requirement: Variant details display
For each expanded variant, the system SHALL display the following columns: Variant Name (e.g., Size), SKU, Price (Original), Sell Price (Active), and Current Stock.

#### Scenario: Comparing prices across variants
- **WHEN** the user expands a product
- **THEN** they SHALL see the `sell_price` and `stock` for every individual variation
