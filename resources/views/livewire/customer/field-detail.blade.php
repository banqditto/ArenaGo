<div class="space-y-6">
    <div>
        <flux:button 
            as="link" 
            href="{{ route('fields.index') }}" 
            variant="ghost" 
            icon="chevron-left" 
            class="-ms-3"
            wire:navigate
        >
            {{ __('Back to Fields') }}
        </flux:button>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Field Details (1 Column on Large Screens) -->
        <div class="space-y-6 lg:col-span-1">
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <!-- Field Image -->
                <div class="relative h-64 w-full bg-zinc-100 dark:bg-zinc-800">
                    @if($field->image_path)
                        <img src="{{ asset('storage/' . $field->image_path) }}" alt="{{ $field->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-zinc-400 dark:text-zinc-600">
                            @if($field->type === 'Futsal')
                                <flux:icon name="trophy" class="size-24" />
                            @elseif($field->type === 'Badminton')
                                <flux:icon name="sparkles" class="size-24" />
                            @elseif($field->type === 'Basketball')
                                <flux:icon name="bolt" class="size-24" />
                            @elseif($field->type === 'Mini Soccer')
                                <flux:icon name="trophy" class="size-24" />
                            @elseif($field->type === 'Padel')
                                <flux:icon name="sparkles" class="size-24" />
                            @elseif($field->type === 'Voli')
                                <flux:icon name="bolt" class="size-24" />
                            @else
                                <flux:icon name="sparkles" class="size-24" />
                            @endif
                        </div>
                    @endif
                    
                    <span class="absolute top-4 left-4 rounded-full px-3 py-1 text-xs font-semibold shadow-sm
                        @if($field->type === 'Futsal') bg-teal-50 text-teal-700 border border-teal-200 dark:bg-teal-950 dark:text-teal-300 dark:border-teal-900
                        @elseif($field->type === 'Badminton') bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-900
                        @elseif($field->type === 'Basketball') bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-950 dark:text-orange-300 dark:border-orange-900
                        @elseif($field->type === 'Mini Soccer') bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-900
                        @elseif($field->type === 'Padel') bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950 dark:text-indigo-300 dark:border-indigo-900
                        @elseif($field->type === 'Voli') bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950 dark:text-purple-300 dark:border-purple-900
                        @else bg-zinc-50 text-zinc-700 border border-zinc-200 dark:bg-zinc-950 dark:text-zinc-300 dark:border-zinc-900
                        @endif">
                        {{ $field->type }}
                    </span>
                </div>

                <!-- Field Info -->
                <div class="p-6">
                    <flux:heading level="2" size="xl" class="font-bold">{{ $field->name }}</flux:heading>
                    
                    @if($field->description)
                        <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            {{ $field->description }}
                        </p>
                    @endif
                    
                    <div class="mt-4 flex items-baseline gap-1 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        <span class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50">
                            Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}
                        </span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">/ {{ __('hour') }}</span>
                    </div>

                    <div class="mt-6 space-y-3 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50">
                        <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                            <flux:icon name="clock" class="size-4 text-zinc-400" />
                            <span>{{ __('Operational: 08:00 - 22:00') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                            <flux:icon name="information-circle" class="size-4 text-zinc-400" />
                            <span>{{ __('Duration: 1 to 5 hours max') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Booking Calendar (2 Columns on Large Screens) -->
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading level="2" size="lg" class="font-bold">{{ __('Select Booking Date & Slots') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Choose the date and consecutive hours you want to play.') }}</flux:subheading>

                <!-- Date Picker -->
                <div class="mt-6 max-w-sm">
                    <flux:input 
                        type="date" 
                        wire:model.live="selectedDate" 
                        label="{{ __('Booking Date') }}" 
                        min="{{ today()->format('Y-m-d') }}"
                    />
                </div>

                <!-- Calendar Status Legend -->
                <div class="mt-6 flex flex-wrap gap-4 border-t border-zinc-100 pt-6 dark:border-zinc-800">
                    <div class="flex items-center gap-2 text-xs text-zinc-500">
                        <span class="inline-block size-3 rounded border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800"></span>
                        <span>{{ __('Available') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-zinc-500">
                        <span class="inline-block size-3 rounded border border-teal-500 bg-teal-500"></span>
                        <span>{{ __('Selected') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-zinc-500">
                        <span class="inline-block size-3 rounded border border-rose-250 bg-rose-50 dark:border-rose-900/50 dark:bg-rose-950/20"></span>
                        <span>{{ __('Booked / Locked') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-zinc-500">
                        <span class="inline-block size-3 rounded border border-zinc-200 bg-zinc-150 dark:border-zinc-800 dark:bg-zinc-800/40"></span>
                        <span>{{ __('Past Hours') }}</span>
                    </div>
                </div>

                <!-- Hourly Slots Grid -->
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                    @foreach($hourlySlots as $slot)
                        @php
                            $isSelected = in_array($slot['start'], $selectedSlots);
                            $isBooked = $slot['status'] === 'booked';
                            $isPast = $slot['status'] === 'past';
                        @endphp
                        
                        <button 
                            type="button"
                            wire:click="selectSlot('{{ $slot['start'] }}')"
                            @disabled($isBooked || $isPast)
                            class="flex flex-col items-center justify-center rounded-lg border p-3 text-center transition-all duration-200
                            @if($isSelected)
                                bg-teal-600 border-teal-600 text-white font-semibold dark:bg-teal-50 dark:border-teal-50 dark:text-zinc-950 shadow-sm
                            @elseif($isBooked)
                                bg-rose-50 border-rose-100 text-rose-400 dark:bg-rose-950/20 dark:border-rose-900/30 dark:text-rose-600 cursor-not-allowed
                            @elseif($isPast)
                                bg-zinc-100 border-zinc-150 text-zinc-300 dark:bg-zinc-800/30 dark:border-zinc-800/30 dark:text-zinc-600 cursor-not-allowed
                            @else
                                bg-zinc-50 border-zinc-200 text-zinc-800 hover:bg-zinc-100 hover:border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-700 dark:hover:border-zinc-600
                            @endif"
                        >
                            <span class="text-sm font-medium">{{ $slot['start'] }} - {{ $slot['end'] }}</span>
                            <span class="mt-1 text-[10px] opacity-75">
                                @if($isSelected)
                                    {{ __('Selected') }}
                                @elseif($isBooked)
                                    {{ __('Booked/Locked') }}
                                @elseif($isPast)
                                    {{ __('Unavailable') }}
                                @else
                                    {{ __('Available') }}
                                @endif
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Booking Summary Card -->
                @if(!empty($selectedSlots))
                    @php
                        $hours = array_map(fn($s) => (int) substr($s, 0, 2), $selectedSlots);
                        sort($hours);
                        $startHour = min($hours);
                        $endHour = max($hours) + 1;
                        $startTime = sprintf('%02d:00', $startHour);
                        $endTime = sprintf('%02d:00', $endHour);
                        $duration = count($hours);
                        $totalPrice = $duration * $field->price_per_hour;
                    @endphp
                    
                    <div class="mt-8 rounded-xl border border-teal-100 bg-teal-50/30 p-5 dark:border-teal-900/50 dark:bg-teal-950/10">
                        <flux:heading level="3" class="font-bold text-teal-800 dark:text-teal-400">{{ __('Booking Summary') }}</flux:heading>
                        
                        <div class="mt-4 grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400 block">{{ __('Date') }}</span>
                                <span class="font-semibold text-zinc-950 dark:text-zinc-50">{{ \Illuminate\Support\Carbon::parse($selectedDate)->format('d F Y') }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400 block">{{ __('Time Slot') }}</span>
                                <span class="font-semibold text-zinc-950 dark:text-zinc-50">{{ $startTime }} - {{ $endTime }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400 block">{{ __('Duration') }}</span>
                                <span class="font-semibold text-zinc-950 dark:text-zinc-50">{{ $duration }} {{ __('hour(s)') }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400 block">{{ __('Total Price') }}</span>
                                <span class="font-bold text-teal-700 dark:text-teal-400 text-lg">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Info Expiry Alert -->
                        <div class="mt-4 flex items-start gap-2 text-xs text-teal-600 dark:text-teal-400">
                            <flux:icon name="information-circle" class="size-4 shrink-0" />
                            <span>{{ __('Clicking "Book Now" locks this slot for 10 minutes. If unpaid, it will automatically expire and release the slot.') }}</span>
                        </div>

                        <!-- Error Display -->
                        @error('booking')
                            <div class="mt-4 rounded-lg bg-rose-50 border border-rose-100 p-3 text-xs text-rose-700 font-semibold dark:bg-rose-950/10 dark:border-rose-900/30 dark:text-rose-400">
                                {{ $message }}
                            </div>
                        @enderror

                        <!-- Book Button -->
                        <div class="mt-6 flex justify-end">
                            <flux:button 
                                wire:click="bookNow" 
                                variant="primary" 
                                class="w-full sm:w-auto px-8"
                            >
                                {{ __('Book Now') }}
                            </flux:button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="border-t border-zinc-200 pt-8 dark:border-zinc-800">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <!-- Header of reviews section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-zinc-100 dark:border-zinc-800">
                <div>
                    <flux:heading level="2" size="lg" class="font-bold flex items-center gap-2">
                        <span>{{ __('Customer Reviews') }}</span>
                        <span class="rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ $this->reviews->count() }}
                        </span>
                    </flux:heading>
                    <flux:subheading class="mt-1">{{ __('Genuine reviews left by players who booked this court.') }}</flux:subheading>
                </div>
                
                <!-- Aggregate rating badge -->
                @if($this->reviews->isNotEmpty())
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1">
                            @php $avg = $field->average_rating; @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <flux:icon 
                                    name="star" 
                                    variant="{{ $avg >= $i ? 'solid' : ($avg >= $i - 0.5 ? 'solid' : 'outline') }}" 
                                    class="size-5 {{ $avg >= $i - 0.5 ? 'text-amber-400' : 'text-zinc-300 dark:text-zinc-650' }}" 
                                />
                            @endfor
                        </div>
                        <span class="text-lg font-bold text-zinc-900 dark:text-zinc-50">
                            {{ number_format($avg, 1) }} {{ __('out of 5') }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- List of reviews -->
            @if($this->reviews->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 text-center text-zinc-400 dark:text-zinc-600">
                    <flux:icon name="chat-bubble-bottom-center-text" class="size-12 mb-4" />
                    <flux:heading level="3" class="font-bold">{{ __('No Reviews Yet') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('This court does not have any reviews yet. Be the first to play and review!') }}</flux:text>
                </div>
            @else
                <div class="mt-6 space-y-6 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($this->reviews as $review)
                        <div class="pt-6 first:pt-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <flux:avatar 
                                        :name="$review->user->name" 
                                        :initials="$review->user->initials()" 
                                        size="sm"
                                    />
                                    <div>
                                        <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-50">{{ $review->user->name }}</h4>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $review->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                                
                                <!-- Rating Score -->
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <flux:icon 
                                            name="star" 
                                            variant="{{ $review->rating >= $i ? 'solid' : 'outline' }}" 
                                            class="size-4 {{ $review->rating >= $i ? 'text-amber-400' : 'text-zinc-300 dark:text-zinc-600' }}" 
                                        />
                                    @endfor
                                </div>
                            </div>
                            
                            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed font-normal">
                                {{ $review->comment }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
