<?php

use App\Models\Unit;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

new class extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';

    public bool   $showShareModal = false;
    public string $shareUrl       = '';

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }

    public function generateShareLink(int $unitId): void
    {
        $unit = Unit::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($unitId);

        $unit->update([
            'share_token'            => Str::random(16),
            'share_token_expires_at' => now()->addMinutes(30),
        ]);

        $this->shareUrl       = url('/share/' . $unit->share_token);
        $this->showShareModal = true;
    }

    protected function baseQuery()
    {
        return Unit::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereNull('tgl_jual')
            ->with(['masterbarang.merek', 'masterbarang.tipe', 'unitdetail', 'gambars']);
    }

    #[Computed]
    public function totalStock(): int
    {
        return Unit::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereNull('tgl_jual')
            ->count();
    }

    #[Computed]
    public function units()
    {
        return $this->baseQuery()
            ->when($this->search, fn($q) =>
                $q->whereHas('masterbarang', fn($q2) =>
                    $q2->where('nama_barang', 'like', '%' . $this->search . '%')
                )->orWhereHas('unitdetail', fn($q2) =>
                    $q2->where('no_polisi', 'like', '%' . $this->search . '%')
                )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(10);
    }
};
?>

<div class="space-y-6">

    {{-- Total Stok Card --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 w-fit">
        <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Stok</p>
        <p class="mt-1 text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $this->totalStock }} <span class="text-sm font-normal text-zinc-400">unit</span></p>
    </div>

    {{-- Filter --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama kendaraan atau no. polisi..."
                icon="magnifying-glass"
            />
        </div>
        <div class="flex gap-2 flex-wrap">
            @foreach(['' => 'Semua', 'siap-jual' => 'Siap Jual', 'perbaikan' => 'Perbaikan', 'sewa' => 'Sewa', 'tahan' => 'Tahan'] as $val => $label)
                <flux:button
                    size="sm"
                    wire:click="$set('filterStatus', '{{ $val }}')"
                    variant="{{ $filterStatus === $val ? 'primary' : 'subtle' }}"
                >
                    {{ $label }}
                </flux:button>
            @endforeach
        </div>
    </div>

    {{-- Daftar Unit (Read-only) --}}
    <div class="space-y-4">
        @forelse($this->units as $unit)
            @php
                $statusColors = match($unit->status) {
                    'siap-jual' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
                    'perbaikan' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                    'sewa'      => 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
                    'tahan'     => 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                    default     => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400',
                };
                $statusLabel = match($unit->status) {
                    'siap-jual' => 'Siap Jual',
                    'perbaikan' => 'Perbaikan',
                    'sewa'      => 'Sewa',
                    'tahan'     => 'Tahan',
                    default     => ucfirst($unit->status ?? '-'),
                };
            @endphp

            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 overflow-hidden"
                 wire:key="viewer-unit-{{ $unit->id }}">
                <div class="flex flex-col lg:flex-row">

                    {{-- Gambar --}}
                    <div class="lg:w-56 w-full flex-shrink-0 bg-zinc-100 dark:bg-zinc-800">
                        @if($unit->gambars->count() > 0)
                            @php
                                $gambarUrls = $unit->gambars->map(fn($g) => \Illuminate\Support\Facades\Storage::disk('public')->url($g->path))->values()->toArray();
                            @endphp
                            <div x-data="{ active: 0, images: {{ Js::from($gambarUrls) }} }">
                                <div class="w-full h-40 overflow-hidden">
                                    <img :src="images[active]"
                                         alt="{{ $unit->masterbarang?->nama_barang }}"
                                         class="w-full h-full object-cover" />
                                </div>
                                @if(count($gambarUrls) > 1)
                                    <div class="grid grid-cols-4 gap-0.5">
                                        @for($i = 0; $i < min(4, count($gambarUrls)); $i++)
                                            <div class="h-12 overflow-hidden cursor-pointer"
                                                 x-on:click="active = {{ $i }}"
                                                 :class="active === {{ $i }} ? 'ring-2 ring-blue-500' : 'opacity-60 hover:opacity-100'">
                                                <img src="{{ $gambarUrls[$i] }}" class="w-full h-full object-cover" />
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="w-full h-40 flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-600 gap-2">
                                <flux:icon.photo class="size-10" />
                                <span class="text-xs">Belum ada foto</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                            <div>
                                <flux:heading size="lg">
                                    {{ $unit->masterbarang?->nama_barang ?? 'Unit #' . $unit->id }}
                                </flux:heading>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    @foreach(array_filter([
                                        $unit->masterbarang?->merek?->nama,
                                        $unit->masterbarang?->tipe?->nama,
                                        $unit->unitdetail?->tahun ? (string)$unit->unitdetail->tahun : null,
                                        $unit->unitdetail?->warna,
                                    ]) as $tag)
                                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                            {{ $tag }}
                                        </span>
                                    @endforeach

                                    @if($unit->unit_titip)
                                        <span class="text-xs font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded">
                                            Titipan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            <span class="inline-flex items-center self-start text-xs font-semibold px-3 py-1 rounded-full {{ $statusColors }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Detail Grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                            @if($unit->unitdetail?->no_polisi)
                                <div>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">No. Polisi</p>
                                    <p class="text-sm font-semibold font-mono text-zinc-800 dark:text-zinc-200">{{ $unit->unitdetail->no_polisi }}</p>
                                </div>
                            @endif

                            @if($unit->unitdetail?->km)
                                <div>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">KM</p>
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ number_format($unit->unitdetail->km, 0, ',', '.') }} km</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Tgl Masuk</p>
                                <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                    {{ $unit->tgl_beli?->format('d/m/Y') ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Usia di Stok</p>
                                @if($unit->tgl_beli)
                                    @php
                                        $diff = $unit->tgl_beli->diff(now());
                                        $totalBulan = $diff->y * 12 + $diff->m;
                                    @endphp
                                    <p class="text-sm font-semibold {{ $totalBulan >= 3 ? 'text-red-600 dark:text-red-400' : 'text-zinc-800 dark:text-zinc-200' }}">
                                        {{ $totalBulan > 0 ? $totalBulan . ' bln ' . $diff->d . ' hr' : $diff->d . ' hari' }}
                                    </p>
                                @else
                                    <p class="text-sm text-zinc-400">-</p>
                                @endif
                            </div>

                            @if($unit->gudang)
                                <div>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Gudang</p>
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $unit->gudang->nama }}</p>
                                </div>
                            @endif

                            @if($unit->harga_jual)
                                <div>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Harga Jual</p>
                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($unit->harga_jual, 0, ',', '.') }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Aksi --}}
                        <div class="mt-4 pt-4 border-t border-neutral-100 dark:border-neutral-800">
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
                <flux:text class="text-zinc-400 dark:text-zinc-500">Tidak ada unit ditemukan.</flux:text>
            </div>
        @endforelse

        @if($this->units->hasPages())
            <div class="mt-4">
                {{ $this->units->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Share Link --}}
    @if($showShareModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             wire:click.self="$set('showShareModal', false)">
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
                <flux:text class="mt-2">Salin link berikut untuk membagikan foto unit ini. Link berlaku 30 menit.</flux:text>
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
