<?php

use App\Models\Unit;
use App\Models\Unitbiaya;
use App\Models\Unitdetail;
use App\Models\Gambar;
use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

new class extends Component
{
    use WithFileUploads, WithPagination;

    // Filter
    public string $search = '';
    public string $filterStatus = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    // Share modal
    public bool $showShareModal = false;
    public string $shareUrl = '';

    // Biaya modal
    public bool $showBiayaModal = false;
    public ?int $biayaUnitId = null;
    public string $biayaKategori = 'perbaikan';
    public string $biayaKeterangan = '';
    public string $biayaAmount = '';

    // Gambar modal
    public bool $showGambarModal = false;
    public ?int $gambarUnitId = null;
    public $photos = [];

    // Detail modal
    public bool $showDetailModal = false;
    public ?int $detailUnitId = null;
    public string $detailNoPolisi = '';
    public string $detailNoMesin = '';
    public string $detailNoRangka = '';
    public string $detailTahun = '';
    public string $detailKm = '';
    public string $detailWarna = '';
    public string $detailNamaBpkb = '';
    public string $detailAlamatBpkb = '';
    public string $detailNoBpkb = '';
    public string $detailMasaPajak = '';
    public string $detailMasaStnk = '';

    // Penjualan modal
    public bool $showPenjualanModal = false;
    public ?int $penjualanUnitId = null;
    public string $penjualanNamaKonsumen = '';
    public string $penjualanAlamat = '';
    public string $penjualanKontak = '';
    public string $penjualanHargaJual = '';
    public string $penjualanStatusPembelian = 'cash';
    public string $penjualanLeasing = '';
    public string $penjualanCatatan = '';
    public string $penjualanTglJual = '';

    // Harga Jual modal
    public bool $showHargaJualModal = false;
    public ?int $hargaJualUnitId = null;
    public string $hargaJualValue = '';

    // Confirm modal
    public bool $showConfirmModal = false;
    public ?int $confirmUnitId = null;
    public string $confirmAction = '';
    public string $confirmMessage = '';

    protected function baseQuery()
    {
        $user = auth()->user();
        if (! $user) {
            return Unit::query()->whereRaw('1 = 0');
        }

        return Unit::query()
            ->where('tenant_id', $user->tenant_id)
            ->whereNull('tgl_jual')
            ->when($this->search, fn($q) => $q->whereHas('masterbarang', fn($q2) =>
                $q2->where('nama_barang', 'like', '%' . $this->search . '%')
            ))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->with(['masterbarang.merek', 'masterbarang.tipe', 'unitdetail', 'unitbiayas', 'gambars']);
    }

    public function getUnitsProperty()
    {
        return $this->baseQuery()->latest()->paginate(10);
    }

    public function getStockCompositionProperty()
    {
        $units = Unit::where('tenant_id', auth()->user()->tenant_id)
            ->whereNull('tgl_jual')
            ->with('masterbarang:id,nama_barang')
            ->get(['id', 'masterbarang_id']);

        $total = $units->count();

        return $units
            ->groupBy(fn($u) => $u->masterbarang?->nama_barang ?? '-')
            ->map(fn($group, $nama) => [
                'nama'   => $nama,
                'jumlah' => $group->count(),
                'persen' => $total > 0 ? round($group->count() / $total * 100, 1) : 0,
            ])
            ->sortByDesc('jumlah')
            ->values();
    }

    // --- Biaya ---
    public function openBiaya(int $unitId)
    {
        $this->biayaUnitId = $unitId;
        $this->biayaKategori = 'perbaikan';
        $this->biayaKeterangan = '';
        $this->biayaAmount = '';
        $this->showBiayaModal = true;
    }

    public function saveBiaya()
    {
        $this->validate([
            'biayaKategori' => 'required|string',
            'biayaKeterangan' => 'required|string|max:250',
            'biayaAmount' => 'required|numeric|min:1',
        ]);

        $unit = Unit::find($this->biayaUnitId);
        if (! $unit) return;

        Unitbiaya::create([
            'unit_id' => $unit->id,
            'kategori' => $this->biayaKategori,
            'keterangan' => $this->biayaKeterangan,
            'amount' => $this->biayaAmount,
        ]);

        // Update total biaya di unit
        $unit->update([
            'biaya' => $unit->unitbiayas()->sum('amount'),
        ]);

        $this->showBiayaModal = false;
        $this->reset(['biayaUnitId', 'biayaKategori', 'biayaKeterangan', 'biayaAmount']);
    }

    // --- Gambar ---
    public function openGambar(int $unitId)
    {
        $this->gambarUnitId = $unitId;
        $this->photos = [];
        $this->showGambarModal = true;
    }

    public function saveGambar()
    {
        $this->validate([
            'photos.*' => 'image|max:2048',
        ]);

        $unit = Unit::find($this->gambarUnitId);
        if (! $unit) return;

        foreach ($this->photos as $photo) {
            $path = $photo->store('unit-images', 'public');
            Gambar::create([
                'tenant_id' => $unit->tenant_id,
                'unit_id' => $unit->id,
                'path' => $path,
            ]);
        }

        $this->showGambarModal = false;
        $this->reset(['gambarUnitId', 'photos']);
    }

    public function deleteGambar(int $gambarId)
    {
        $gambar = Gambar::find($gambarId);
        if ($gambar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($gambar->path);
            $gambar->delete();
        }
    }

    // --- Detail ---
    public function openDetail(int $unitId)
    {
        $this->detailUnitId = $unitId;
        $detail = Unitdetail::where('unit_id', $unitId)->first();

        $this->detailNoPolisi = $detail?->no_polisi ?? '';
        $this->detailNoMesin = $detail?->no_mesin ?? '';
        $this->detailNoRangka = $detail?->no_rangka ?? '';
        $this->detailTahun = (string) ($detail?->tahun ?? '');
        $this->detailKm = (string) ($detail?->km ?? '');
        $this->detailWarna = $detail?->warna ?? '';
        $this->detailNamaBpkb = $detail?->nama_bpkb ?? '';
        $this->detailAlamatBpkb = $detail?->alamat_bpkb ?? '';
        $this->detailNoBpkb = $detail?->no_bpkb ?? '';
        $this->detailMasaPajak = $detail?->masa_berlaku_pajak?->format('Y-m-d') ?? '';
        $this->detailMasaStnk = $detail?->masa_berlaku_stnk?->format('Y-m-d') ?? '';

        $this->showDetailModal = true;
    }

    public function saveDetail()
    {
        $this->validate([
            'detailNoPolisi' => 'required|string|max:20',
            'detailNoMesin' => 'required|string|max:50',
            'detailNoRangka' => 'required|string|max:50',
            'detailTahun' => 'required|integer|min:1900|max:2099',
            'detailKm' => 'required|integer|min:0',
            'detailWarna' => 'required|string|max:50',
            'detailMasaPajak' => 'required|date',
            'detailMasaStnk' => 'required|date',
        ]);

        $unit = Unit::find($this->detailUnitId);
        if (! $unit) return;

        Unitdetail::updateOrCreate(
            ['unit_id' => $unit->id],
            [
                'no_polisi' => $this->detailNoPolisi,
                'no_mesin' => $this->detailNoMesin,
                'no_rangka' => $this->detailNoRangka,
                'tahun' => $this->detailTahun,
                'km' => $this->detailKm,
                'warna' => $this->detailWarna,
                'nama_bpkb' => $this->detailNamaBpkb ?: null,
                'alamat_bpkb' => $this->detailAlamatBpkb ?: null,
                'no_bpkb' => $this->detailNoBpkb ?: null,
                'masa_berlaku_pajak' => $this->detailMasaPajak,
                'masa_berlaku_stnk' => $this->detailMasaStnk,
            ]
        );

        $this->showDetailModal = false;
        $this->reset(['detailUnitId', 'detailNoPolisi', 'detailNoMesin', 'detailNoRangka', 'detailTahun', 'detailKm', 'detailWarna', 'detailNamaBpkb', 'detailAlamatBpkb', 'detailNoBpkb', 'detailMasaPajak', 'detailMasaStnk']);
    }

    // --- Penjualan ---
    public function openPenjualan(int $unitId)
    {
        $this->penjualanUnitId = $unitId;
        $this->penjualanNamaKonsumen = '';
        $this->penjualanAlamat = '';
        $this->penjualanKontak = '';
        $this->penjualanHargaJual = '';
        $this->penjualanStatusPembelian = 'cash';
        $this->penjualanLeasing = '';
        $this->penjualanCatatan = '';
        $this->penjualanTglJual = now()->format('Y-m-d\TH:i');
        $this->showPenjualanModal = true;
    }

    public function savePenjualan()
    {
        $this->validate([
            'penjualanNamaKonsumen' => 'required|string|max:255',
            'penjualanHargaJual' => 'required|numeric|min:1',
            'penjualanStatusPembelian' => 'required|in:cash,kredit,cash-bertahap',
            'penjualanTglJual' => 'required|date',
            'penjualanAlamat' => 'nullable|string|max:255',
            'penjualanKontak' => 'nullable|string|max:50',
            'penjualanLeasing' => 'nullable|string|max:255',
            'penjualanCatatan' => 'nullable|string|max:500',
        ]);

        $unit = Unit::find($this->penjualanUnitId);
        if (! $unit) return;

        Penjualan::create([
            'tenant_id' => $unit->tenant_id,
            'unit_id' => $unit->id,
            'tgl_jual' => $this->penjualanTglJual,
            'nama_konsumen' => $this->penjualanNamaKonsumen,
            'alamat' => $this->penjualanAlamat ?: null,
            'kontak' => $this->penjualanKontak ?: null,
            'harga_jual' => $this->penjualanHargaJual,
            'status_pembelian' => $this->penjualanStatusPembelian,
            'leasing' => $this->penjualanLeasing ?: null,
            'catatan' => $this->penjualanCatatan ?: null,
        ]);

        $unit->update([
            'tgl_jual' => $this->penjualanTglJual,
            'status'   => 'terjual',
        ]);

        $this->showPenjualanModal = false;
        $this->reset(['penjualanUnitId', 'penjualanNamaKonsumen', 'penjualanAlamat', 'penjualanKontak', 'penjualanHargaJual', 'penjualanStatusPembelian', 'penjualanLeasing', 'penjualanCatatan', 'penjualanTglJual']);
    }

    // --- Toggle Status ---
    public function confirmToggle(int $unitId, string $action)
    {
        $this->confirmUnitId = $unitId;
        $this->confirmAction = $action;

        $this->confirmMessage = match($action) {
            'perbaikan' => 'Ubah status unit ini ke perbaikan?',
            'siap-jual' => 'Ubah status unit ini ke siap jual?',
            default => 'Yakin ingin mengubah status?',
        };

        $this->showConfirmModal = true;
    }

    public function executeToggle()
    {
        $unit = Unit::find($this->confirmUnitId);
        if (! $unit) return;

        match($this->confirmAction) {
            'perbaikan' => $unit->update(['status' => 'perbaikan']),
            'siap-jual' => $unit->update(['status' => 'siap-jual']),
            default => null,
        };

        $this->showConfirmModal = false;
        $this->reset(['confirmUnitId', 'confirmAction', 'confirmMessage']);
    }

    // --- Harga Jual ---
    public function openHargaJual(int $unitId)
    {
        $unit = Unit::find($unitId);
        if (! $unit) return;

        $this->hargaJualUnitId = $unitId;
        $this->hargaJualValue = $unit->harga_jual ? (string) $unit->harga_jual : '';
        $this->showHargaJualModal = true;
    }

    public function saveHargaJual()
    {
        $this->validate([
            'hargaJualValue' => 'required|numeric|min:1',
        ]);

        $unit = Unit::find($this->hargaJualUnitId);
        if (! $unit) return;

        $unit->update(['harga_jual' => $this->hargaJualValue]);

        $this->showHargaJualModal = false;
        $this->reset(['hargaJualUnitId', 'hargaJualValue']);
    }

    // --- Share ---
    public function generateShareLink(int $unitId)
    {
        $unit = Unit::find($unitId);
        if (! $unit) return;

        $unit->update([
            'share_token' => Str::random(16),
            'share_token_expires_at' => now()->addMinutes(30),
        ]);

        $this->shareUrl = url('/share/' . $unit->share_token);
        $this->showShareModal = true;
    }
};
?>

