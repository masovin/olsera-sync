## Context

This project aims to bridge the gap between Olsera (offline/online POS) and WooCommerce (online store). Currently, there is no connection between the two systems, leading to manual inventory updates and potential data mismatches.

## Goals / Non-Goals

**Goals:**

- Unidirectional synchronization of product data from Olsera to WooCommerce.
- Real-time or near-real-time inventory level updates.
- Centralized mapping of products using SKUs as the source of truth.
- Comprehensive logging of sync events and failures.
- Frontend UI for managing the sync
- Save response Json from Olsera to /storage/olsera
- Successfull synced product has flag in products table

**Non-Goals:**

- Syncing customers, orders, or loyalty points (future phase).
- Syncing in the reverse direction (WooCommerce to Olsera).

## Decisions

### Architectural Approach

We will use a **Service-Provider pattern**.

- `OlseraClient`: Handles all raw API requests to Olsera.
- `WooCommerceClient`: Handles all raw API requests to WooCommerce.
- `SyncProcessor`: Orchestrates the flow - fetch from one, map, then push to the other.

### Data Storage

- A `products` table will store the product data.
- A `sync_mappings` table will track the relationship between Olsera Product IDs and WooCommerce Product IDs to speed up lookups, though SKU will remain the primary logical key.
- A `sync_logs` table will store the status of each sync attempt (Success/Failure/Errors).

### Automation

- Use **Laravel Jobs** to process sync tasks in the background.
- Use **Artisan Commands** as the entry points for the scheduler.

## Risks / Trade-offs

- **API Rate Limiting**: Both Olsera and WooCommerce have rate limits. We will implement chunking and sleep intervals in our background jobs to mitigate this.
- **SKU Consistency**: If SKUs are missing or duplicated in either system, the sync will fail for those specific products. We will log these as "Invalid Data" rather than stopping the entire sync.
- **Image Handling**: Syncing product images can be bandwidth-intensive. We will implement a check to only upload images if the hash has changed.
