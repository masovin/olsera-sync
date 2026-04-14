## 1. Setup & Integration Foundation

- [x] 1.1 Add Olsera and WooCommerce credentials to `.env.example` and `.env`.
- [x] 1.2 Configure API services in `config/services.php`.
- [x] 1.3 Implement `App\Services\OlseraClient` with methods to fetch products and stock.
- [x] 1.4 Implement `App\Services\WooCommerceClient` using the official library or direct HTTP calls to manage products.

## 2. Infrastructure & Data Layer

## 2. Infrastructure & Data Layer

- [x] 2.1 Create migration for `products` table (olsera_id, sku, name, price, stock, is_synced, etc.).
- [x] 2.2 Create migration for `sync_mappings` table (olsera_id, woocommerce_id).
- [x] 2.3 Create migration for `sync_logs` table (type, status, message, details).
- [x] 2.4 Create `Product`, `SyncMapping`, and `SyncLog` Eloquent models.

## 3. Core Sync Engine

- [x] 3.1 Implement `App\Services\SyncProcessor` service.
- [x] 3.2 Implement logic to fetch from Olsera and save/update local `Product` records.
- [x] 3.3 Implement logic to save raw Olsera JSON response to `storage/olsera`.
- [x] 3.4 Implement logic to sync local `Product` records to WooCommerce.
- [x] 3.5 Implement `is_synced` flag update logic after successful WooCommerce sync.
- [x] 3.6 Implement data mapper to convert Olsera product structure to WooCommerce structure.

## 4. Automation & CLI

- [ ] 4.1 Create `SyncProductsJob` for full product synchronization.
- [ ] 4.2 Create `SyncInventoryJob` for lightweight stock-only updates.
- [ ] 4.3 Implement `olsera:sync-products` Artisan command.
- [ ] 4.4 Implement `olsera:sync-inventory` Artisan command.
- [ ] 4.5 Setup basic Frontend UI for sync management (e.g., using Filament or simple Blade/Livewire).
- [ ] 4.6 Schedule jobs in `routes/console.php`.

## 5. Verification & Testing

- [ ] 5.1 Manual test of Olsera API connection.
- [ ] 5.2 Manual test of WooCommerce API connection.
- [ ] 5.3 Verify full sync process with a small batch of products.
- [ ] 5.4 Verify inventory sync updates in WooCommerce.
- [ ] 5.5 Check database logs for accuracy.
