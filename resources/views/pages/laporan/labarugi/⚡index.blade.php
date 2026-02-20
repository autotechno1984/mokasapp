<?php

use App\Models\Biaya;
use App\Models\Penjualan;
use Livewire\Attributes\Computed;
use Livewire\Component;

new
#[\Livewire\Attributes\Layout('layouts.app')]
#[\Livewire\Attributes\Title('Laporan Laba Rugi')]
class extends Component
{
    public string $bulan = '';

    public function mount(): void
    {
        $this->bulan = now()->format('Y-m');
    }

    #[Computed]
    public function summary(): array
    {
        $tahun = $this->bulan ? substr($this->bulan, 0, 4) : now()->year;
        $bulan = $this->bulan ? substr($this->bulan, 5, 2) : now()->month;

        $penjualans = Penjualan::with(['unit.unitbiayas'])
            ->whereHas('unit', fn ($q) => $q->where('tenant_id', auth()->user()->tenant_id))
            ->whereYear('tgl_jual', $tahun)
            ->whereMonth('tgl_jual', $bulan)
            ->get();

        $totalHargaJual = $penjualans->sum('harga_jual');

        $totalModal = $penjualans->sum(function ($p) {
            $unit = $p->unit;
            if (! $unit) return 0;
            return ($unit->harga_beli ?? 0)
                 + ($unit->biaya ?? 0)
                 + $unit->unitbiayas->sum('amount');
        });

        $keuntunganJual = $totalHargaJual - $totalModal;

        $biayaOperasional = Biaya::where('tenant_id', auth()->user()->tenant_id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->sum('jumlah');

        return [
            'total_unit'         => $penjualans->count(),
            'total_harga_jual'   => $totalHargaJual,
            'total_modal'        => $totalModal,
            'keuntungan_jual'    => $keuntunganJual,
            'biaya_operasional'  => $biayaOperasional,
            'profit'             => $keuntunganJual - $biayaOperasional,
        ];
    }

    #[Computed]
    public function penjualans()
    {
        $tahun = $this->bulan ? substr($this->bulan, 0, 4) : now()->year;
        $bulan = $this->bulan ? substr($this->bulan, 5, 2) : now()->month;

        return Penjualan::with(['unit.masterbarang.merek', 'unit.masterbarang.model', 'unit.unitdetail', 'unit.unitbiayas'])
            ->whereHas('unit', fn ($q) => $q->where('tenant_id', auth()->user()->tenant_id))
            ->whereYear('tgl_jual', $tahun)
            ->whereMonth('tgl_jual', $bulan)
            ->orderByDesc('tgl_jual')
            ->get();
    }
};
?>

<div>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Laporan Laba Rugi</flux:heading>
            <flux:subheading>Rekap keuntungan, biaya, dan profit per bulan.</flux:subheading>
        </div>
        <div class="w-44">
            <flux:input wire:model.live="bulan" type="month" />
        </div>
    </div>

