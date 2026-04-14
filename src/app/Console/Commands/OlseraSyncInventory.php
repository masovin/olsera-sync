<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Services\SyncProcessor;

#[Signature('olsera:sync-inventory')]
#[Description('Fetch only stock levels from Olsera and sync to WooCommerce')]
class OlseraSyncInventory extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SyncProcessor $processor)
    {
        $this->info('Starting Olsera to WooCommerce Inventory Sync...');

        $this->info('Step 1: Fetching inventory from Olsera...');
        $ingestResult = $processor->ingestInventory();
        $this->info("Found {$ingestResult['count']} products with updated stock.");

        $this->info('Step 2: Dispatching to WooCommerce...');
        $dispatchResult = $processor->dispatch();
        $this->info("Successfully synced: {$dispatchResult['synced']}");
        $this->info("Failures: {$dispatchResult['failed']}");

        $this->info('Inventory sync completed.');
    }
}
