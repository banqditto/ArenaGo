@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Arena Go" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md overflow-hidden bg-transparent">
            <img src="{{ asset('images/logo-arenago.png') }}" class="size-8 object-cover" alt="Arena Go Logo" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Arena Go" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md overflow-hidden bg-transparent">
            <img src="{{ asset('images/logo-arenago.png') }}" class="size-8 object-cover" alt="Arena Go Logo" />
        </x-slot>
    </flux:brand>
@endif
