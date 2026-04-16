## 1. Logic Removal

- [x] 1.1 Remove the image mapping conditional block in `SyncProcessor::mapVariantToWoo`.

## 2. Verification

- [x] 2.1 Run `php artisan olsera:sync-products` and verify in the logs (`storage/app/private/woocommerce/`) that variation payloads no longer contain the `image` key.
