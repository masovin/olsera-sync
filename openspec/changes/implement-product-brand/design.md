## Context

Currently, the synchronization engine treats brands as raw strings (`brand` column in `products` table). However, WooCommerce often utilizes a dedicated taxonomy (typically `product_brand`) to manage brands. This mismatch prevents products from being correctly associated with brands on the WooCommerce side during synchronization.

## Goals / Non-Goals

**Goals:**
- Provide a centralized repository of WooCommerce brands in the local database.
- Automate the fetching of brands from the WooCommerce store.
- Map Olsera product classifications to official WooCommerce brand IDs.
- Update the product synchronization payload to include brand taxonomy associations.

**Non-Goals:**
- Creating new brands in WooCommerce (brands should be managed in WooCommerce first).
- Supporting multiple brands per product (Olsera single classification model).

## Decisions

### 1. Database Schema
A new `Brand` model and `brands` migration will be created:
- `id`: Internal primary key.
- `woocommerce_id`: ID of the term in WooCommerce.
- `name`: Human-readable name (used for mapping match).
- `slug`: WooCommerce term slug.

### 2. WooCommerce Client Extension
Add a `getBrands()` method to `WooCommerceClient` that requests the `/products/brands` endpoint. 
> [!NOTE]
> We will default to `/products/brands` which is standard for the most popular brand plugins. If the store uses a custom attribute, this can be updated in the client.

### 3. Brand Synchronisation Command
A new Artisan command `olsera:sync-brands` will call `WooCommerceClient::getBrands()` and update the local `brands` table using `updateOrCreate`.

### 4. Resolving Brands in SyncProcessor
In `SyncProcessor::mapProductToWoo()`, we will:
1.  Read the `brand` string from the local `Product`.
2.  Query the `Brand` table for a match by name.
3.  If found, add a `brands` array to the WooCommerce payload:
    ```json
    "brands": [term_id]
    ```

## Risks / Trade-offs

- **Name Mismatch**: Case sensitivity or minor spelling differences between Olsera and WooCommerce will cause mapping to fail. We will use a case-insensitive search by default.
- **Taxonomy Availability**: If the WooCommerce store does not have a Brands plugin installed, the API call will fail. We will wrap the call in a try-catch for robustness.
