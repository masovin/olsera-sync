## ADDED Requirements

### Requirement: Parent Attribute Definition
When creating or updating a variable product, the parent product must define the attributes that its variations will use. The attribute name is dynamically selected based on the variant characteristics.

#### Scenario: Syncing Footwear Attributes
- **WHEN** an Olsera product has nested variants with numeric names (e.g., "42")
- **THEN** the WooCommerce parent is created with an attribute named "Size".

#### Scenario: Syncing Apparel Attributes
- **WHEN** an Olsera product has nested variants with letter-based names (e.g., "M", "XL")
- **THEN** the WooCommerce parent is created with an attribute named "Apparel Size".

### Requirement: Automatic Category Assignment
Products must be assigned a WooCommerce category based on their detected product type (Footwear vs Apparel).

#### Scenario: Footwear Category Assignment
- **WHEN** the product is detected as Footwear (`Size`)
- **THEN** it is assigned to WooCommerce category ID `23`.

#### Scenario: Apparel Category Assignment
- **WHEN** the product is detected as Apparel (`Apparel Size`)
- **THEN** it is assigned to WooCommerce category ID `80`.

### Requirement: Hierarchical Sync Loop
The synchronization process must handle the parent and variants in the correct order to ensure referential integrity.

#### Scenario: Successful Variation Linking
- **WHEN** a new variable product is synchronized
- **THEN** the parent is created first, and its WooCommerce ID is used to create the corresponding variations.

### Requirement: Mapping Persistence for Variations
Variation IDs must be stored in the `sync_mappings` table to allow future updates instead of duplicate creations.

#### Scenario: Subsequent Variation Update
- **WHEN** a variation's price changes in Olsera
- **THEN** the sync found the existing mapping for that variation and updates the specific variation on WooCommerce without recreating the parent.
