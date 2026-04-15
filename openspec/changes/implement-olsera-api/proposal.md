## Why

The current Olsera integration needs to be updated to use the Olsera Open API (v1). This is required to ensure compatibility with the latest API features and to provide a more robust way of synchronizing product data.

## What Changes

- Implement authentication logic for Olsera Open API using `app_id` and `secret_key`.
- Implement token refresh mechanism using `refresh_token`.
- Implement product listing functionality from the Open API.
- Implement product detail retrieval for specific products.
- Update application configuration to store Open API credentials.
- Ensure raw API responses are logged for debugging/audit purposes.

## Capabilities

### New Capabilities
- `olsera-auth`: Handles token generation and refresh using Olsera Open API credentials.
- `olsera-product-api`: Provides methods to fetch product lists and detailed product information.
- `database-schema-update`: Updates the local product schema to match Olsera Open API v1 fields (barcode, weight, buy_price, etc.).
- `sync-logic-migration`: Migrates the `SyncProcessor` and related commands/dashboard to use the new Open API client.

### Modified Capabilities
- `SyncProcessor`: Updated to use `OlseraOpenClient` for data ingestion.

## Impact

- **Affected Code**: `App\Services\OlseraClient` (supplemented by `OlseraOpenClient`), `App\Services\SyncProcessor` (migrated to new client), `database/migrations/*_create_products_table.php`.
- **APIs**: Olsera Open API v1.
- **Dependencies**: Laravel's `Http` client.
- **Systems**: Configuration in `config/services.php` and environment variables.
