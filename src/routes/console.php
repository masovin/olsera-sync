<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Full product sync every day at midnight
Schedule::command('olsera:sync-products')->daily();

// Inventory sync every 15 minutes
Schedule::command('olsera:sync-inventory')->everyFifteenMinutes();
