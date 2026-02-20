<x-layouts::app :title="__('Dashboard')">
    <div class="flex flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl" level="1">
                Ringkasan Showroom : {{ auth()->user()?->tenant?->nama_tenant ?? 'Showroom' }}
            </flux:heading>
            <flux:subheading>Pantau performa showroom Anda hari ini.</flux:subheading>
        </div>

        @if(auth()->user()->isOwner())
            {{-- Owner: widget summary + full list dengan aksi --}}
            <livewire:lvstockwidget />

            <div>
                <flux:heading size="lg" class="mb-4">Stok Kendaraan</flux:heading>
                <livewire:lvlistkendaraan />
            </div>
        @else
            {{-- Viewer: info cards + list read-only --}}
            <div>
                <flux:heading size="lg" class="mb-4">Stok Kendaraan</flux:heading>
                <livewire:listkendaraanforviewer />
            </div>
        @endif
    </div>
</x-layouts::app>