    {{-- Summary Cards (3 kolom) --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        {{-- Kolom 1: Keuntungan Jual Unit --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Keuntungan Jual Unit</p>
            <p class="mt-2 text-2xl font-bold text-zinc-800 dark:text-zinc-100">
                Rp {{ number_format($this->summary['keuntungan_jual'], 0, ',', '.') }}
            </p>
            <div class="mt-3 space-y-1 border-t border-zinc-100 pt-3 dark:border-zinc-700/50">
                <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span>Total Omzet</span>
                    <span>Rp {{ number_format($this->summary['total_harga_jual'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span>Total Modal Unit</span>
                    <span>Rp {{ number_format($this->summary['total_modal'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs font-medium text-zinc-600 dark:text-zinc-300">
                    <span>Unit Terjual</span>
                    <span>{{ $this->summary['total_unit'] }} unit</span>
                </div>
            </div>
        </div>

        {{-- Kolom 2: Biaya Operasional --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Biaya Operasional</p>
            <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">
                Rp {{ number_format($this->summary['biaya_operasional'], 0, ',', '.') }}
            </p>
            <div class="mt-3 border-t border-zinc-100 pt-3 dark:border-zinc-700/50">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    Total pengeluaran operasional bulan ini (gaji, sewa, listrik, dll).
                </p>
            </div>
        </div>

        {{-- Kolom 3: Profit --}}
        <div class="rounded-xl border p-5
            {{ $this->summary['profit'] >= 0
                ? 'border-green-200 bg-green-50 dark:border-green-700/50 dark:bg-green-900/20'
                : 'border-red-200 bg-red-50 dark:border-red-700/50 dark:bg-red-900/20' }}">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Profit Bulan Ini</p>
            <p class="mt-2 text-2xl font-bold
                {{ $this->summary['profit'] >= 0
                    ? 'text-green-700 dark:text-green-400'
                    : 'text-red-700 dark:text-red-400' }}">
                Rp {{ number_format($this->summary['profit'], 0, ',', '.') }}
            </p>
            <div class="mt-3 space-y-1 border-t border-zinc-100 pt-3 dark:border-zinc-700/50">
                <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span>Keuntungan Jual</span>
                    <span>Rp {{ number_format($this->summary['keuntungan_jual'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span>Biaya Operasional</span>
                    <span class="text-red-500">- Rp {{ number_format($this->summary['biaya_operasional'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail: Mobile Card List --}}
    <div class="mt-6 space-y-3 sm:hidden">
        @forelse ($this->penjualans as $i => $item)
            @php
                $unit        = $item->unit;
                $hargaBeli   = $unit?->harga_beli ?? 0;
                $biayaUnit   = $unit?->biaya ?? 0;
                $biayaDetail = $unit?->unitbiayas->sum('amount') ?? 0;
                $modal       = $hargaBeli + $biayaUnit + $biayaDetail;
                $keuntungan  = $item->harga_jual - $modal;
            @endphp
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-semibold text-zinc-800 dark:text-zinc-100">
                            {{ $unit?->masterbarang?->nama_barang ?? '-' }}
                        </p>
                        @if($unit?->masterbarang?->merek || $unit?->masterbarang?->model)
                            <p class="text-xs text-zinc-400">
                                {{ implode(' · ', array_filter([
                                    $unit->masterbarang->merek?->nama,
                                    $unit->masterbarang->model?->nama,
                                ])) }}
                            </p>
                        @endif
                        <p class="mt-0.5 font-mono text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $unit?->unitdetail?->no_polisi ?? '-' }}
                        </p>
                    </div>
                    <span class="shrink-0 text-xs text-zinc-400 dark:text-zinc-500">
                        {{ $item->tgl_jual?->format('d/m/Y') ?? '-' }}
                    </span>
                </div>
                <div class="mt-3 grid grid-cols-3 gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-700/50">
                    <div>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Harga Jual</p>
                        <p class="mt-0.5 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                            Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Modal</p>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                            Rp {{ number_format($modal, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Keuntungan</p>
                        <p class="mt-0.5 text-sm font-bold {{ $keuntungan >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($keuntungan, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-zinc-200 bg-white py-10 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-400 dark:text-zinc-500">Belum ada data penjualan bulan ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Detail Table (desktop) --}}
    <div class="mt-6 hidden overflow-x-auto rounded-xl border border-zinc-200 sm:block dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tgl Jual</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">No. Polisi</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Harga Jual</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Modal</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Keuntungan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse ($this->penjualans as $i => $item)
                    @php
                        $unit        = $item->unit;
                        $hargaBeli   = $unit?->harga_beli ?? 0;
                        $biayaUnit   = $unit?->biaya ?? 0;
                        $biayaDetail = $unit?->unitbiayas->sum('amount') ?? 0;
                        $modal       = $hargaBeli + $biayaUnit + $biayaDetail;
                        $keuntungan  = $item->harga_jual - $modal;
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $i + 1 }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->tgl_jual?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            <span class="font-medium">{{ $unit?->masterbarang?->nama_barang ?? '-' }}</span>
                            @if($unit?->masterbarang?->merek || $unit?->masterbarang?->model)
                                <br>
                                <span class="text-xs text-zinc-400">
                                    {{ implode(' · ', array_filter([
                                        $unit->masterbarang->merek?->nama,
                                        $unit->masterbarang->model?->nama,
                                    ])) }}
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-zinc-700 dark:text-zinc-300">
                            {{ $unit?->unitdetail?->no_polisi ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-zinc-800 dark:text-zinc-200">
                            Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-zinc-500 dark:text-zinc-400">
                            Rp {{ number_format($modal, 0, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold
                            {{ $keuntungan >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($keuntungan, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Belum ada data penjualan bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($this->penjualans->count() > 0)
            <tfoot class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Total</td>
                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-zinc-800 dark:text-zinc-200">
                        Rp {{ number_format($this->summary['total_harga_jual'], 0, ',', '.') }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-zinc-500 dark:text-zinc-400">
                        Rp {{ number_format($this->summary['total_modal'], 0, ',', '.') }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold
                        {{ $this->summary['keuntungan_jual'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        Rp {{ number_format($this->summary['keuntungan_jual'], 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
