## Why

Currently, the synchronization process runs in a single pass. For large datasets, this leads to two problems:
1. **Partial Ingestion**: Only the first page of products is fetched from Olsera.
2. **Timeouts**: Processing hundreds of products in a single WooCommerce API session can exceed PHP's `max_execution_time`.

Implementing a robust loop with runtime monitoring ensures all products are eventually synced while maintaining server stability.

## What Changes

- **Looping Ingestion**: `SyncProcessor` will loop through all available pages of the Olsera Open API.
- **Batching & Timeouts in Command**: The `olsera:sync-products` command will loop through unsynced products in small batches, checking the elapsed time between batches and stopping gracefully if a timeout (e.g., 300s) is reached.
- **Improved Service Methods**: `SyncProcessor` methods will be updated to support pagination and limits.

## Capabilities

### New Capabilities
- `sync-timeout-management`: Global management of execution time for long-running sync tasks.

### Modified Capabilities
- `product-sync`: Added multi-page ingestion and batched dispatching.

## Impact

- `OlseraSyncProducts.php`: New looping structure in `handle()`.
- `SyncProcessor.php`: Updated `ingest()` and `dispatch()` signatures.
- Performance: More predictable resource usage.
- Reliability: Ensures 100% of products are eventually processed across one or more runs.
