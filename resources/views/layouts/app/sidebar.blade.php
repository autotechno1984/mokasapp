<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800" container>
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Menu Utama')" class="grid">
                    @if(auth()->user()->isOwner())
                        <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="square-2-stack" href="/unit-create" :current="request()->routeIs('unit.create')" wire:navigate>
                            {{ __('Unit') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="banknotes" :href="route('biaya.index')" :current="request()->routeIs('biaya.index')" wire:navigate>
                            {{ __('Biaya') }}
                        </flux:sidebar.item>
                        <flux:sidebar.group expandable heading="{{ __('Laporan') }}" icon="shopping-bag">
                            <flux:sidebar.item :href="route('laporan.penjualan')" :current="request()->routeIs('laporan.penjualan')" wire:navigate>
                                {{ __('Laporan Penjualan') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item :href="route('laporan.stock')" :current="request()->routeIs('laporan.stock')" wire:navigate>
                                {{ __('Laporan Stok') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item :href="route('laporan.labarugi')" :current="request()->routeIs('laporan.labarugi')" wire:navigate>
                                {{ __('Laporan Laba Rugi') }}
                            </flux:sidebar.item>
                        </flux:sidebar.group>
                        <flux:sidebar.item icon="users" :href="route('setting.users')" :current="request()->routeIs('setting.users')" wire:navigate>
                            {{ __('Manajemen User') }}
                        </flux:sidebar.item>
                    @else

                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            {{-- Trial notification (desktop sidebar) --}}
            @if(tenant() && tenant()->status === 'trial')
                @php
                    $trialEndsAt  = \Carbon\Carbon::parse(tenant()->data['trial_ends_at'] ?? null);
                    $sisaHari     = (int) now()->startOfDay()->diffInDays($trialEndsAt->startOfDay(), false);
                    $isKritis     = $sisaHari <= 3;
                @endphp
                <div class="mx-2 mb-2 rounded-lg px-3 py-2.5 {{ $isKritis ? 'bg-red-50 dark:bg-red-900/20' : 'bg-amber-50 dark:bg-amber-900/20' }}">
                    <div class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 {{ $isKritis ? 'text-red-500' : 'text-amber-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <div>
                            <p class="text-xs font-semibold {{ $isKritis ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400' }}">
                                @if($sisaHari < 0)
                                    Trial telah berakhir
                                @elseif($sisaHari === 0)
                                    Trial berakhir hari ini
                                @else
                                    Trial berakhir {{ $sisaHari }} hari lagi
                                @endif
                            </p>
                            <p class="mt-0.5 text-xs {{ $isKritis ? 'text-red-500 dark:text-red-500' : 'text-amber-500 dark:text-amber-500' }}">
                                s/d {{ $trialEndsAt->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    {{-- Trial notification (mobile dropdown) --}}
                    @if(tenant() && tenant()->status === 'trial')
                        @php
                            $trialEndsAt  = \Carbon\Carbon::parse(tenant()->data['trial_ends_at'] ?? null);
                            $sisaHari     = (int) now()->startOfDay()->diffInDays($trialEndsAt->startOfDay(), false);
                            $isKritis     = $sisaHari <= 3;
                        @endphp
                        <div class="mx-1 my-1 rounded-lg px-3 py-2.5 {{ $isKritis ? 'bg-red-50 dark:bg-red-900/20' : 'bg-amber-50 dark:bg-amber-900/20' }}">
                            <div class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 {{ $isKritis ? 'text-red-500' : 'text-amber-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                                <div>
                                    <p class="text-xs font-semibold {{ $isKritis ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400' }}">
                                        @if($sisaHari < 0)
                                            Trial telah berakhir
                                        @elseif($sisaHari === 0)
                                            Trial berakhir hari ini
                                        @else
                                            Trial berakhir {{ $sisaHari }} hari lagi
                                        @endif
                                    </p>
                                    <p class="mt-0.5 text-xs {{ $isKritis ? 'text-red-500 dark:text-red-500' : 'text-amber-500 dark:text-amber-500' }}">
                                        s/d {{ $trialEndsAt->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <flux:menu.separator />
                    @endif

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main>
            {{ $slot }}
        </flux:main>

        <tallstackui:toast />

        @fluxScripts
    </body>
</html>
