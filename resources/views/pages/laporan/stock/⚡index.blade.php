<?php

use App\Models\Penjualan;
use App\Models\Unit;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Layout('layouts.app')]
#[\Livewire\Attributes\Title('Laporan Stok')]
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
        // Stok kumulatif: unit yang masuk s.d. akhir bulan & belum terjual (atau terjual setelah bulan itu)
        $endOfMonth = $this->bulan ? Carbon::parse($this->bulan)->endOfMonth() : null;

        $stok = Unit::where('tenant_id', auth()->user()->tenant_id)
            ->when($endOfMonth, function ($q) use ($endOfMonth) {
                $q->where('tgl_beli', '<=', $endOfMonth)
                  ->where(function ($inner) use ($endOfMonth) {
                      $inner->whereNull('tgl_jual')
                            ->orWhere('tgl_jual', '>', $endOfMonth);
                  });
            }, fn ($q) => $q->whereNull('tgl_jual'));

        // Pembelian bulan ini saja
        $beli = Unit::where('tenant_id', auth()->user()->tenant_id)
            ->when($this->bulan, fn ($q) =>
                $q->whereYear('tgl_beli', substr($this->bulan, 0, 4))
                  ->whereMonth('tgl_beli', substr($this->bulan, 5, 2))
            );

        // Terjual bulan ini saja
        $jual = Penjualan::where('tenant_id', auth()->user()->tenant_id)
            ->when($this->bulan, fn ($q) =>
                $q->whereYear('tgl_jual', substr($this->bulan, 0, 4))
                  ->whereMonth('tgl_jual', substr($this->bulan, 5, 2))
            );

        return [
            'total_unit'      => (clone $stok)->count(),
            'nilai_stok'      => (clone $stok)->selectRaw('SUM(harga_beli + biaya) as total')->value('total') ?? 0,
            'siap_jual'       => (clone $stok)->where('status', 'siap-jual')->count(),
            'nilai_siap_jual' => (clone $stok)->where('status', 'siap-jual')->selectRaw('SUM(harga_beli + biaya) as total')->value('total') ?? 0,
            'perbaikan'       => (clone $stok)->where('status', 'perbaikan')->count(),
            'nilai_perbaikan' => (clone $stok)->where('status', 'perbaikan')->selectRaw('SUM(harga_beli + biaya) as total')->value('total') ?? 0,
            'pembelian_unit'  => (clone $beli)->count(),
            'nilai_pembelian' => (clone $beli)->selectRaw('SUM(harga_beli) as total')->value('total') ?? 0,
            'terjual'         => (clone $jual)->count(),
            'nilai_terjual'   => (clone $jual)->sum('harga_jual'),
        ];
    }

    #[Computed]
    public function units()
    {
        $endOfMonth = $this->bulan ? Carbon::parse($this->bulan)->endOfMonth() : null;

        return Unit::with(['masterbarang.merek', 'masterbarang.tipe', 'unitdetail', 'gudang'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($endOfMonth, function ($q) use ($endOfMonth) {
                $q->where('tgl_beli', '<=', $endOfMonth)
                  ->where(function ($inner) use ($endOfMonth) {
                      $inner->whereNull('tgl_jual')
                            ->orWhere('tgl_jual', '>', $endOfMonth);
                  });
            }, fn ($q) => $q->whereNull('tgl_jual'))
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereHas('masterbarang', fn ($q2) =>
                              $q2->where('nama_barang', 'like', "%{$this->search}%")
                          )
                          ->orWhereHas('unitdetail', fn ($q2) =>
                              $q2->where('no_polisi', 'like', "%{$this->search}%")
                                 ->orWhere('no_mesin', 'like', "%{$this->search}%")
                                 ->orWhere('no_rangka', 'like', "%{$this->search}%")
                          );
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('tgl_beli')
            ->paginate(15);
    }
};
?>

<div>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Laporan Stok</flux:heading>
            <flux:subheading>Daftar seluruh data stok unit berdasarkan tanggal beli.</flux:subheading>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
        {{-- Total Stok --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Stok</p>
            <p class="mt-1 text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $this->summary['total_unit'] }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                Rp {{ number_format($this->summary['nilai_stok'], 0, ',', '.') }}
            </p>
        </div>
        {{-- Siap Jual --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Siap Jual</p>
            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->summary['siap_jual'] }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                Rp {{ number_format($this->summary['nilai_siap_jual'], 0, ',', '.') }}
            </p>
        </div>
        {{-- Perbaikan --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Perbaikan</p>
            <p class="mt-1 text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $this->summary['perbaikan'] }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                Rp {{ number_format($this->summary['nilai_perbaikan'], 0, ',', '.') }}
            </p>
        </div>
        {{-- Pembelian --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Pembelian</p>
            <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->summary['pembelian_unit'] }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                Rp {{ number_format($this->summary['nilai_pembelian'], 0, ',', '.') }}
            </p>
        </div>
        {{-- Terjual --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Terjual</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $this->summary['terjual'] }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                Rp {{ number_format($this->summary['nilai_terjual'], 0, ',', '.') }}
            </p>
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
                placeholder="Cari nama barang, no. polisi, no. mesin, atau no. rangka..."
                icon="magnifying-glass"
            />
        </div>
        <div class="w-full sm:w-44">
            <flux:select wire:model.live="status" placeholder="Semua Status">
                <option value="">Semua Status</option>
                <option value="siap-jual">Siap Jual</option>
                <option value="perbaikan">Perbaikan</option>
                <option value="sewa">Sewa</option>
                <option value="tahan">Tahan</option>
            </flux:select>
        </div>
    </div>

    {{-- Table --}}
    <div class="mt-4 overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tgl Beli</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Usia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">No. Polisi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tahun</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Warna</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">KM</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Harga Beli</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Biaya</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Gudang</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Titip</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse ($this->units as $item)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                            {{ $this->units->firstItem() + $loop->index }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->tgl_beli?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if($item->tgl_beli)
                                @php
                                    $diff = $item->tgl_beli->diff(now());
                                    $totalBulan = $diff->y * 12 + $diff->m;
                                @endphp
                                <span class="{{ $totalBulan >= 3 ? 'font-semibold text-red-600 dark:text-red-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                                    @if($totalBulan > 0)
                                        {{ $totalBulan }} bln {{ $diff->d }} hr
                                    @else
                                        {{ $diff->d }} hari
                                    @endif
                                </span>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            <span class="font-medium">{{ $item->masterbarang?->nama_barang ?? '-' }}</span>
                            @if($item->masterbarang?->merek || $item->masterbarang?->tipe)
                                <br>
                                <span class="text-xs text-zinc-400">
                                    {{ implode(' · ', array_filter([
                                        $item->masterbarang->merek?->nama,
                                        $item->masterbarang->tipe?->nama,
                                    ])) }}
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-zinc-700 dark:text-zinc-300">
                            {{ $item->unitdetail?->no_polisi ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->unitdetail?->tahun ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->unitdetail?->warna ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->unitdetail?->km ? number_format($item->unitdetail->km, 0, ',', '.') . ' km' : '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 font-medium text-zinc-800 dark:text-zinc-200">
                            Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            Rp {{ number_format($item->biaya, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($item->status) {
                                    'siap-jual'  => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                                    'perbaikan'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400',
                                    'sewa'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                                    'tahan'      => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                                    default      => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                                {{ ucfirst(str_replace('-', ' ', $item->status ?? '-')) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            {{ $item->gudang?->nama ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                            @if($item->unit_titip)
                                <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-700 dark:bg-orange-900/40 dark:text-orange-400">Ya</span>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Belum ada data stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($this->units->hasPages())
        <div class="mt-4">
            {{ $this->units->links() }}
        </div>
    @endif
</div>
