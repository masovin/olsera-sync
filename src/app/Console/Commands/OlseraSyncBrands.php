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
        $this->info("Starting Brand Sync from WooCommerce...");

        $page = 1;
        $totalImported = 0;

        while (true) {
            $this->comment("Fetching page {$page}...");
            
            // Get brands using terms endpoint (assuming taxonomy is product_brand)
            $brands = $woo->request('GET', '/products/brands', [
                'per_page' => 100,
                'page' => $page
            ]);

            if (empty($brands) || !is_array($brands)) {
                break;
            }

            foreach ($brands as $brandData) {
                Brand::updateOrCreate(
                    ['woocommerce_id' => $brandData['id']],
                    [
                        'name' => $brandData['name'],
                        'slug' => $brandData['slug'],
                    ]
                );
                $totalImported++;
            }

            if (count($brands) < 100) {
                break;
            }

            $page++;
        }

        $this->info("Successfully synced {$totalImported} brands.");
        return 0;
    }
}