<div>
    <div class="mb-4 flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama kendaraan..." icon="magnifying-glass" />
        </div>
        <div class="flex gap-2">
            <flux:button size="sm" wire:click="$set('filterStatus', '')"
                variant="{{ $filterStatus === '' ? 'primary' : 'subtle' }}">
                Semua
            </flux:button>
            <flux:button size="sm" wire:click="$set('filterStatus', 'perbaikan')"
                variant="{{ $filterStatus === 'perbaikan' ? 'primary' : 'subtle' }}">
                Perbaikan
            </flux:button>
            <flux:button size="sm" wire:click="$set('filterStatus', 'siap-jual')"
                variant="{{ $filterStatus === 'siap-jual' ? 'primary' : 'subtle' }}">
                Siap Jual
            </flux:button>
        </div>
    </div>

    {{-- Komposisi Stok --}}
    @if($this->stockComposition->count() > 0)
        <div class="mt-4 w-full sm:w-1/3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500 mb-2">Komposisi Stok</p>
            <div class="space-y-1.5">
                @foreach($this->stockComposition as $item)
                    <div class="flex items-center gap-2">
                        <span class="w-28 shrink-0 truncate text-xs text-zinc-600 dark:text-zinc-400" title="{{ $item['nama'] }}">
                            {{ $item['nama'] }}
                        </span>

                        <span class="w-10 shrink-0 text-right text-xs text-zinc-500 dark:text-zinc-400">{{ $item['jumlah'] }} Unit</span>
                        <span class="w-8 shrink-0 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $item['persen'] }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="space-y-4 mt-2">
        @forelse($this->units as $unit)
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 overflow-hidden" wire:key="unit-{{ $unit->id }}">
                <div class="flex flex-col lg:flex-row">
                    {{-- Gambar --}}
                    <div class="lg:w-64 w-full flex-shrink-0 bg-zinc-100 dark:bg-zinc-800">
                        @if($unit->gambars->count() > 0)
                            @php
                                $gambarUrls = $unit->gambars->map(fn($g) => Storage::disk('public')->url($g->path))->values()->toArray();
                            @endphp
                            <div x-data="{ active: 0, images: {{ Js::from($gambarUrls) }} }">
                                {{-- Gambar utama --}}
                                <div class="w-full h-44 overflow-hidden cursor-pointer">
                                    <img :src="images[active]"
                                         alt="{{ $unit->masterbarang?->nama_barang }}"
                                         class="w-full h-full object-cover transition-all duration-200" />
                                </div>
                                {{-- 4 thumbnail sisi motor --}}
                                <div class="grid grid-cols-4 gap-0.5">
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="h-14 overflow-hidden bg-zinc-200 dark:bg-zinc-700 cursor-pointer"
                                             @if($i < count($gambarUrls))
                                                 x-on:click="active = {{ $i }}"
                                                 :class="active === {{ $i }} ? 'ring-2 ring-blue-500' : 'opacity-70 hover:opacity-100'"
                                             @endif>
                                            @if($i < count($gambarUrls))
                                                <img src="{{ $gambarUrls[$i] }}"
                                                     alt="Sisi {{ $i + 1 }}"
                                                     class="w-full h-full object-cover" />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500">
                                                    <flux:icon.photo class="size-4" />
                                                </div>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @else
                            <div class="w-full h-44 flex items-center justify-center text-zinc-400 dark:text-zinc-600">
                                <flux:icon.photo class="size-10" />
                            </div>
                            <div class="grid grid-cols-4 gap-0.5">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="h-14 bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-400 dark:text-zinc-500">
                                        <flux:icon.photo class="size-4" />
                                    </div>
                                @endfor
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div>
                                <flux:heading size="lg">
                                    {{ $unit->masterbarang?->nama_barang ?? 'Unit #' . $unit->id }}
                                </flux:heading>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    @if($unit->masterbarang?->merek)
                                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                            {{ $unit->masterbarang->merek->nama }}
                                        </span>
                                    @endif
                                    @if($unit->masterbarang?->tipe)
                                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                            {{ $unit->masterbarang->tipe->nama }}
                                        </span>
                                    @endif
                                    @if($unit->unitdetail?->tahun)
                                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                            {{ $unit->unitdetail->tahun }}
                                        </span>
                                    @endif
                                    @if($unit->unitdetail?->warna)
                                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                            {{ $unit->unitdetail->warna }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            @php
                                $statusColors = match($unit->status) {
                                    'siap-jual' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
                                    'perbaikan' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                                    'sewa' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
                                    'tahan' => 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                                    default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400',
                                };
                                $statusLabel = match($unit->status) {
                                    'siap-jual' => 'Siap Jual',
                                    'perbaikan' => 'Perbaikan',
                                    'sewa' => 'Sewa',
                                    'tahan' => 'Tahan',
                                    default => ucfirst($unit->status),
                                };
                            @endphp
                            <span class="inline-flex items-center self-start text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColors }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Detail Row --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                            @if($unit->unitdetail?->no_polisi)
                                <div>
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">No. Polisi</span>
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $unit->unitdetail->no_polisi }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">Harga Beli</span>
                                <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Rp {{ number_format($unit->harga_beli, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">Total Biaya</span>
                                <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Rp {{ number_format($unit->biaya ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">Modal</span>
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format(($unit->harga_beli ?? 0) + ($unit->biaya ?? 0), 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">Harga Jual</span>
                                @if($unit->harga_jual)
                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($unit->harga_jual, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-sm text-zinc-400 dark:text-zinc-500">Belum diset</p>
                                @endif
                            </div>
                            <div>
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">Usia Unit</span>
                                @if($unit->tgl_beli)
                                    @php
                                        $diff = $unit->tgl_beli->diff(now());
                                        $totalBulan = $diff->y * 12 + $diff->m;
                                    @endphp
                                    <p class="text-sm font-semibold {{ $totalBulan >= 3 ? 'text-red-600 dark:text-red-400' : 'text-zinc-800 dark:text-zinc-200' }}">
                                        {{ $totalBulan > 0 ? $totalBulan . ' bln ' . $diff->d . ' hr' : $diff->d . ' hari' }}
                                    </p>
                                @else
                                    <p class="text-sm text-zinc-400 dark:text-zinc-500">-</p>
                                @endif
                            </div>
                        </div>

                        {{-- Biaya list --}}
                        @if($unit->unitbiayas->count() > 0)
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach($unit->unitbiayas as $biaya)
                                    <span class="inline-flex items-center gap-1 text-xs bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-full">
                                        {{ $biaya->keterangan }}: Rp {{ number_format($biaya->amount, 0, ',', '.') }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                            <flux:button size="sm" variant="filled" icon="clipboard-document-list" wire:click="openDetail({{ $unit->id }})">
                                {{ $unit->unitdetail ? 'Edit Detail' : 'Tambah Detail' }}
                            </flux:button>
                            <flux:button size="sm" variant="filled" icon="camera" wire:click="openGambar({{ $unit->id }})">
                                Gambar
                            </flux:button>
                            <flux:button size="sm" variant="filled" icon="banknotes" wire:click="openBiaya({{ $unit->id }})">
                                Tambah Biaya
                            </flux:button>
                            <flux:button size="sm" variant="filled" icon="tag" wire:click="openHargaJual({{ $unit->id }})">
                                {{ $unit->harga_jual ? 'Edit Harga Jual' : 'Set Harga Jual' }}
                            </flux:button>

                            @if($unit->status !== 'perbaikan')
                                <flux:button size="sm" variant="subtle" icon="wrench" wire:click="confirmToggle({{ $unit->id }}, 'perbaikan')"
                                    class="text-orange-600 dark:text-orange-400">
                                    Perbaikan
                                </flux:button>
                            @else
                                <flux:button size="sm" variant="subtle" icon="check-circle" wire:click="confirmToggle({{ $unit->id }}, 'siap-jual')"
                                    class="text-emerald-600 dark:text-emerald-400">
                                    Siap Jual
                                </flux:button>
                            @endif

                            <flux:button size="sm" variant="subtle" icon="shopping-cart" wire:click="openPenjualan({{ $unit->id }})"
                                class="text-blue-600 dark:text-blue-400">
                                Terjual
                            </flux:button>

                            <flux:button size="sm" variant="subtle" icon="share" wire:click="generateShareLink({{ $unit->id }})"
                                class="text-violet-600 dark:text-violet-400">
                                Bagikan
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-neutral-300 dark:border-neutral-600 p-10 flex flex-col items-center justify-center gap-2">
                <flux:icon.cube class="size-10 text-zinc-300 dark:text-zinc-600" />
                <flux:text class="text-zinc-400 dark:text-zinc-500">Belum ada data unit kendaraan.</flux:text>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $this->units->links() }}
        </div>
    </div>

    {{-- Modal Tambah Biaya --}}
    @if($showBiayaModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showBiayaModal', false)">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 border border-neutral-200 dark:border-neutral-700">
                <flux:heading size="lg">Tambah Biaya</flux:heading>
                <div class="mt-4 space-y-4">
                    <div>
                        <flux:select wire:model="biayaKategori" label="Kategori">
                            <option value="perbaikan">Perbaikan</option>
                            <option value="pajak">Pajak</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:input wire:model="biayaKeterangan" label="Keterangan" placeholder="Contoh: Ganti oli mesin" />
                        @error('biayaKeterangan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:input wire:model="biayaAmount" label="Jumlah (Rp)" type="number" placeholder="0" />
                        @error('biayaAmount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <flux:button variant="subtle" wire:click="$set('showBiayaModal', false)">Batal</flux:button>
                        <flux:button variant="primary" wire:click="saveBiaya">Simpan</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Gambar --}}
    @if($showGambarModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showGambarModal', false)">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 border border-neutral-200 dark:border-neutral-700">
                <flux:heading size="lg">Kelola Gambar</flux:heading>

                {{-- Existing images --}}
                @php
                    $unitGambars = \App\Models\Unit::find($gambarUnitId)?->gambars ?? collect();
                @endphp
                @if($unitGambars->count() > 0)
                    <div class="grid grid-cols-3 gap-2 mt-4">
                        @foreach($unitGambars as $gambar)
                            <div class="relative group rounded-lg overflow-hidden border border-neutral-200 dark:border-neutral-700">
                                <img src="{{ Storage::disk('public')->url($gambar->path) }}" class="w-full h-24 object-cover" />
                                <button wire:click="deleteGambar({{ $gambar->id }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full size-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Upload Gambar</label>
                    <input type="file" accept="image/*" multiple
                        x-on:change="Array.from($event.target.files).forEach(f => $wire.upload('photos', f)); $event.target.value = ''"
                        class="block w-full text-sm text-zinc-500 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-400 hover:file:bg-blue-100" />
                    @error('photos.*') <span class="text-xs text-red-500">{{ $message }}</span> @enderror

                    <div wire:loading wire:target="photos" class="mt-2 text-sm text-zinc-500">
                        Mengupload...
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button variant="subtle" wire:click="$set('showGambarModal', false)">Tutup</flux:button>
                    @if(count($photos) > 0)
                        <flux:button variant="primary" wire:click="saveGambar">Simpan Gambar</flux:button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Detail Unit --}}
    @if($showDetailModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 border border-neutral-200 dark:border-neutral-700 max-h-[90vh] overflow-y-auto">
                <flux:heading size="lg">Detail Kendaraan</flux:heading>
                <div class="mt-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <flux:input wire:model="detailNoPolisi" label="No. Polisi" placeholder="B 1234 ABC" />
                            @error('detailNoPolisi') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="detailWarna" label="Warna" placeholder="Hitam" />
                            @error('detailWarna') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="detailTahun" label="Tahun" type="number" placeholder="2024" />
                            @error('detailTahun') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="detailKm" label="KM" type="number" placeholder="0" />
                            @error('detailKm') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="detailNoMesin" label="No. Mesin" placeholder="No. mesin" />
                            @error('detailNoMesin') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="detailNoRangka" label="No. Rangka" placeholder="No. rangka" />
                            @error('detailNoRangka') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <flux:separator />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <flux:input wire:model="detailNamaBpkb" label="Nama BPKB" placeholder="Opsional" />
                        </div>
                        <div class="sm:col-span-2">
                            <flux:input wire:model="detailAlamatBpkb" label="Alamat BPKB" placeholder="Opsional" />
                        </div>
                        <div>
                            <flux:input wire:model="detailNoBpkb" label="No. BPKB" placeholder="Opsional" />
                        </div>
                    </div>

                    <flux:separator />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <flux:input wire:model="detailMasaPajak" label="Masa Berlaku Pajak" type="date" />
                            @error('detailMasaPajak') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="detailMasaStnk" label="Masa Berlaku STNK" type="date" />
                            @error('detailMasaStnk') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <flux:button variant="subtle" wire:click="$set('showDetailModal', false)">Batal</flux:button>
                        <flux:button variant="primary" wire:click="saveDetail">Simpan</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Penjualan --}}
    @if($showPenjualanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showPenjualanModal', false)">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 border border-neutral-200 dark:border-neutral-700 max-h-[90vh] overflow-y-auto">
                <flux:heading size="lg">Form Penjualan</flux:heading>
                <div class="mt-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <flux:input wire:model="penjualanNamaKonsumen" label="Nama Konsumen" placeholder="Nama pembeli" />
                            @error('penjualanNamaKonsumen') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="penjualanKontak" label="Kontak" placeholder="No. HP (opsional)" />
                        </div>
                        <div>
                            <flux:input wire:model="penjualanAlamat" label="Alamat" placeholder="Alamat (opsional)" />
                        </div>
                    </div>

                    <flux:separator />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <flux:input wire:model="penjualanHargaJual" label="Harga Jual (Rp)" type="number" placeholder="0" />
                            @error('penjualanHargaJual') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:select wire:model="penjualanStatusPembelian" label="Status Pembelian">
                                <option value="cash">Cash</option>
                                <option value="kredit">Kredit</option>
                                <option value="cash-bertahap">Cash Bertahap</option>
                            </flux:select>
                            @error('penjualanStatusPembelian') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input wire:model="penjualanLeasing" label="Leasing" placeholder="Nama leasing (opsional)" />
                        </div>
                        <div>
                            <flux:input wire:model="penjualanTglJual" label="Tanggal Jual" type="datetime-local" />
                            @error('penjualanTglJual') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <flux:input wire:model="penjualanCatatan" label="Catatan" placeholder="Catatan tambahan (opsional)" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <flux:button variant="subtle" wire:click="$set('showPenjualanModal', false)">Batal</flux:button>
                        <flux:button variant="primary" wire:click="savePenjualan">Simpan Penjualan</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Harga Jual --}}
    @if($showHargaJualModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showHargaJualModal', false)">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl w-full max-w-sm mx-4 p-6 border border-neutral-200 dark:border-neutral-700">
                <flux:heading size="lg">Set Harga Jual</flux:heading>
                <div class="mt-4 space-y-4">
                    <div>
                        <flux:input wire:model="hargaJualValue" label="Harga Jual (Rp)" type="number" placeholder="0" />
                        @error('hargaJualValue') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <flux:button variant="subtle" wire:click="$set('showHargaJualModal', false)">Batal</flux:button>
                        <flux:button variant="primary" wire:click="saveHargaJual">Simpan</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showConfirmModal', false)">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl w-full max-w-sm mx-4 p-6 border border-neutral-200 dark:border-neutral-700">
                <flux:heading size="lg">Konfirmasi</flux:heading>
                <flux:text class="mt-3">{{ $confirmMessage }}</flux:text>
                <div class="flex justify-end gap-2 mt-6">
                    <flux:button variant="subtle" wire:click="$set('showConfirmModal', false)">Batal</flux:button>
                    <flux:button variant="primary" wire:click="executeToggle">Ya, Lanjutkan</flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Share Link --}}
    @if($showShareModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showShareModal', false)">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 border border-neutral-200 dark:border-neutral-700"
                 x-data="{
                     copied: false,
                     copyUrl() {
                         const input = this.$refs.shareInput;
                         input.select();
                         input.setSelectionRange(0, 99999);
                         try {
                             if (navigator.clipboard && window.isSecureContext) {
                                 navigator.clipboard.writeText(input.value);
                             } else {
                                 document.execCommand('copy');
                             }
                             this.copied = true;
                             setTimeout(() => this.copied = false, 2000);
                         } catch(e) {
                             document.execCommand('copy');
                             this.copied = true;
                             setTimeout(() => this.copied = false, 2000);
                         }
                     }
                 }">
                <flux:heading size="lg">Bagikan Unit</flux:heading>
                <flux:text class="mt-2">Salin link berikut untuk membagikan foto unit ini:</flux:text>
                <div class="mt-4 flex gap-2">
                    <input type="text" value="{{ $shareUrl }}" readonly x-ref="shareInput"
                        class="flex-1 text-sm bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-700 dark:text-zinc-300" />
                    <button
                        x-on:click="copyUrl()"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                        :class="copied ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-blue-600 text-white hover:bg-blue-700'">
                        <span x-show="!copied">Salin</span>
                        <span x-show="copied" x-cloak>Tersalin!</span>
                    </button>
                </div>
                <div class="flex justify-end mt-4">
                    <flux:button variant="subtle" wire:click="$set('showShareModal', false)">Tutup</flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
