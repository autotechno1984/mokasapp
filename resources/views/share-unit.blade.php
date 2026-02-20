<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head', ['title' => $unit->masterbarang?->nama_barang ?? 'Unit Kendaraan'])
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 min-h-screen">
    @php
        $gambarUrls = $unit->gambars->map(fn($g) => Storage::disk('public')->url($g->path))->values()->toArray();
    @endphp

    <div class="max-w-lg mx-auto py-8 px-4">

        {{-- Nama Dealer --}}
        <div class="text-center mb-4">
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                {{ $unit->tenant?->nama_tenant ?? 'Showroom' }}
            </p>
        </div>

        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-zinc-800 dark:text-zinc-100">
                {{ $unit->masterbarang?->nama_barang ?? 'Unit Kendaraan' }}
            </h1>
            <div class="flex flex-wrap items-center justify-center gap-2 mt-2">
                @if($unit->masterbarang?->merek)
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-200 dark:bg-zinc-800 px-2 py-0.5 rounded">
                        {{ $unit->masterbarang->merek->nama }}
                    </span>
                @endif
                @if($unit->masterbarang?->tipe)
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-200 dark:bg-zinc-800 px-2 py-0.5 rounded">
                        {{ $unit->masterbarang->tipe->nama }}
                    </span>
                @endif
                @if($unit->unitdetail?->tahun)
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-200 dark:bg-zinc-800 px-2 py-0.5 rounded">
                        {{ $unit->unitdetail->tahun }}
                    </span>
                @endif
                @if($unit->unitdetail?->warna)
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-200 dark:bg-zinc-800 px-2 py-0.5 rounded">
                        {{ $unit->unitdetail->warna }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Gambar --}}
        @if(count($gambarUrls) > 0)
            <div class="rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900"
                 x-data="{ active: 0, images: {{ Js::from($gambarUrls) }} }">
                {{-- Gambar utama --}}
                <div class="w-full h-72 overflow-hidden bg-zinc-100 dark:bg-zinc-800 cursor-pointer">
                    <img :src="images[active]"
                         alt="{{ $unit->masterbarang?->nama_barang }}"
                         class="w-full h-full object-cover transition-all duration-200" />
                </div>
                {{-- 4 thumbnail sisi motor --}}
                <div class="grid grid-cols-4 gap-0.5">
                    @for($i = 0; $i < 4; $i++)
                        <div class="h-16 overflow-hidden bg-zinc-200 dark:bg-zinc-700 cursor-pointer"
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-zinc-300 dark:border-zinc-600 p-16 flex flex-col items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                </svg>
                <p class="text-zinc-400 dark:text-zinc-500">Belum ada foto.</p>
            </div>
        @endif

        {{-- Info --}}
        <div class="mt-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <div class="grid grid-cols-2 gap-3">
                @if($unit->unitdetail?->no_polisi)
                    <div>
                        <span class="text-xs text-zinc-400">No. Polisi</span>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $unit->unitdetail->no_polisi }}</p>
                    </div>
                @endif
                @if($unit->unitdetail?->tahun)
                    <div>
                        <span class="text-xs text-zinc-400">Tahun</span>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $unit->unitdetail->tahun }}</p>
                    </div>
                @endif
                @if($unit->unitdetail?->warna)
                    <div>
                        <span class="text-xs text-zinc-400">Warna</span>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $unit->unitdetail->warna }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-8 text-center">
        <p class="text-xs text-zinc-400 dark:text-zinc-600">
            Powered by <span class="font-semibold text-zinc-500 dark:text-zinc-400">mokasapp</span>
        </p>
    </div>

    @fluxScripts
</body>
</html>
