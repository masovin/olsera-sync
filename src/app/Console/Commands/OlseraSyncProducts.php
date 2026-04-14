<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Services\SyncProcessor;

#[Signature('olsera:sync-products')]
#[Description('Fetch products from Olsera and sync to WooCommerce')]
class OlseraSyncProducts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SyncProcessor $processor)
    {
        $this->info('Starting Olsera to WooCommerce Product Sync...');

        $this->info('Step 1: Ingesting from Olsera...');
        $ingestResult = $processor->ingest();
        $this->info("Ingested {$ingestResult['count']} products/updates.");

        $this->info('Step 2: Dispatching to WooCommerce...');
        $dispatchResult = $processor->dispatch();
        $this->info("Successfully synced: {$dispatchResult['synced']}");
        $this->info("Failures: {$dispatchResult['failed']}");

        $this->info('Sync process completed.');
    }
}
