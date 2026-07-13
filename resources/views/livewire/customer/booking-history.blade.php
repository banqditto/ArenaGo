<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('My Bookings') }}</flux:heading>
        <flux:subheading>{{ __('View your past and active sports field reservations') }}</flux:subheading>
    </div>

    @if($bookings->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 p-12 text-center dark:border-zinc-700">
            <flux:icon name="calendar" class="size-12 text-zinc-400 dark:text-zinc-600" />
            <flux:heading level="3" class="mt-4">{{ __('No bookings found') }}</flux:heading>
            <flux:subheading class="mt-2">{{ __('You haven\'t made any bookings yet. Head over to the Fields Directory to book a field.') }}</flux:subheading>
            <div class="mt-6">
                <flux:button as="link" href="{{ route('fields.index') }}" variant="primary" wire:navigate>
                    {{ __('Browse Fields') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm text-zinc-600 dark:text-zinc-300">
                    <thead class="bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 border-b border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700">
                        <tr>
                            <th class="px-6 py-4">{{ __('Field') }}</th>
                            <th class="px-6 py-4">{{ __('Booking Date') }}</th>
                            <th class="px-6 py-4">{{ __('Time Slot') }}</th>
                            <th class="px-6 py-4">{{ __('Total Price') }}</th>
                            <th class="px-6 py-4">{{ __('Status') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20">
                                <!-- Field info -->
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-50">
                                    <div class="flex flex-col">
                                        <span>{{ $booking->field->name }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $booking->field->type }}</span>
                                    </div>
                                </td>
                                
                                <!-- Booking Date -->
                                <td class="px-6 py-4">
                                    {{ $booking->booking_date->format('d F Y') }}
                                </td>
                                
                                <!-- Time Slot -->
                                <td class="px-6 py-4">
                                    {{ $booking->start_time }} - {{ $booking->end_time }}
                                </td>
                                
                                <!-- Total Price -->
                                <td class="px-6 py-4 font-semibold">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </td>
                                
                                <!-- Status Badge -->
                                <td class="px-6 py-4">
                                    @php
                                        $isPending = $booking->status === 'pending';
                                        $isExpired = $booking->status === 'expired' || ($isPending && $booking->expires_at->isPast());
                                        $isPaid = $booking->status === 'paid';
                                        $isCancelled = $booking->status === 'cancelled';
                                    @endphp
                                    
                                    @if($isPaid)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50">
                                            {{ __('Paid') }}
                                        </span>
                                    @elseif($isCancelled)
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 border border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/50">
                                            {{ __('Cancelled') }}
                                        </span>
                                    @elseif($isExpired)
                                        <span class="inline-flex items-center rounded-full bg-zinc-50 px-2.5 py-0.5 text-xs font-semibold text-zinc-500 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                            {{ __('Expired') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 border border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/50">
                                            {{ __('Pending Payment') }}
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($booking->status === 'pending' && $booking->expires_at->isFuture())
                                            <flux:button 
                                                as="link" 
                                                href="{{ route('checkout', $booking) }}" 
                                                variant="primary" 
                                                size="sm"
                                                wire:navigate
                                            >
                                                {{ __('Pay Now') }}
                                            </flux:button>
                                        @elseif($booking->status === 'paid')
                                            @if($booking->review)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                                    <flux:icon name="check-circle" class="size-4" />
                                                    {{ __('Reviewed') }}
                                                </span>
                                            @else
                                                <flux:button 
                                                    type="button" 
                                                    size="sm" 
                                                    variant="outline" 
                                                    wire:click="$dispatch('openReviewModal', { bookingId: {{ $booking->id }} })"
                                                >
                                                    {{ __('Leave Review') }}
                                                </flux:button>
                                            @endif
                                        @else
                                            <span class="text-xs text-zinc-400 dark:text-zinc-600">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $bookings->links() }}
        </div>
    @endif

    <livewire:customer.leave-review />
</div>
