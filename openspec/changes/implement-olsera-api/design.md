## Context

The current `OlseraClient` is not compatible with the Olsera Open API v1. The documentation provided specifies a Bearer token authentication flow and a specific URL structure for products. We need a modern client that handles authentication and product retrieval according to these specs.

## Goals / Non-Goals

**Goals:**
- Implement a robust `OlseraOpenClient` for the Open API.
- Automate token management (generation on failure/expiration).
- Provide clean wrapper methods for `getProducts` and `getProductDetail`.
- Log all API interactions for auditing.

**Non-Goals:**
- Modifying the existing legacy `OlseraClient` (to prevent breaking changes).
- Implementing other parts of the Olsera API not listed in the provided documentation.

## Decisions

### 1. New Service Class
**Decision**: Create `App\Services\OlseraOpenClient`.
**Rationale**: Keeps the new implementation clean and isolated from any legacy code.
**Alternatives**: Updating `OlseraClient`. Rejected because the authentication headers and URL structures are fundamentally different.

### 2. Token Storage
**Decision**: Use `Cache` or a small database table to store the `access_token` and `refresh_token`.
**Rationale**: Tokens should persist across requests to avoid unnecessary authentications.
**Alternatives**: Environment variables (rejected, too dynamic) or a file (rejected, less idiomatic than Cache).

### 3. Automatic Token Refresh
**Decision**: The client SHALL attempt to refresh the token if a request fails with a 401 Unauthorized status.
**Rationale**: Provides a seamless experience for consumers of the service.
**Alternatives**: Manual token management by the caller. Rejected as it complicates the calling code.### 4. SyncProcessor Migration
**Decision**: Update `SyncProcessor` to depend on `OlseraOpenClient`.
**Rationale**: Centralizing the sync logic ensures both CLI and Dashboard benefit from the new Open API.
**Implementation**:
- Update constructor to inject `OlseraOpenClient`.
- Refactor `ingest()` to map `v1/en/product` fields (e.g., `id`, `sku`, `name`, `price`, `stock`).
- Refactor `ingestInventory()` to use the Open API's product data if a dedicated stock endpoint is missing.

### 5. Dashboard & CLI Commands
**Decision**: No changes required to `SyncDashboard.php` or `OlseraSync*.php` commands.
**Rationale**: They already use `SyncProcessor` which will be updated via dependency injection.

### 6. Database Schema Update
**Decision**: Modify the `products` table migration to include missing fields from the Olsera Open API v1.
**Rationale**: Storing these fields locally reduces the need for repeated API calls and enables better synchronization with WooCommerce.
**New Fields**:
- `barcode` (string, nullable)
- `buy_price` (decimal, default 0)
- `weight` (decimal, default 0)
- `is_variant` (boolean, default false)
- `allow_decimal` (boolean, default false)

## Risks / Trade-offs

- **[Risk] Token Refresh Failure** → **[Mitigation]** If refresh fails, attempt a full re-authentication using `app_id` and `secret_key`.
- **[Risk] Expired Refresh Token** → **[Mitigation]** Re-authenticate from scratch if refresh token is rejected.
