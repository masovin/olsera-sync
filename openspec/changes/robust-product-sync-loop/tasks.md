## 1. Service Layer Updates

- [ ] 1.1 Update `SyncProcessor::ingest()` to support `page` and `limit` parameters and return results with a completion indicator.
- [ ] 1.2 Update `SyncProcessor::dispatch()` to process only a specified number of products and return progress.

## 2. Command Refactoring

- [ ] 2.1 Add the `--timeout` option and `startTime` tracking to `OlseraSyncProducts`.
- [ ] 2.2 Implement a `while` loop for multi-page product ingestion from Olsera.
- [ ] 2.3 Implement the resilient dispatch loop with batching and time checks.

## 3. Verification

- [ ] 3.1 Test with a short timeout (e.g., `--timeout=5`) to verify graceful halting.
- [ ] 3.2 Test full synchronization with default settings to verify complete data ingestion and dispatch.
