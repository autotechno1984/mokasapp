<?php

use App\Models\Gudang;
use App\Models\Masterbarang;
use App\Models\Unit;
use Livewire\Component;

new
#[\Livewire\Attributes\Layout('layouts.app')]
#[\Livewire\Attributes\Title('Tambah Unit')]
class extends Component
{
    public $masterbarang_id = '';
    public $gudang_id = '';
    public $tgl_beli = '';
    public $harga_beli = '';
    public $status = 'siap-jual';
    public $unit_titip = false;

    // Search
    public $masterbarangOptions = [];
    public $gudangOptions = [];
    public $selectedMasterbarangName = '';
    public $selectedGudangName = '';

    // Modal tambah gudang
    public $showGudangModal = false;
    public $gudang_nama = '';
    public $gudang_alamat = '';
    public $gudangCount = 0;

    public function mount(): void
    {
        $this->masterbarangOptions = Masterbarang::where('isactive', true)
            ->with(['merek', 'model', 'tipe'])
            ->limit(20)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'nama' => $m->nama_barang,
                'detail' => implode(' · ', array_filter([
                    $m->merek?->nama,
                    $m->model?->nama,
                    $m->tipe?->nama,
                ])),
            ])
            ->toArray();

        $this->loadGudangOptions();
    }

    private function loadGudangOptions(): void
    {
        $this->gudangCount = Gudang::where('tenant_id', auth()->user()->tenant_id)->count();

        $this->gudangOptions = Gudang::where('tenant_id', auth()->user()->tenant_id)
            ->limit(20)
            ->get()
            ->map(fn ($g) => [
                'id' => $g->id,
                'nama' => $g->nama,
            ])
            ->toArray();
    }

    public function openGudangModal(): void
    {
        if ($this->gudangCount >= 3) {
            return;
        }
        $this->gudang_nama = '';
        $this->gudang_alamat = '';
        $this->showGudangModal = true;
    }

    public function saveGudang(): void
    {
        if ($this->gudangCount >= 3) {
            $this->showGudangModal = false;
            return;
        }

        $this->validate([
            'gudang_nama'   => 'required|string|max:100',
            'gudang_alamat' => 'required|string|max:255',
        ], [], [
            'gudang_nama'   => 'Nama Gudang',
            'gudang_alamat' => 'Alamat Gudang',
        ]);

        $gudang = Gudang::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id'   => auth()->id(),
            'nama'      => $this->gudang_nama,
            'alamat'    => $this->gudang_alamat,
        ]);

        $this->showGudangModal = false;
        $this->loadGudangOptions();

        // Auto-pilih gudang yang baru dibuat
        $this->gudang_id = $gudang->id;
        $this->selectedGudangName = $gudang->nama;
    }

    public function searchMasterbarang($query): void
    {
        $this->masterbarangOptions = Masterbarang::where('isactive', true)
            ->where('nama_barang', 'like', "%{$query}%")
            ->with(['merek', 'model', 'tipe'])
            ->limit(20)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'nama' => $m->nama_barang,
                'detail' => implode(' · ', array_filter([
                    $m->merek?->nama,
                    $m->model?->nama,
                    $m->tipe?->nama,
                ])),
            ])
            ->toArray();
    }

    public function searchGudang($query): void
    {
        $this->gudangOptions = Gudang::where('tenant_id', auth()->user()->tenant_id)
            ->where('nama', 'like', "%{$query}%")
            ->limit(20)
            ->get()
            ->map(fn ($g) => [
                'id' => $g->id,
                'nama' => $g->nama,
            ])
            ->toArray();
    }



    public function save(): void
    {
        $this->validate([
            'masterbarang_id' => 'required|exists:masterbarangs,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'tgl_beli' => 'required|date',
            'harga_beli' => 'required|numeric|min:0',
            'status' => 'required|in:siap-jual,perbaikan,sewa,tahan',
            'unit_titip' => 'boolean',
        ]);

        Unit::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'masterbarang_id' => $this->masterbarang_id,
            'gudang_id' => $this->gudang_id,
            'tgl_beli' => $this->tgl_beli,
            'harga_beli' => $this->harga_beli,
            'biaya' => 0,
            'status' => $this->status,
            'unit_titip' => $this->unit_titip,
        ]);

        session()->flash('success', 'Unit berhasil ditambahkan.');
        $this->redirect(route('dashboard'), navigate: true);
    }
};
?>

