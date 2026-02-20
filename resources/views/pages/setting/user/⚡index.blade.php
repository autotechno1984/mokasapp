<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

new
#[\Livewire\Attributes\Layout('layouts.app')]
#[\Livewire\Attributes\Title('Manajemen User')]
class extends Component
{
    public bool $showForm = false;

    public string $name     = '';
    public string $email    = '';
    public string $password = '';

    public ?int $deleteId = null;

    #[Computed]
    public function tenant()
    {
        return auth()->user()->tenant->load('plan');
    }

    #[Computed]
    public function maxUser(): int
    {
        return $this->tenant->plan?->max_user ?? 2;
    }

    #[Computed]
    public function users()
    {
        return User::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at')
            ->get();
    }

    #[Computed]
    public function userCount(): int
    {
        return $this->users->count();
    }

    #[Computed]
    public function canAddUser(): bool
    {
        return $this->userCount < $this->maxUser;
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        if (! $this->canAddUser) {
            $this->addError('email', 'Batas maksimal user (' . $this->maxUser . ') sudah tercapai.');
            return;
        }

        $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
        ]);

        User::create([
            'name'      => $this->name,
            'email'     => $this->email,
            'password'  => Hash::make($this->password),
            'tenant_id' => auth()->user()->tenant_id,
            'role'      => 'viewer',
        ]);

        $this->showForm = false;
        $this->resetForm();
        unset($this->users, $this->userCount, $this->canAddUser);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
    }

    public function destroy(): void
    {
        if (! $this->deleteId) {
            return;
        }

        $user = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', $this->deleteId)
            ->firstOrFail();

        if ($user->id === auth()->id()) {
            $this->deleteId = null;
            return;
        }

        $user->delete();
        $this->deleteId = null;
        unset($this->users, $this->userCount, $this->canAddUser);
    }

    private function resetForm(): void
    {
        $this->name     = '';
        $this->email    = '';
        $this->password = '';
        $this->resetErrorBag();
    }
};
?>

<div>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Manajemen User</flux:heading>
            <flux:subheading>Kelola akun yang bisa mengakses tenant ini.</flux:subheading>
        </div>
        @if($this->canAddUser)
            <flux:button icon="plus" wire:click="openForm">
                Tambah User
            </flux:button>
        @endif
    </div>

    {{-- Kuota --}}
    <div class="mt-4 flex items-center gap-3 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex-1">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Kuota User</p>
            <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                @php $pct = $this->maxUser > 0 ? min(($this->userCount / $this->maxUser) * 100, 100) : 100; @endphp
                <div
                    class="h-2 rounded-full transition-all {{ $pct >= 100 ? 'bg-red-500' : 'bg-blue-500' }}"
                    style="width: {{ $pct }}%"
                ></div>
            </div>
        </div>
        <div class="text-right">
            <span class="text-lg font-bold text-zinc-800 dark:text-zinc-100">{{ $this->userCount }}</span>
            <span class="text-sm text-zinc-400"> / {{ $this->maxUser }}</span>
        </div>
        @if(! $this->canAddUser)
            <flux:badge color="red">Kuota penuh</flux:badge>
        @endif
    </div>

    {{-- Form Tambah User --}}
    @if($showForm)
        <div class="mt-4 rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Tambah User Viewer</flux:heading>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <flux:label>Nama</flux:label>
                    <flux:input wire:model="name" placeholder="Nama lengkap" />
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:label>Email</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="email@contoh.com" />
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:label>Password</flux:label>
                    <flux:input wire:model="password" type="password" placeholder="Min. 8 karakter" />
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <flux:button variant="primary" wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="cancelForm">Batal</flux:button>
            </div>
        </div>
    @endif

    {{-- Tabel User --}}
    <div class="mt-4 overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Dibuat</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse($this->users as $user)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3 font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <flux:badge color="blue" size="sm" class="ml-1">Anda</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if($user->role === 'owner')
                                <flux:badge color="amber">Owner</flux:badge>
                            @else
                                <flux:badge color="zinc">Viewer</flux:badge>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-500 dark:text-zinc-400">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($user->id !== auth()->id())
                                @if($deleteId === $user->id)
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-xs text-zinc-500">Hapus user ini?</span>
                                        <flux:button size="sm" variant="danger" wire:click="destroy">Ya, hapus</flux:button>
                                        <flux:button size="sm" variant="ghost" wire:click="cancelDelete">Batal</flux:button>
                                    </div>
                                @else
                                    <flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $user->id }})">
                                        Hapus
                                    </flux:button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Belum ada user lain.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
