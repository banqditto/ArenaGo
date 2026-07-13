<div class="space-y-6">
    <div>
        <flux:heading level="1" size="xl" class="font-bold">{{ __('All User Bookings') }}</flux:heading>
        <flux:subheading class="mt-1">{{ __('Read-only list of all user field reservations and their transaction states.') }}</flux:subheading>
    </div>

    <!-- Bookings Table Card -->
    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900 overflow-hidden">
        @if($bookings->isEmpty())
            <div class="flex flex-col items-center justify-center p-12 text-center text-zinc-400 dark:text-zinc-600">
                <flux:icon name="ticket" class="size-16 mb-4" />
                <flux:heading level="3" class="font-bold">{{ __('No Bookings Found') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Users have not reserved any fields yet.') }}</flux:text>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-zinc-150 bg-zinc-50/50 text-xs font-semibold text-zinc-500 uppercase tracking-wider dark:border-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-400">
                            <th class="px-6 py-4">{{ __('ID/Code') }}</th>
                            <th class="px-6 py-4">{{ __('Customer') }}</th>
                            <th class="px-6 py-4">{{ __('Field Reserved') }}</th>
                            <th class="px-6 py-4">{{ __('Schedule') }}</th>
                            <th class="px-6 py-4">{{ __('Total Price') }}</th>
                            <th class="px-6 py-4">{{ __('Payment Status') }}</th>
                            <th class="px-6 py-4">{{ __('Booked At') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-150 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                        @foreach($bookings as $item)
                            <tr class="hover:bg-zinc-50/30 dark:hover:bg-zinc-800/20 transition-colors duration-150">
                                <!-- ID Column -->
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                                    #{{ sprintf('%05d', $item->id) }}
                                </td>
                                <!-- Customer Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $item->user->name }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->user->email }}</span>
                                    </div>
                                </td>
                                <!-- Field Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $item->field->name }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->field->type }}</span>
                                    </div>
                                </td>
                                <!-- Schedule Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col text-xs">
                                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $item->booking_date->format('d M Y') }}</span>
                                        <span class="text-zinc-500 dark:text-zinc-400">{{ $item->start_time }} - {{ $item->end_time }}</span>
                                    </div>
                                </td>
                                <!-- Price Column -->
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-zinc-900 dark:text-zinc-50">
                                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                </td>
                                <!-- Status Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold border
                                        @if($item->status === 'paid')
                                            bg-emerald-50 text-emerald-700 border-emerald-250 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50
                                        @elseif($item->status === 'pending')
                                            bg-amber-50 text-amber-700 border-amber-250 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/50
                                        @elseif($item->status === 'expired')
                                            bg-zinc-50 text-zinc-550 border-zinc-200 dark:bg-zinc-800/40 dark:text-zinc-400 dark:border-zinc-700/60
                                        @else
                                            bg-rose-50 text-rose-700 border-rose-250 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/50
                                        @endif">
                                        @if($item->status === 'paid')
                                            {{ __('Paid') }}
                                        @elseif($item->status === 'pending')
                                            {{ __('Pending') }}
                                        @elseif($item->status === 'expired')
                                            {{ __('Expired') }}
                                        @else
                                            {{ __('Cancelled') }}
                                        @endif
                                    </span>
                                </td>
                                <!-- Booked At Column -->
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $item->created_at->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination Footer -->
            <div class="px-6 py-4 border-t border-zinc-150 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-900/20">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
