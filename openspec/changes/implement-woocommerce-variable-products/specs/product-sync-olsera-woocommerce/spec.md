## MODIFIED Requirements

### Requirement: Differentiated Sync Modes
The sync processor must detect whether a product should be handled as a "simple" or "variable" product.

#### Scenario: Auto-detect Variable Type
- **WHEN** the local product has `has_variants` set to true
- **THEN** the processor uses the variable sync logic, otherwise it defaults to simple.