<div class="">
    {{-- Header --}}

    <div>
        <flux:heading size="xl">Tambah Unit</flux:heading>
        <flux:subheading>Isi data unit baru.</flux:subheading>
    </div>
    <div class="my-2 flex items-center gap-2">
        <flux:button
            variant="primary"
            wire:click="openGudangModal"
            :disabled="$gudangCount >= 3"
        >+ Gudang</flux:button>
        @if($gudangCount >= 3)
            <span class="text-xs text-amber-600 dark:text-amber-400">Maksimal 3 gudang tercapai.</span>
        @else
            <span class="text-xs text-zinc-400">{{ $gudangCount }}/3 gudang</span>
        @endif
    </div>
    <form wire:submit="save" class="mt-6 max-w-xl space-y-6">
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Data Unit</p>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Informasi utama unit kendaraan.</p>

            <div class="mt-4 flex flex-col gap-4">

                {{-- Masterbarang searchable select --}}
                <div
                    x-data="{
                        open: false,
                        search: '',
                        select(id, nama) {
                            $wire.set('masterbarang_id', id);
                            $wire.set('selectedMasterbarangName', nama);
                            this.search = '';
                            this.open = false;
                        },
                        doSearch() {
                            $wire.call('searchMasterbarang', this.search);
                        }
                    }"
                    @click.outside="open = false"
                    class="relative"
                >
                    <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Barang <span class="text-red-500">*</span></label>
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex w-full items-center justify-between rounded-lg border border-zinc-300 bg-white px-3 py-2 text-left text-sm shadow-sm transition hover:border-zinc-400 focus:border-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-zinc-500"
                    >
                        <span x-text="$wire.selectedMasterbarangName || 'Pilih barang...'" :class="$wire.selectedMasterbarangName ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500'"></span>
                        <svg class="h-4 w-4 text-zinc-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                    </button>

                    <div
                        x-show="open"
                        x-transition.opacity.duration.150ms
                        class="absolute z-50 mt-1 w-full rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800"
                    >
                        <div class="border-b border-zinc-200 p-2 dark:border-zinc-700">
                            <input
                                x-model.debounce.300ms="search"
                                @input="doSearch()"
                                type="text"
                                placeholder="Cari barang..."
                                class="w-full rounded-md border border-zinc-300 bg-white px-3 py-1.5 text-sm focus:border-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200"
                                @click.stop
                            />
                        </div>
                        <ul class="max-h-48 overflow-y-auto py-1">
                            @foreach($masterbarangOptions as $opt)
                                <li>
                                    <button
                                        type="button"
                                        @click="select('{{ $opt['id'] }}', '{{ addslashes($opt['nama']) }}')"
                                        class="flex w-full flex-col px-3 py-2 text-left text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700"
                                        :class="$wire.masterbarang_id == '{{ $opt['id'] }}' ? 'bg-zinc-50 dark:bg-zinc-700/50' : ''"
                                    >
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $opt['nama'] }}</span>
                                        @if($opt['detail'])
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $opt['detail'] }}</span>
                                        @endif
                                    </button>
                                </li>
                            @endforeach
                            @if(empty($masterbarangOptions))
                                <li class="px-3 py-2 text-sm text-zinc-400">Tidak ditemukan</li>
                            @endif
                        </ul>
                    </div>
                    @error('masterbarang_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Gudang searchable select --}}
                <div
                    x-data="{
                        open: false,
                        search: '',
                        select(id, nama) {
                            $wire.set('gudang_id', id);
                            $wire.set('selectedGudangName', nama);
                            this.search = '';
                            this.open = false;
                        },
                        doSearch() {
                            $wire.call('searchGudang', this.search);
                        }
                    }"
                    @click.outside="open = false"
                    class="relative"
                >
                    <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Gudang <span class="text-red-500">*</span></label>
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex w-full items-center justify-between rounded-lg border border-zinc-300 bg-white px-3 py-2 text-left text-sm shadow-sm transition hover:border-zinc-400 focus:border-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-zinc-500"
                    >
                        <span x-text="$wire.selectedGudangName || 'Pilih gudang...'" :class="$wire.selectedGudangName ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500'"></span>
                        <svg class="h-4 w-4 text-zinc-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                    </button>

                    <div
                        x-show="open"
                        x-transition.opacity.duration.150ms
                        class="absolute z-50 mt-1 w-full rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800"
                    >
                        <div class="border-b border-zinc-200 p-2 dark:border-zinc-700">
                            <input
                                x-model.debounce.300ms="search"
                                @input="doSearch()"
                                type="text"
                                placeholder="Cari gudang..."
                                class="w-full rounded-md border border-zinc-300 bg-white px-3 py-1.5 text-sm focus:border-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200"
                                @click.stop
                            />
                        </div>
                        <ul class="max-h-48 overflow-y-auto py-1">
                            @foreach($gudangOptions as $opt)
                                <li>
                                    <button
                                        type="button"
                                        @click="select('{{ $opt['id'] }}', '{{ addslashes($opt['nama']) }}')"
                                        class="w-full px-3 py-2 text-left text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700"
                                        :class="$wire.gudang_id == '{{ $opt['id'] }}' ? 'bg-zinc-50 dark:bg-zinc-700/50' : ''"
                                    >
                                        <span class="text-zinc-900 dark:text-zinc-100">{{ $opt['nama'] }}</span>
                                    </button>
                                </li>
                            @endforeach
                            @if(empty($gudangOptions))
                                <li class="px-3 py-2 text-sm text-zinc-400">Tidak ditemukan</li>
                            @endif
                        </ul>
                    </div>
                    @error('gudang_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input
                        wire:model="tgl_beli"
                        label="Tanggal Beli"
                        type="date"
                        required
                    />

                    <div>
                        <flux:input
                            wire:model="harga_beli"
                            label="Harga Beli"
                            type="text"
                            required
                            placeholder="0"
                            x-data
                            x-on:input="
                                let v = $el.value.replace(/\D/g, '');
                                $el.value = new Intl.NumberFormat('id-ID').format(v);
                                $wire.set('harga_beli', v);
                            "
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:select wire:model="status" label="Status" required>
                        <option value="siap-jual">Siap Jual</option>
                        <option value="perbaikan">Perbaikan</option>
                        <option value="sewa">Sewa</option>
                        <option value="tahan">Tahan</option>
                    </flux:select>

                    <div class="flex items-end pb-2">
                        <flux:checkbox wire:model="unit_titip" label="Unit Titipan" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <flux:button variant="ghost" :href="route('dashboard')" wire:navigate>Batal</flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan Unit</span>
                <span wire:loading>Menyimpan...</span>
            </flux:button>
        </div>
    </form>

    {{-- Modal Tambah Gudang --}}
    @if($showGudangModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            x-data
            @keydown.escape.window="$wire.set('showGudangModal', false)"
        >
            <div
                class="w-full max-w-md rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
                @click.stop
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Tambah Gudang</p>
                        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $gudangCount }}/3 gudang digunakan</p>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('showGudangModal', false)"
                        class="rounded-md p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-300"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="space-y-4 px-5 py-5">
                    <div>
                        <flux:input
                            wire:model="gudang_nama"
                            label="Nama Gudang"
                            placeholder="cth. Gudang Utama"
                            required
                        />
                        @error('gudang_nama') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="gudang_alamat"
                            label="Alamat Gudang"
                            placeholder="cth. Jl. Merdeka No. 1"
                            required
                        />
                        @error('gudang_alamat') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-zinc-200 px-5 py-4 dark:border-zinc-700">
                    <flux:button variant="ghost" wire:click="$set('showGudangModal', false)">Batal</flux:button>
                    <flux:button variant="primary" wire:click="saveGudang" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveGudang">Simpan Gudang</span>
                        <span wire:loading wire:target="saveGudang">Menyimpan...</span>
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
