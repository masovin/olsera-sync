## Why

The current system provides a synchronization dashboard but lacks a user interface to browse and verify the products and variants stored in the local database. A product catalog page is essential for users to inspect the ingested data, verify branding, pricing, and stock levels before they are synced to WooCommerce.

## What Changes

- **New Page**: Introduction of a "Product Catalog" page accessible via a new `/products` route.
- **UI Components**: Implementation of a searchable and filterable product grid/table using Livewire.
- **Variant Inspection**: Ability to expand a product to view all its associated variants, including their distinct SKUs, prices, and stock.
- **Navigation**: Update the application's layout to include a link to the new catalog.
- **Brand Information**: Display the recently added "brand" (classification) data for each product.

## Capabilities

### New Capabilities
- `product-catalog-browsing`: Allows users to list, search, and view product details directly from the local database.
- `variant-data-display`: Provides a clear interface for viewing nested variant information within the product context.

### Modified Capabilities
- None

## Impact

- **Affected Code**: `App\Livewire\ProductCatalog` (new), `resources/views/livewire/product-catalog.blade.php` (new), `routes/web.php`, `resources/views/layouts/app.blade.php`.
- **User Interface**: New visual components for the product list and variant details.
- **Database**: Reads from `products` and `product_variants` tables.
