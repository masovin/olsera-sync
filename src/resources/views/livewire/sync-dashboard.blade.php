<div wire:poll.10s>
    <header class="flex justify-between items-center mb-12">
        <div>
            <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-blue to-brand-purple">
                Product Sync Manager
            </h1>
            <p class="text-slate-400 mt-2">Synchronizing Olsera POS with WooCommerce Store</p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand-blue/10 text-brand-blue border border-brand-blue/20">
                System Active
            </span>
        </div>
    </header>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="glass p-6 rounded-2xl">
            <h3 class="text-sm font-medium text-slate-400 mb-1">Total Syncable Products</h3>
            <p class="text-3xl font-bold">{{ number_format($totalProducts) }}</p>
        </div>
        <div class="glass p-6 rounded-2xl border-l-4 border-l-brand-purple">
            <h3 class="text-sm font-medium text-slate-400 mb-1">Pending Synchronization</h3>
            <p class="text-3xl font-bold">{{ number_format($pendingSync) }}</p>
        </div>
        <div class="glass p-6 rounded-2xl">
            <h3 class="text-sm font-medium text-slate-400 mb-1">Last Successful Sync</h3>
            <p class="text-xl font-bold">{{ $lastSync ? $lastSync->created_at->diffForHumans() : 'Never' }}</p>
        </div>
    </div>

    <!-- Action Center -->
    <div class="glass p-8 rounded-3xl mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-brand-purple/10 blur-3xl rounded-full"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-6">Action Center</h2>
            <div class="flex flex-wrap gap-4">
                <button 
                    wire:click="runProductSync" 
                    wire:loading.attr="disabled"
                    class="group relative inline-flex items-center px-8 py-4 font-bold text-white bg-gradient-to-r from-brand-blue to-brand-blue/80 rounded-xl transition-all hover:scale-105 disabled:opacity-50 disabled:hover:scale-100"
                >
                    <span wire:loading.remove wire:target="runProductSync">Full Product Sync</span>
                    <span wire:loading wire:target="runProductSync" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Syncing...
                    </span>
                </button>

                <button 
                    wire:click="runInventorySync" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-8 py-4 font-bold text-brand-purple border-2 border-brand-purple/30 rounded-xl transition-all hover:bg-brand-purple/10 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="runInventorySync">Inventory Stock Sync</span>
                    <span wire:loading wire:target="runInventorySync" class="flex items-center">
                         <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-brand-purple" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating Stock...
                    </span>
                </button>
            </div>

            @if (session()->has('message'))
                <div class="mt-6 p-4 bg-green-500/20 border border-green-500/40 text-green-400 rounded-xl">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mt-6 p-4 bg-red-500/20 border border-red-500/40 text-red-400 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-700/50">
            <h2 class="text-xl font-bold">Recent Sync Activity</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-slate-300">
                <thead>
                    <tr class="bg-slate-800/30">
                        <th class="px-6 py-4 font-semibold">Time</th>
                        <th class="px-6 py-4 font-semibold">Type</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                {{ $log->created_at->format('M d, H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="capitalize">{{ str_replace('_', ' ', $log->type) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($log->status === 'success')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                        Success
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                        Error
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm truncate max-w-xs">
                                {{ $log->message }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 italic">
                                No activity recorded yet. Run a sync to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6">
            {{ $logs->links() }}
        </div>
    </div>
</div>
