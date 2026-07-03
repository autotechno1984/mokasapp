<?php

use App\Models\Kwitansi;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Layout('layouts.app')]
#[\Livewire\Attributes\Title('Kwitansi')]
class extends Component
{
    use WithPagination;

    // Filter
    public string $search = '';
    public string $filterStatus = '';
    public string $filterBulan = '';

    // Form
    public string $tanggal = '';
    public string $nama_penerima = '';
    public string $untuk_pembayaran = '';
    public string $jumlah = '';
    public string $metode = '';
    public string $catatan = '';

    public bool $showModal = false;

    public function mount(): void
    {
        $this->filterBulan = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterBulan(): void { $this->resetPage(); }

    #[Computed]
    public function kwitansis()
    {
        return Kwitansi::where('tenant_id', auth()->user()->tenant_id)
            ->when($this->search, fn ($q) => $q->where(fn ($sub) =>
                $sub->where('nomor', 'like', "%{$this->search}%")
                    ->orWhere('nama_penerima', 'like', "%{$this->search}%")
            ))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterBulan, fn ($q) =>
                $q->whereYear('tanggal', substr($this->filterBulan, 0, 4))
                  ->whereMonth('tanggal', substr($this->filterBulan, 5, 2))
            )
            ->latest('tanggal')
            ->latest('id')
            ->paginate(15);
    }

    #[Computed]
    public function totalAktif(): float
    {
        return Kwitansi::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'aktif')
            ->when($this->filterBulan, fn ($q) =>
                $q->whereYear('tanggal', substr($this->filterBulan, 0, 4))
                  ->whereMonth('tanggal', substr($this->filterBulan, 5, 2))
            )
            ->sum('jumlah');
    }

    private function generateNomor(): string
    {
        $prefix = 'KW/' . now()->format('Ym') . '/';

        $last = Kwitansi::where('tenant_id', auth()->user()->tenant_id)
            ->where('nomor', 'like', $prefix . '%')
            ->orderByDesc('nomor')
            ->value('nomor');

        $seq = $last ? ((int) substr($last, strlen($prefix)) + 1) : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->tanggal = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'tanggal'          => 'required|date',
            'nama_penerima'    => 'required|string|max:150',
            'untuk_pembayaran' => 'required|string|max:255',
            'jumlah'           => 'required|numeric|min:1',
            'metode'           => 'nullable|string|max:50',
            'catatan'          => 'nullable|string|max:255',
        ], [], [
            'tanggal'          => 'Tanggal',
            'nama_penerima'    => 'Nama penerima',
            'untuk_pembayaran' => 'Untuk pembayaran',
            'jumlah'           => 'Jumlah',
        ]);

        Kwitansi::create([
            'tenant_id'        => auth()->user()->tenant_id,
            'user_id'          => auth()->id(),
            'nomor'            => $this->generateNomor(),
            'tanggal'          => $this->tanggal,
            'nama_penerima'    => $this->nama_penerima,
            'untuk_pembayaran' => $this->untuk_pembayaran,
            'jumlah'           => $this->jumlah,
            'metode'           => $this->metode ?: null,
            'catatan'          => $this->catatan ?: null,
            'status'           => 'aktif',
        ]);

        session()->flash('success', 'Kwitansi berhasil dibuat.');
        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function batalkan(int $id): void
    {
        Kwitansi::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id)
            ->update(['status' => 'batal']);

        session()->flash('success', 'Kwitansi ditandai BATAL.');
    }

    private function resetForm(): void
    {
        $this->tanggal = '';
        $this->nama_penerima = '';
        $this->untuk_pembayaran = '';
        $this->jumlah = '';
        $this->metode = '';
        $this->catatan = '';
        $this->resetValidation();
    }
};
?>

<div>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Kwitansi</flux:heading>
            <flux:subheading>Buat & cetak bukti pembayaran (PDF).</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="openCreate">+ Buat Kwitansi</flux:button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="w-full sm:w-64">
            <flux:input wire:model.live.debounce.400ms="search" label="Cari" placeholder="Nomor / nama penerima" />
        </div>
        <div class="w-full sm:w-44">
            <flux:input wire:model.live="filterBulan" label="Bulan" type="month" />
        </div>
        <div class="w-full sm:w-40">
            <flux:select wire:model.live="filterStatus" label="Status">
                <option value="">Semua</option>
                <option value="aktif">Aktif</option>
                <option value="batal">Batal</option>
            </flux:select>
        </div>
    </div>

    {{-- Summary --}}
    <div class="mt-4 rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Penerimaan (kwitansi aktif)</p>
        <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">
            Rp {{ number_format($this->totalAktif, 0, ',', '.') }}
        </p>
    </div>

    {{-- Table --}}
    <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Nomor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Penerima</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Untuk</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Jumlah</th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                @forelse($this->kwitansis as $kw)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 {{ $kw->status === 'batal' ? 'opacity-60' : '' }}">
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $kw->nomor }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $kw->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">{{ $kw->nama_penerima }}</td>
                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ \Illuminate\Support\Str::limit($kw->untuk_pembayaran, 40) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($kw->jumlah, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($kw->status === 'batal')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">Batal</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Aktif</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" variant="ghost" :href="route('kwitansi.pdf', $kw->id)" target="_blank">Cetak</flux:button>
                                @if($kw->status !== 'batal')
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        class="text-red-500 hover:text-red-600 dark:text-red-400"
                                        wire:click="batalkan({{ $kw->id }})"
                                        wire:confirm="Batalkan kwitansi ini? Nomor tetap tersimpan dan ditandai BATAL."
                                    >Batalkan</flux:button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">Belum ada kwitansi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->kwitansis->hasPages())
            <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                {{ $this->kwitansis->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Buat Kwitansi --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="w-full max-w-md rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900" @click.stop>
                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Buat Kwitansi</p>
                        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Nomor kwitansi dibuat otomatis.</p>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="rounded-md p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-300">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="space-y-4 px-5 py-5">
                        <div>
                            <flux:input wire:model="tanggal" label="Tanggal" type="date" required />
                            @error('tanggal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="nama_penerima" label="Telah terima dari" placeholder="Nama pembayar" required />
                            @error('nama_penerima') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="untuk_pembayaran" label="Untuk pembayaran" placeholder="cth. DP unit Honda Beat 2020" required />
                            @error('untuk_pembayaran') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <flux:input
                                wire:model="jumlah"
                                label="Jumlah (Rp)"
                                type="text"
                                placeholder="0"
                                required
                                x-data
                                x-on:input="
                                    let v = $el.value.replace(/\D/g, '');
                                    $el.value = new Intl.NumberFormat('id-ID').format(v);
                                    $wire.set('jumlah', v);
                                "
                            />
                            @error('jumlah') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <flux:select wire:model="metode" label="Metode (opsional)">
                                <option value="">- Pilih -</option>
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer">Transfer</option>
                                <option value="Debit/Kartu">Debit/Kartu</option>
                            </flux:select>
                        </div>
                        <div>
                            <flux:input wire:model="catatan" label="Catatan (opsional)" placeholder="cth. sisa pelunasan menyusul" />
                            @error('catatan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-zinc-200 px-5 py-4 dark:border-zinc-700">
                        <flux:button variant="ghost" type="button" wire:click="$set('showModal', false)">Batal</flux:button>
                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Simpan</span>
                            <span wire:loading>Menyimpan...</span>
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
