<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Services\SyncProcessor;

#[Signature('olsera:sync-products {--timeout=300 : Max execution time in seconds} {--dispatch-only : Skip Step 1 and only sync unsynced products to WooCommerce}')]
#[Description('Fetch products from Olsera and sync to WooCommerce')]
class OlseraSyncProducts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SyncProcessor $processor)
    {
        $timeout = (int) $this->option('timeout');
        $startTime = microtime(true);
        $this->info("Starting Olsera to WooCommerce Product Sync (Timeout: {$timeout}s)...");

        if (!$this->option('dispatch-only')) {
            $this->info('Step 1: Ingesting from Olsera...');
            $page = 1;
            $totalIngested = 0;
            
            do {
                $this->comment("  Fetching page {$page}...");
                $ingestResult = $processor->ingest($page, 15);
                $totalIngested += $ingestResult['processed'];
                $this->info("    - Page {$page}: Found {$ingestResult['count']} items.");
                $page++;
            } while ($ingestResult['has_more']);

            $this->info("Total Items Ingested/Updated: {$totalIngested}");
        } else {
            $this->info('Step 1: Skipped (--dispatch-only).');
        }

        $this->info('Step 2: Dispatching to WooCommerce...');
        $synced = 0;
        $failed = 0;
        $remaining = 1; // Initial value to enter loop

        while ($remaining > 0) {
            // Check timeout
            $elapsed = microtime(true) - $startTime;
            if ($elapsed >= $timeout) {
                $this->warn("Timeout of {$timeout}s reached. Stopping gracefully.");
                $this->warn("Progress: {$synced} synced, {$failed} failed. {$remaining} items remaining.");
                break;
            }

            $this->comment("  Processing batch (Time elapsed: " . round($elapsed, 1) . "s)...");
            $dispatchResult = $processor->dispatch(20);
            
            $synced += $dispatchResult['synced'];
            $failed += $dispatchResult['failed'];
            $remaining = $dispatchResult['remaining'];

            if ($dispatchResult['synced'] === 0 && $dispatchResult['failed'] === 0) {
                // Safety break if nothing was processed
                break;
            }
        }

        $this->info("Successfully synced: {$synced}");
        $this->info("Failures: {$failed}");

        $this->info('Sync process completed.');
    }
}
