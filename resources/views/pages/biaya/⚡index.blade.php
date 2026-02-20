<?php

use App\Models\Biaya;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Layout('layouts.app')]
#[\Livewire\Attributes\Title('Biaya')]
class extends Component
{
    use WithPagination;

    // Filter
    public string $filterTahun = '';
    public string $filterBulan = '';

    // Form fields
    public string $kategori = '';
    public string $tanggal = '';
    public string $keterangan = '';
    public string $jumlah = '';

    // Edit state
    public ?int $editId = null;

    // Modal state
    public bool $showModal = false;

    public function mount(): void
    {
        $this->filterTahun = now()->format('Y');
        $this->filterBulan = now()->format('m');
    }

    public function updatingFilterTahun(): void
    {
        $this->resetPage();
    }

    public function updatingFilterBulan(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function biayas()
    {
        return Biaya::where('tenant_id', auth()->user()->tenant_id)
            ->when($this->filterTahun, fn ($q) => $q->whereYear('tanggal', $this->filterTahun))
            ->when($this->filterBulan, fn ($q) => $q->whereMonth('tanggal', $this->filterBulan))
            ->latest('tanggal')
            ->paginate(15);
    }

    #[Computed]
    public function total(): float
    {
        return Biaya::where('tenant_id', auth()->user()->tenant_id)
            ->when($this->filterTahun, fn ($q) => $q->whereYear('tanggal', $this->filterTahun))
            ->when($this->filterBulan, fn ($q) => $q->whereMonth('tanggal', $this->filterBulan))
            ->sum('jumlah');
    }

    #[Computed]
    public function tahunOptions(): array
    {
        $tahunPertama = Biaya::where('tenant_id', auth()->user()->tenant_id)
            ->min(\Illuminate\Support\Facades\DB::raw('YEAR(tanggal)'));

        $tahunSekarang = now()->year;
        $dari = $tahunPertama ? (int) $tahunPertama : $tahunSekarang;

        return range($tahunSekarang, $dari);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $biaya = Biaya::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $this->editId = $biaya->id;
        $this->kategori = $biaya->kategori;
        $this->tanggal = $biaya->tanggal->format('Y-m-d');
        $this->keterangan = $biaya->keterangan ?? '';
        $this->jumlah = (string) $biaya->jumlah;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'kategori'   => 'required|string|max:100',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'jumlah'     => 'required|numeric|min:0',
        ], [], [
            'kategori'   => 'Kategori',
            'tanggal'    => 'Tanggal',
            'keterangan' => 'Keterangan',
            'jumlah'     => 'Jumlah',
        ]);

        $data = [
            'tenant_id'  => auth()->user()->tenant_id,
            'kategori'   => $this->kategori,
            'tanggal'    => $this->tanggal,
            'keterangan' => $this->keterangan,
            'jumlah'     => $this->jumlah,
        ];

        if ($this->editId) {
            Biaya::where('tenant_id', auth()->user()->tenant_id)
                ->findOrFail($this->editId)
                ->update($data);
            session()->flash('success', 'Biaya berhasil diperbarui.');
        } else {
            Biaya::create($data);
            session()->flash('success', 'Biaya berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Biaya::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id)
            ->delete();

        session()->flash('success', 'Biaya berhasil dihapus.');
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->kategori = '';
        $this->tanggal = '';
        $this->keterangan = '';
        $this->jumlah = '';
        $this->resetValidation();
    }
};
?>

<div>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Biaya</flux:heading>
            <flux:subheading>Kelola catatan pengeluaran bisnis Anda.</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="openCreate">+ Tambah Biaya</flux:button>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="w-full sm:w-40">
            <flux:select wire:model.live="filterTahun" label="Tahun">
                <option value="">Semua Tahun</option>
                @foreach($this->tahunOptions as $tahun)
                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full sm:w-44">
            <flux:select wire:model.live="filterBulan" label="Bulan">
                <option value="">Semua Bulan</option>
                <option value="01">Januari</option>
                <option value="02">Februari</option>
                <option value="03">Maret</option>
                <option value="04">April</option>
                <option value="05">Mei</option>
                <option value="06">Juni</option>
                <option value="07">Juli</option>
                <option value="08">Agustus</option>
                <option value="09">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </flux:select>
        </div>
        @if($filterTahun || $filterBulan)
            <flux:button variant="ghost" wire:click="$set('filterTahun', ''); $set('filterBulan', '')">
                Reset
            </flux:button>
        @endif
    </div>

    {{-- Summary card --}}
    <div class="mt-4 rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
            Total Pengeluaran
            @if($filterBulan && $filterTahun)
                <span class="ml-1">— {{ \Carbon\Carbon::create()->month((int)$filterBulan)->translatedFormat('F') }} {{ $filterTahun }}</span>
            @elseif($filterTahun)
                <span class="ml-1">— {{ $filterTahun }}</span>
            @endif
        </p>
        <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">
            Rp {{ number_format($this->total, 0, ',', '.') }}
        </p>
    </div>

    {{-- Table --}}
    <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Keterangan</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Jumlah</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                @forelse($this->biayas as $biaya)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                            {{ $biaya->tanggal->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $biaya->kategori }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $biaya->keterangan ?: '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            Rp {{ number_format($biaya->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" variant="ghost" wire:click="openEdit({{ $biaya->id }})">Edit</flux:button>
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    class="text-red-500 hover:text-red-600 dark:text-red-400"
                                    wire:click="delete({{ $biaya->id }})"
                                    wire:confirm="Yakin ingin menghapus biaya ini?"
                                >Hapus</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">
                            Belum ada data biaya.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->biayas->hasPages())
            <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                {{ $this->biayas->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah / Edit Biaya --}}
    @if($showModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            x-data
            @keydown.escape.window="$wire.set('showModal', false)"
        >
            <div
                class="w-full max-w-md rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
                @click.stop
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $editId ? 'Edit Biaya' : 'Tambah Biaya' }}
                        </p>
                        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $editId ? 'Perbarui data pengeluaran.' : 'Catat pengeluaran baru.' }}
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-md p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-300"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form wire:submit="save">
                    <div class="space-y-4 px-5 py-5">

                        {{-- Kategori --}}
                        <div>
                            <flux:select wire:model="kategori" label="Kategori" required>
                                <option value="">Pilih kategori...</option>
                                <option value="Operasional">Operasional</option>
                                <option value="Gaji">Gaji</option>
                                <option value="Perawatan">Perawatan</option>
                                <option value="Listrik">Listrik</option>
                                <option value="Air">Air</option>
                                <option value="Sewa Tempat">Sewa Tempat</option>
                                <option value="Promosi">Promosi</option>
                                <option value="Lainnya">Lainnya</option>
                            </flux:select>
                            @error('kategori') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <flux:input
                                wire:model="tanggal"
                                label="Tanggal"
                                type="date"
                                required
                            />
                            @error('tanggal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <flux:input
                                wire:model="keterangan"
                                label="Keterangan"
                                placeholder="cth. Bayar listrik bulan ini"
                            />
                            @error('keterangan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jumlah --}}
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
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end gap-3 border-t border-zinc-200 px-5 py-4 dark:border-zinc-700">
                        <flux:button variant="ghost" type="button" wire:click="$set('showModal', false)">Batal</flux:button>
                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $editId ? 'Perbarui' : 'Simpan' }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
