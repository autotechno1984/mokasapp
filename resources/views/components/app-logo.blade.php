@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="" {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ asset('img/logo-mokasapp.png') }}" alt="MokasApp" class="h-8 w-auto">
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="" {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ asset('img/logo-mokasapp.png') }}" alt="MokasApp" class="h-8 w-auto">
        </x-slot>
    </flux:brand>
@endif
