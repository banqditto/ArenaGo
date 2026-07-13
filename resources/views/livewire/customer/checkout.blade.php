<div @if($timeLeft > 0 && !$errorMessage) wire:poll.2s="updateTimer" @endif class="mx-auto max-w-lg space-y-6">
    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        @if($errorMessage)
            <div class="text-center">
                <flux:heading level="1" size="xl" class="font-bold text-rose-600 dark:text-rose-400">{{ __('Payment Initialization Failed') }}</flux:heading>
                <flux:subheading class="mt-2">{{ __('There was a problem initiating your payment with Midtrans.') }}</flux:subheading>
            </div>

            <div class="mt-6 rounded-lg bg-rose-50 border border-rose-100 p-4 text-sm text-rose-700 dark:bg-rose-950/10 dark:border-rose-900/30 dark:text-rose-400">
                <p class="font-bold mb-2">{{ __('Error Details:') }}</p>
                <p class="font-mono text-xs bg-rose-100/50 p-3 rounded border border-rose-200/50 dark:bg-rose-950/30 dark:border-rose-900/40">{{ $errorMessage }}</p>
                
                <div class="mt-6 flex justify-center">
                    <flux:button as="link" href="{{ route('dashboard') }}" variant="danger" wire:navigate>
                        {{ __('Return to Dashboard') }}
                    </flux:button>
                </div>
            </div>
        @else
            <div class="text-center">
                <flux:heading level="1" size="xl" class="font-bold">{{ __('Complete Your Payment') }}</flux:heading>
                <flux:subheading class="mt-2">{{ __('Scan the QRIS code below using your mobile banking or e-wallet') }}</flux:subheading>
            </div>

            <!-- Timer Section -->
            @php
                $minutes = floor($timeLeft / 60);
                $seconds = $timeLeft % 60;
            @endphp
            <div class="mt-6 flex flex-col items-center justify-center rounded-lg bg-rose-50/50 p-4 border border-rose-100 text-center dark:bg-rose-950/10 dark:border-rose-900/30">
                <span class="text-xs font-semibold text-rose-700 dark:text-rose-400 uppercase tracking-wider">{{ __('Payment Countdown') }}</span>
                <span class="mt-1 text-3xl font-extrabold text-rose-800 dark:text-rose-400">
                    {{ sprintf('%02d', $minutes) }}:{{ sprintf('%02d', $seconds) }}
                </span>
                <span class="mt-2 text-xs text-rose-600 dark:text-rose-500">
                    {{ __('Your booking lock will release if unpaid within the remaining time.') }}
                </span>
            </div>

            <!-- QRIS Display -->
            <div class="mt-8 flex flex-col items-center justify-center">
                @if($qrisUrl)
                    <div class="rounded-xl border-4 border-white bg-white p-3 shadow-md">
                        <img src="{{ $qrisUrl }}" alt="{{ __('QRIS Code') }}" class="size-64 object-contain">
                    </div>
                @else
                    <div class="flex size-64 items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                        <flux:icon name="sparkles" class="size-10 animate-spin text-zinc-400" />
                    </div>
                @endif
                
                <div class="mt-4 flex items-center gap-2">
                    <span class="inline-block size-2 animate-ping rounded-full bg-emerald-500"></span>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Waiting for payment settlement...') }}</span>
                </div>
            </div>
        @endif

        <!-- Order Summary Card -->
        <div class="mt-8 border-t border-zinc-100 pt-6 dark:border-zinc-800">
            <flux:heading level="3" class="font-bold mb-4">{{ __('Order Details') }}</flux:heading>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Field Name') }}</span>
                    <span class="font-semibold text-zinc-950 dark:text-zinc-50">{{ $booking->field->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Booking Date') }}</span>
                    <span class="font-semibold text-zinc-950 dark:text-zinc-50">{{ $booking->booking_date->format('d F Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Time Slot') }}</span>
                    <span class="font-semibold text-zinc-950 dark:text-zinc-50">{{ $booking->start_time }} - {{ $booking->end_time }}</span>
                </div>
                <div class="flex justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800 text-base">
                    <span class="font-bold text-zinc-900 dark:text-zinc-50">{{ __('Total Price') }}</span>
                    <span class="font-extrabold text-teal-600 dark:text-teal-400">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Local Simulation Button -->
        @if(app()->environment('local'))
            <div class="mt-8 border-t border-dashed border-zinc-200 pt-6 dark:border-zinc-800">
                <div class="rounded-lg bg-zinc-50 border border-zinc-200 p-4 dark:bg-zinc-800/40 dark:border-zinc-700/60">
                    <div class="flex items-center gap-2 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                        <flux:icon name="bolt" class="size-4 text-amber-500" />
                        <span>{{ __('Developer Tools (Local Only)') }}</span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-4">
                        {{ __('Bypass Midtrans webhooks and simulate a successful payment instantly to test database updates.') }}
                    </p>
                    <flux:button 
                        type="button" 
                        wire:click="simulatePayment" 
                        variant="filled" 
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold dark:bg-amber-600 dark:hover:bg-amber-700"
                    >
                        {{ __('Simulate Successful Payment') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</div>
