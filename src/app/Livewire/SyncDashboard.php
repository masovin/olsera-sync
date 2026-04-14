<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\SyncLog;
use App\Services\SyncProcessor;
use Livewire\Component;
use Livewire\WithPagination;

class SyncDashboard extends Component
{
    use WithPagination;

    public $isSyncing = false;
    public $syncType = '';

    public function runProductSync(SyncProcessor $processor)
    {
        $this->isSyncing = true;
        $this->syncType = 'Full Product Sync';

        try {
            $processor->ingest();
            $processor->dispatch();
            session()->flash('message', 'Full product synchronization completed successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Sync failed: ' . $e->getMessage());
        }

        $this->isSyncing = false;
    }

    public function runInventorySync(SyncProcessor $processor)
    {
        $this->isSyncing = true;
        $this->syncType = 'Inventory Sync';

        try {
            $processor->ingestInventory();
            $processor->dispatch();
            session()->flash('message', 'Inventory synchronization completed successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Sync failed: ' . $e->getMessage());
        }

        $this->isSyncing = false;
    }

    public function render()
    {
        return view('livewire.sync-dashboard', [
            'totalProducts' => Product::count(),
            'pendingSync' => Product::where('is_synced', false)->count(),
            'lastSync' => SyncLog::where('status', 'success')->latest()->first(),
            'logs' => SyncLog::latest()->paginate(10),
        ])->layout('layouts.app');
    }
}
