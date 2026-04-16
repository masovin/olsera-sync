<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WooCommerceClient;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;

class OlseraSyncBrands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olsera:sync-brands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch product brands from WooCommerce and store them locally';

    /**
     * Execute the console command.
     */
    public function handle(WooCommerceClient $woo)
    {
        $this->info('Fetching brands from WooCommerce...');

        try {
            $page = 1;
            $count = 0;
            $totalSynced = 0;

            do {
                $this->info("Fetching page {$page}...");
                $brands = $woo->getBrands(['page' => $page, 'per_page' => 100]);

                if (empty($brands)) {
                    break;
                }

                $count = count($brands);
                foreach ($brands as $brandData) {
                    if (empty($brandData['id'])) continue;

                    Brand::updateOrCreate(
                        ['woocommerce_id' => $brandData['id']],
                        [
                            'name' => $brandData['name'] ?? 'Unknown',
                            'slug' => $brandData['slug'] ?? null,
                        ]
                    );
                    $totalSynced++;
                }

                $page++;
            } while ($count === 100);

            $this->info("Successfully synced {$totalSynced} brands.");
            Log::info("Successfully synced {$totalSynced} brands from WooCommerce.");

        } catch (\Exception $e) {
            $this->error('Failed to sync brands: ' . $e->getMessage());
            Log::error('Brand Sync Error: ' . $e->getMessage());
        }

        return 0;
    }
}
