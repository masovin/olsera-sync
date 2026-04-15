## ADDED Requirements

### Requirement: List ingested products
The system SHALL provide a paginated list of all products currently stored in the local database.

#### Scenario: Viewing the initial product list
- **WHEN** the user navigates to the `/products` route
- **THEN** the system SHALL display a table of products including Name, SKU, Brand, and Total Stock

### Requirement: Search functionality
The system SHALL allow users to filter the product list by name, SKU, or brand using a search input.

#### Scenario: Searching for a specific shoe model
- **WHEN** the user types "Nike Dunk" into the search box
- **THEN** the product list SHALL be filtered in real-time to show only matching entries

### Requirement: Navigation access
The application layout SHALL include a "Catalog" link in the sidebar or navigation menu.

#### Scenario: Switching from dashboard to catalog
- **WHEN** the user clicks the "Catalog" link in the navigation menu
- **THEN** they SHALL be redirected to the product list page
