## 1. Setup & Environment

- [x] 1.1 Create the `App\Livewire\ProductCatalog` Livewire component.
- [x] 1.2 Define the `/products` route in `routes/web.php` and assign it a name.
- [x] 1.3 Add a navigation link to the "Catalog" in the main application layout (`resources/views/layouts/app.blade.php`).

## 2. Core Catalog Implementation

- [x] 2.1 Implement the product fetching logic in `ProductCatalog` with pagination and search/filter parameters (Name, SKU, Brand).
- [x] 2.2 Develop the `resources/views/livewire/product-catalog.blade.php` view with a responsive table or grid.
- [x] 2.3 Implement the row expansion logic to toggle variant visibility for individual products.

## 3. Variant View & Polish

- [x] 3.1 Build the nested variant table within the expanded product row, showing SKU, price, and stock for each variation.
- [x] 3.2 Add loading states and clear empty-state messaging when no products match Search criteria.
- [x] 3.3 Ensure the UI clearly distinguishes between parent products and their nested variants using indentation or styles.

## 4. Verification

- [x] 4.1 Verify that the Catalog page is accessible via the navigation menu.
- [x] 4.2 Confirm that search results update correctly when typing in the search box.
- [x] 4.3 Validate that variant expansion reveals the correct child records from the `product_variants` table.
