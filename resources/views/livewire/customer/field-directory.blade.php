<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Fields Directory') }}</flux:heading>
            <flux:subheading>{{ __('Browse and book available sports fields') }}</flux:subheading>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                icon="magnifying-glass" 
                placeholder="{{ __('Search fields by name...') }}" 
                class="w-full"
            />
        </div>
        <div class="w-full sm:w-48">
            <flux:select wire:model.live="type" placeholder="{{ __('Filter by Type') }}">
                <flux:select.option value="">{{ __('All Types') }}</flux:select.option>
                <flux:select.option value="Futsal">{{ __('Futsal') }}</flux:select.option>
                <flux:select.option value="Badminton">{{ __('Badminton') }}</flux:select.option>
                <flux:select.option value="Basketball">{{ __('Basketball') }}</flux:select.option>
                <flux:select.option value="Mini Soccer">{{ __('Mini Soccer') }}</flux:select.option>
                <flux:select.option value="Padel">{{ __('Padel') }}</flux:select.option>
                <flux:select.option value="Voli">{{ __('Voli') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Fields Grid -->
    @if($fields->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 p-12 text-center dark:border-zinc-700">
            <flux:icon name="face-frown" class="size-12 text-zinc-400 dark:text-zinc-600" />
            <flux:heading level="3" class="mt-4">{{ __('No fields found') }}</flux:heading>
            <flux:subheading class="mt-2">{{ __('Try adjusting your search query or filters.') }}</flux:subheading>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($fields as $field)
                <div class="flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                    <!-- Image Wrapper -->
                    <div class="relative h-48 w-full bg-zinc-100 dark:bg-zinc-800">
                        @if($field->image_path)
                            <img src="{{ asset('storage/' . $field->image_path) }}" alt="{{ $field->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-zinc-400 dark:text-zinc-600">
                                @if($field->type === 'Futsal')
                                    <flux:icon name="trophy" class="size-16" />
                                  @elseif($field->type === 'Badminton')
                                    <flux:icon name="sparkles" class="size-16" />
                                  @elseif($field->type === 'Basketball')
                                    <flux:icon name="bolt" class="size-16" />
                                  @elseif($field->type === 'Mini Soccer')
                                    <flux:icon name="trophy" class="size-16" />
                                  @elseif($field->type === 'Padel')
                                    <flux:icon name="sparkles" class="size-16" />
                                  @elseif($field->type === 'Voli')
                                    <flux:icon name="bolt" class="size-16" />
                                  @else
                                    <flux:icon name="sparkles" class="size-16" />
                                @endif
                            </div>
                        @endif
                        
                        <!-- Floating Badge -->
                        <span class="absolute top-3 left-3 rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-sm
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

                    <!-- Details -->
                    <div class="flex flex-1 flex-col p-5">
                        <flux:heading level="3" size="lg" class="font-bold">{{ $field->name }}</flux:heading>
                        
                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                                Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}
                            </span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">/ {{ __('hour') }}</span>
                        </div>

                        <div class="mt-6">
                            <flux:button 
                                as="link" 
                                href="{{ route('fields.show', $field) }}" 
                                variant="primary" 
                                class="w-full text-center"
                                wire:navigate
                            >
                                {{ __('Book Now') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $fields->links() }}
        </div>
    @endif
</div>
