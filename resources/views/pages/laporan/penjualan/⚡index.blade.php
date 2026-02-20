<?php

use App\Models\Penjualan;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Layout('layouts.app')]
#[\Livewire\Attributes\Title('Laporan Penjualan')]
class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $bulan = '';

    public function mount(): void
    {
        $this->bulan = now()->format('Y-m');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingBulan(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function summary()
    {
        $query = Penjualan::whereHas('unit', fn ($q) => $q->where('tenant_id', auth()->user()->tenant_id))
            ->when($this->bulan, fn ($q) =>
                $q->whereYear('tgl_jual', substr($this->bulan, 0, 4))
                  ->whereMonth('tgl_jual', substr($this->bulan, 5, 2))
            );

        return [
            'total_unit'    => (clone $query)->count(),
            'total_harga'   => (clone $query)->sum('harga_jual'),
            'total_tunai'   => (clone $query)->where('status_pembelian', 'tunai')->count(),
            'total_kredit'  => (clone $query)->where('status_pembelian', 'kredit')->count(),
        ];
    }

    #[Computed]
    public function penjualans()
    {
        return Penjualan::with(['unit.masterbarang.merek', 'unit.masterbarang.model', 'unit.unitdetail'])
            ->whereHas('unit', fn ($q) => $q->where('tenant_id', auth()->user()->tenant_id))
            ->when($this->bulan, fn ($q) =>
                $q->whereYear('tgl_jual', substr($this->bulan, 0, 4))
                  ->whereMonth('tgl_jual', substr($this->bulan, 5, 2))
            )
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('nama_konsumen', 'like', "%{$this->search}%")
                          ->orWhere('kontak', 'like', "%{$this->search}%")
                          ->orWhereHas('unit.masterbarang', fn ($q2) =>
                              $q2->where('nama_barang', 'like', "%{$this->search}%")
                          )
                          ->orWhereHas('unit.unitdetail', fn ($q2) =>
                              $q2->where('no_polisi', 'like', "%{$this->search}%")
                          );
                });
            })
            ->when($this->status, fn ($q) => $q->where('status_pembelian', $this->status))
            ->orderByDesc('tgl_jual')
            ->paginate(15);
    }
};
?>

<div>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Laporan Penjualan</flux:heading>
            <flux:subheading>Daftar seluruh data penjualan unit.</flux:subheading>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Unit Terjual</p>
            <p class="mt-1 text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $this->summary['total_unit'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Omzet</p>
            <p class="mt-1 text-xl font-bold text-zinc-800 dark:text-zinc-100">
                Rp {{ number_format($this->summary['total_harga'], 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Tunai</p>
            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->summary['total_tunai'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Kredit</p>
            <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->summary['total_kredit'] }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="w-full sm:w-44">
            <flux:input
                wire:model.live="bulan"
                type="month"
            />
        </div>
        <div class="flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama barang, no. polisi, konsumen, atau kontak..."
                icon="magnifying-glass"
            />
        </div>
        <div class="w-full sm:w-44">
            <flux:select wire:model.live="status" placeholder="Semua Status">
                <option value="">Semua Status</option>
                <option value="tunai">Tunai</option>
                <option value="kredit">Kredit</option>
            </flux:select>
        </div>
    </div>

    {{-- Table --}}
    <div class="mt-4 overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tgl Jual</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">No. Polisi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Konsumen</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Kontak</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Harga Jual</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Leasing</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse ($this->penjualans as $item)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                            {{ $this->penjualans->firstItem() + $loop->index }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->tgl_jual?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            <span class="font-medium">{{ $item->unit?->masterbarang?->nama_barang ?? '-' }}</span>
                            @if($item->unit?->masterbarang?->merek || $item->unit?->masterbarang?->model)
                                <br>
                                <span class="text-xs text-zinc-400">
                                    {{ implode(' · ', array_filter([
                                        $item->unit->masterbarang->merek?->nama,
                                        $item->unit->masterbarang->model?->nama,
                                    ])) }}
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-zinc-700 dark:text-zinc-300">
                            {{ $item->unit?->unitdetail?->no_polisi ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $item->nama_konsumen ?? '-' }}</p>
                            @if($item->alamat)
                                <p class="text-xs text-zinc-400">{{ $item->alamat }}</p>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->kontak ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 font-medium text-zinc-800 dark:text-zinc-200">
                            Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($item->status_pembelian) {
                                    'tunai'  => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                                    'kredit' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                                    'indent' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400',
                                    default  => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                                {{ ucfirst($item->status_pembelian ?? '-') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->leasing ?? '-' }}
                        </td>
                        <td class="max-w-xs px-4 py-3 text-zinc-500 dark:text-zinc-400">
                            {{ $item->catatan ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Belum ada data penjualan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($this->penjualans->hasPages())
        <div class="mt-4">
            {{ $this->penjualans->links() }}
        </div>
    @endif
</div>
