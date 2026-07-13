<div class="space-y-6">
    <div>
        <flux:heading level="1" size="xl" class="font-bold">{{ __('Admin Dashboard') }}</flux:heading>
        <flux:subheading class="mt-1">{{ __('Overview of sports arena operations, bookings, and revenue.') }}</flux:subheading>
    </div>

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Revenue Card -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">
                    <flux:icon name="banknotes" class="size-6" />
                </div>
                <div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Revenue') }}</span>
                    <flux:heading level="2" size="xl" class="font-extrabold mt-1">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </flux:heading>
                </div>
            </div>
        </div>

        <!-- Fields Card -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-blue-50 p-3 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">
                    <flux:icon name="cube" class="size-6" />
                </div>
                <div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Fields') }}</span>
                    <flux:heading level="2" size="xl" class="font-extrabold mt-1">
                        {{ $totalFields }}
                    </flux:heading>
                </div>
            </div>
        </div>

        <!-- Active Bookings Card -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-amber-50 p-3 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400">
                    <flux:icon name="ticket" class="size-6" />
                </div>
                <div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Active Bookings') }}</span>
                    <flux:heading level="2" size="xl" class="font-extrabold mt-1">
                        {{ $totalActiveBookings }}
                    </flux:heading>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Short info about system status -->
    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading level="3" class="font-bold mb-4">{{ __('Quick Actions') }}</flux:heading>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <flux:button as="link" href="{{ route('admin.fields') }}" variant="primary" icon="plus" wire:navigate>
                {{ __('Manage Fields CRUD') }}
            </flux:button>
            <flux:button as="link" href="{{ route('admin.bookings') }}" variant="outline" icon="document-text" wire:navigate>
                {{ __('View All Bookings') }}
            </flux:button>
        </div>
    </div>
</div>
