## Context

The `olsera-sync` application currently has a dashboard for managing synchronization tasks. This design introduces a second primary view: the Product Catalog. This page will serve as the local inventory browser, allowing users to verify data ingested from Olsera v1 before it is pushed to WooCommerce.

## Goals / Non-Goals

**Goals:**
- Implement a paginated list of products.
- Provide real-time search by name, SKU, or brand.
- Enable row expansion to show variant-level data (Price, stock, etc.).
- Update navigation to allow toggling between Dashboard and Catalog.

**Non-Goals:**
- Allowing manual editing of product data (the database should remain a mirror of Olsera).
- Implementing batch actions on the catalog page.

## Decisions

### 1. Unified Search Logic
**Decision**: Implement search using a public `$search` property in Livewire and filtering in the `render()` method.
**Rationale**: Leverages Livewire's reactive binding while keeping the database query logic consolidated in the controller.

### 2. State-Based Row Expansion
**Decision**: Use a Livewire array property `expandedRows = []` to track which product IDs have their variants visible.
**Rationale**: Simple to manage and allows multiple rows to be open simultaneously without complex DOM manipulation.

### 3. Layout Restructuring
**Decision**: Introduce a navigation sidebar/header in `layouts.app` to provide easy access to `/` (Dashboard) and `/products` (Catalog).
**Rationale**: Standardizes the application's navigation pattern as it grows beyond a single page.

## Risks / Trade-offs

- **Memory Usage** → Storing large lists in Livewire state could be heavy. **Mitigation**: Use `WithPagination` and ensure only necessary IDs are tracked in the expansion array.
- **UI Clutter** → Deeply nested tables can become hard to read. **Mitigation**: Use subtle backgrounds for expanded variant rows and clear headers.
