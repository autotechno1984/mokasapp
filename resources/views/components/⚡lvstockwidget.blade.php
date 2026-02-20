<?php

use App\Models\Unit;
use Livewire\Component;

new class extends Component
{
    protected function baseQuery()
    {
        $user = auth()->user();

        if (! $user) {
            return Unit::query()->whereRaw('1 = 0');
        }

        return Unit::query()
            ->where('tenant_id', $user->tenant_id);
    }

    public function getTotalStockProperty(): int
    {
        return (clone $this->baseQuery())
            ->whereNull('tgl_jual')
            ->count();
    }

    public function getSoldThisMonthProperty(): int
    {
        return (clone $this->baseQuery())
            ->whereYear('tgl_jual', now()->year)
            ->whereMonth('tgl_jual', now()->month)
            ->count();
    }

    public function getStockThisMonthProperty(): int
    {
        return (clone $this->baseQuery())
            ->whereYear('tgl_beli', now()->year)
            ->whereMonth('tgl_beli', now()->month)
            ->count();
    }

    public function getStatusCountsProperty(): array
    {
        return (clone $this->baseQuery())
            ->whereNull('tgl_jual')
            ->selectRaw("status, count(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    public function getTitipCountProperty(): int
    {
        return (clone $this->baseQuery())
            ->whereNull('tgl_jual')
            ->where('unit_titip', true)
            ->count();
    }
};
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
    {{-- Card: Stok Tersedia --}}
    <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center justify-center size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <flux:icon.cube class="size-5 text-blue-600 dark:text-blue-400" />
            </div>
            <flux:text size="sm" class="font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Stok Tersedia</flux:text>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-4xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->totalStock }}</span>
            <span class="text-sm text-zinc-500 dark:text-zinc-400">unit</span>
        </div>
        @if($this->titipCount > 0)
            <div class="mt-2">
                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full">
                    {{ $this->titipCount }} unit titipan
                </span>
            </div>
        @endif
        @if(count($this->statusCounts) > 0)
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($this->statusCounts as $status => $count)
                    @php
                        $colors = match($status) {
                            'siap-jual' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
                            'perbaikan' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                            'sewa' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
                            'tahan' => 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                            default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400',
                        };
                        $label = match($status) {
                            'siap-jual' => 'Siap Jual',
                            'perbaikan' => 'Perbaikan',
                            'sewa' => 'Sewa',
                            'tahan' => 'Tahan',
                            default => ucfirst($status),
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full {{ $colors }}">
                        {{ $count }} {{ $label }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Card: Terjual Bulan Ini --}}
    <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center justify-center size-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                <flux:icon.shopping-cart class="size-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <flux:text size="sm" class="font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Terjual Bulan Ini</flux:text>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-4xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->soldThisMonth }}</span>
            <span class="text-sm text-zinc-500 dark:text-zinc-400">unit</span>
        </div>
        <div class="mt-2">
            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ now()->translatedFormat('F Y') }}</span>
        </div>
    </div>

    {{-- Card: Masuk Bulan Ini --}}
    <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center justify-center size-10 rounded-lg bg-violet-100 dark:bg-violet-900/30">
                <flux:icon.arrow-down-tray class="size-5 text-violet-600 dark:text-violet-400" />
            </div>
            <flux:text size="sm" class="font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Masuk Bulan Ini</flux:text>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-4xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->stockThisMonth }}</span>
            <span class="text-sm text-zinc-500 dark:text-zinc-400">unit</span>
        </div>
        <div class="mt-2">
            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ now()->translatedFormat('F Y') }}</span>
        </div>
    </div>
</div>
