## Context

The current `OlseraSyncProducts` command executes a single `ingest()` and a single `dispatch()`.
- `ingest()` fetches the first page of products from Olsera (default 100).
- `dispatch()` processes all unsynced products in a single database collection.
This works for small inventories but fails to sync all products for larger inventories and risk PHP timeout errors.

## Goals / Non-Goals

**Goals:**
- Implement a pagination loop for product ingestion.
- Implement a batching loop for product dispatching.
- Add runtime monitoring to gracefully exit before server timeouts occur.
- Allow the user to specify a timeout via command-line argument.

**Non-Goals:**
- Implementing a permanent background worker (this remains a console command).
- Changing the synchronization logic for individual products.

## Decisions

- **Command-Line Option**: Add `--timeout` (default 300 seconds) to `OlseraSyncProducts`.
- **Ingestion Loop**: Loop `ingest(page++)` while the result count matches the page size.
- **Dispatch Loop**: Loop `dispatch(batchSize)` while unsynced products exist and `elapsedTime < timeout`.
- **Elapsed Time Tracking**: Use `microtime(true)` at the start and check it at the end of each batch.

## Risks / Trade-offs

- **Memory Usage**: Looping in a single PHP process can lead to memory growth. We will use `Product::where(...)->get()` per batch to keep the hydrated model count low.
- **Partial Sync**: A timeout will result in a partial sync. This is by design; the remaining products will be picked up on the next execution.
